<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Misi;
use App\Models\Pengerjaan;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class KaryawanMisiController extends Controller
{
    public function index()
    {
        $id    = Session::get('id_karyawan');
        $today = Carbon::today();

        // Jika hari ini bukan hari kerja, tampilkan kosong
        if (!\App\Helpers\HariLiburHelper::isHariKerja($today)) {
            $pengerjaan = collect();
            return view('karyawan.misi.index', compact('pengerjaan'));
        }

        // Cek apakah karyawan sedang izin hari ini
        $sedangIzin = Absensi::where('id_karyawan', $id)
            ->whereDate('tanggal', $today)
            ->where('status', 'izin')
            ->exists();

        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_karyawan', $id)
            ->whereDate('tanggal', $today)
            ->get();

        $pengerjaan = $pengerjaan->map(function ($p) use ($sedangIzin) {
            if (!$p->misi) return $p;

            $now       = Carbon::now('Asia/Jakarta');
            $mulai     = Carbon::parse($p->misi->waktu_mulai);
            $selesai   = Carbon::parse($p->misi->waktu_selesai);
            $toleransi = $selesai->copy()->addMinutes(10);

            // Logika otomatis mengubah status jika melewati batas waktu toleransi
            // (kalau sedang izin, biarkan tetap 'belum_mengerjakan' meski sudah lewat toleransi)
            if ($p->status == 'belum_mengerjakan' && $now->gt($toleransi) && !$sedangIzin) {
                $p->status = 'tidak_mengerjakan';
            }

            // Tentukan hak akses upload bukti.
            // Kalau sedang izin, tombol "Kerjakan" dinonaktifkan meski secara waktu masih memungkinkan.
            $p->bisa_upload = !$sedangIzin
                && $now->between($mulai, $toleransi)
                && in_array($p->status, ['belum_mengerjakan', 'ditolak']);

            $p->sudah_lewat = $now->gt($toleransi);
            $p->belum_mulai = $now->lt($mulai) && $p->status == 'belum_mengerjakan';
            $p->sedang_izin = $sedangIzin;

            return $p;
        });

        $pengerjaan = $pengerjaan->sortBy(function ($p) {
            if ($p->status == 'belum_mengerjakan') {
                if ($p->bisa_upload) return 1;
                if ($p->belum_mulai) return 2;
            }
            if ($p->status == 'ditolak') return 3;
            if ($p->status == 'menunggu') return 4;
            if (in_array($p->status, ['disetujui', 'terlambat'])) return 5;
            if ($p->status == 'tidak_mengerjakan') return 6;

            return 7;
        })->values();

        return view('karyawan.misi.index', compact('pengerjaan', 'sedangIzin'));
    }

    // Detail misi + form upload
    public function show($id)
    {
        $id_karyawan = Session::get('id_karyawan');
        $today       = Carbon::today();

        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_pengerjaan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->whereDate('tanggal', $today)
            ->firstOrFail();

        $sedangIzin = Absensi::where('id_karyawan', $id_karyawan)
            ->whereDate('tanggal', $today)
            ->where('status', 'izin')
            ->exists();

        $now       = Carbon::now('Asia/Jakarta');
        $mulai     = Carbon::parse($pengerjaan->misi->waktu_mulai);
        $selesai   = Carbon::parse($pengerjaan->misi->waktu_selesai);
        $toleransi = $selesai->copy()->addMinutes(10);

        $bisaUpload = !$sedangIzin
            && $now->between($mulai, $toleransi)
            && in_array($pengerjaan->status, ['belum_mengerjakan', 'ditolak']);

        return view('karyawan.misi.detail', compact('pengerjaan', 'bisaUpload', 'selesai', 'toleransi', 'sedangIzin'));
    }

    // Upload bukti misi
    public function upload(Request $request, $id)
    {
        $id_karyawan = Session::get('id_karyawan');
        $now         = Carbon::now('Asia/Jakarta');
        $today       = Carbon::today();

        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_pengerjaan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->whereDate('tanggal', $today)
            ->firstOrFail();

        // Cegah upload kalau karyawan sedang izin hari ini
        $sedangIzin = Absensi::where('id_karyawan', $id_karyawan)
            ->whereDate('tanggal', $today)
            ->where('status', 'izin')
            ->exists();

        if ($sedangIzin) {
            return response()->json(['message' => 'Anda sedang izin hari ini, misi tidak wajib dikerjakan.'], 403);
        }

        if (!in_array($pengerjaan->status, ['belum_mengerjakan', 'ditolak'])) {
            return response()->json(['message' => 'Misi ini tidak bisa diupload ulang.'], 403);
        }

        if (!$request->has('foto') || empty($request->foto)) {
            return response()->json(['message' => 'Foto bukti wajib diambil.'], 422);
        }

        try {
            $imgData = $request->foto;

            if (str_contains($imgData, 'base64,')) {
                $imgData = explode('base64,', $imgData)[1];
            }

            $image_base64 = base64_decode($imgData);
            $fileName     = $id_karyawan . '_misi_' . $id . '_' . time() . '.png';

            $destinationPath = public_path('uploads/misi/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            file_put_contents($destinationPath . $fileName, $image_base64);

            if ($pengerjaan->foto && file_exists($destinationPath . $pengerjaan->foto)) {
                unlink($destinationPath . $pengerjaan->foto);
            }

            $pengerjaan->update([
                'foto'         => $fileName,
                'waktu_upload' => $now->toTimeString(),
                'status'       => 'menunggu',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Bukti misi berhasil diupload! Menunggu persetujuan admin.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses gambar: ' . $e->getMessage()
            ], 500);
        }
    }
}