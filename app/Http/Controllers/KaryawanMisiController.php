<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Misi;
use App\Models\Pengerjaan;
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

        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_karyawan', $id)
            ->where('tanggal', $today->toDateString())
            ->get();

        $pengerjaan = $pengerjaan->map(function ($p) {
            if (!$p->misi) return $p;

            $now       = Carbon::now();
            $mulai     = Carbon::parse($p->misi->waktu_mulai);
            $selesai   = Carbon::parse($p->misi->waktu_selesai);
            $toleransi = $selesai->copy()->addMinutes(10);

            if ($p->status == 'belum_mengerjakan' && $now->gt($toleransi)) {
                $p->status = 'tidak_mengerjakan';
            }

            // Tentukan hak akses upload bukti
            $p->bisa_upload = $now->between($mulai, $toleransi)
                && in_array($p->status, ['belum_mengerjakan', 'ditolak']);

            $p->sudah_lewat = $now->gt($toleransi);
            $p->belum_mulai = $now->lt($mulai) && $p->status == 'belum_mengerjakan';

            return $p;
        });

        // Mengurutkan item: 
        // 1. Yang bisa dikerjakan berada di PALING ATAS.
        // 2. Yang belum mulai berada di bawahnya.
        // 3. Status aktif/menunggu verifikasi/ditolak berada di tengah.
        // 4. Selesai (disetujui/terlambat) dan Tidak Mengerjakan berada di PALING BAWAH
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

        return view('karyawan.misi.index', compact('pengerjaan'));
    }

    // Detail misi + form upload
    public function show($id)
    {
        $id_karyawan = Session::get('id_karyawan');
        $today       = Carbon::today()->toDateString();

        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_pengerjaan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->where('tanggal', $today)
            ->firstOrFail();

        $now       = Carbon::now();
        $mulai     = Carbon::parse($pengerjaan->misi->waktu_mulai);
        $selesai   = Carbon::parse($pengerjaan->misi->waktu_selesai);
        $toleransi = $selesai->copy()->addMinutes(10);

        $bisaUpload = $now->between($mulai, $toleransi)
            && in_array($pengerjaan->status, ['belum_mengerjakan', 'ditolak']);

        return view('karyawan.misi.detail', compact('pengerjaan', 'bisaUpload', 'selesai', 'toleransi'));
    }

    // Upload bukti misi
    public function upload(Request $request, $id)
    {
        $id_karyawan = Session::get('id_karyawan');
        $now         = Carbon::now('Asia/Jakarta');
        $today       = Carbon::today()->toDateString();

        // 1. Ambil data pengerjaan misi
        $pengerjaan = Pengerjaan::with('misi')
            ->where('id_pengerjaan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->where('tanggal', $today)
            ->firstOrFail();

        // 2. Validasi status pengerjaan
        if (!in_array($pengerjaan->status, ['belum_mengerjakan', 'ditolak'])) {
            return response()->json(['message' => 'Misi ini tidak bisa diupload ulang.'], 403);
        }

        // 3. Pastikan data foto dikirim
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

            // 4. Set lokasi penyimpanan langsung ke public/uploads/misi/
            $destinationPath = public_path('uploads/misi/');

            // Buat folder otomatis jika belum tersedia di folder public kamu
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // 5. Tulis data gambar menjadi file fisik (.png)
            file_put_contents($destinationPath . $fileName, $image_base64);

            // Hapus file foto lama jika ini adalah proses upload ulang
            if ($pengerjaan->foto && file_exists($destinationPath . $pengerjaan->foto)) {
                unlink($destinationPath . $pengerjaan->foto);
            }

            // 6. Update data ke database
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
