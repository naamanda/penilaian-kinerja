<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumpulan;
use App\Models\Divisi;
use Carbon\Carbon;

class PengumpulanController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'antrean');
        $search = $request->input('search');
        $bulan  = (int) $request->get('bulan', date('n'));
        $tahun  = (int) $request->get('tahun', date('Y'));

        // 1. QUERY UTAMA UNTUK ISI TABEL (DI-FILTER BERDASARKAN TAB YANG DIKLIK)
        $query = Pengumpulan::with(['tugas.divisi', 'karyawan']);

        if ($tab == 'antrean') {
            $query->whereIn('status', ['menunggu', 'ditolak'])
                ->whereMonth('tanggal_upload', $bulan)
                ->whereYear('tanggal_upload', $tahun);
        } elseif ($tab == 'belum_mengerjakan') {
            // REVISI: Masukkan 'tidak_mengerjakan' ke dalam query tab agar admin tetap bisa melihat listnya
            $query->whereIn('status', ['belum_mengerjakan', 'tidak_mengerjakan'])
                ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan)->whereYear('deadline', $tahun))
                ->whereRaw('EXISTS (
            SELECT 1 FROM tugas 
            JOIN karyawan ON karyawan.id_divisi = tugas.id_divisi
            WHERE tugas.id_tugas = pengumpulan.id_tugas
            AND karyawan.id_karyawan = pengumpulan.id_karyawan)');
        } elseif ($tab == 'selesai') {
            $query->whereIn('status', ['disetujui', 'terlambat'])
                ->whereMonth('tanggal_upload', $bulan)
                ->whereYear('tanggal_upload', $tahun);
        }

        // Filter Pencarian jika user mengetik nama karyawan/tugas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', fn($q2) => $q2->where('nama', 'like', '%' . $search . '%'))
                    ->orWhereHas('tugas', fn($q2) => $q2->where('nama_tugas', 'like', '%' . $search . '%'));
            });
        }

        // Eksekusi data untuk tabel bawah (Paginasi 5 data per halaman)
        $data = $query->orderBy('id_pengumpulan', 'desc')->paginate(5)->withQueryString();


        // =========================================================================
        // 2. HITUNG STATISTIK (DIPISAH SENDIRI-SENDIRI BIAR RELEVAN & TIDAK NGACU)
        // =========================================================================
        $stat = [
            // REVISI: Hitung gabungan status 'belum_mengerjakan' dan 'tidak_mengerjakan' untuk counter di atas tab
            'belum' => Pengumpulan::whereIn('status', ['belum_mengerjakan', 'tidak_mengerjakan'])
                ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan)->whereYear('deadline', $tahun))
                ->whereRaw('EXISTS (
            SELECT 1 FROM tugas 
            JOIN karyawan ON karyawan.id_divisi = tugas.id_divisi
            WHERE tugas.id_tugas = pengumpulan.id_tugas
            AND karyawan.id_karyawan = pengumpulan.id_karyawan)')
                ->count(),

            'menunggu' => Pengumpulan::where('status', 'menunggu')
                ->whereMonth('tanggal_upload', $bulan)->whereYear('tanggal_upload', $tahun)->count(),

            'terlambat' => Pengumpulan::where('status', 'terlambat')
                ->whereMonth('tanggal_upload', $bulan)->whereYear('tanggal_upload', $tahun)->count(),

            'disetujui' => Pengumpulan::where('status', 'disetujui')
                ->whereMonth('tanggal_upload', $bulan)->whereYear('tanggal_upload', $tahun)->count(),
        ];

        $list_divisi = Divisi::all();

        return view('admin.tugas.pengumpulan.index', compact('data', 'stat', 'tab', 'search', 'bulan', 'tahun', 'list_divisi'));
    }

    public function show($id)
    {
        $pengumpulan = Pengumpulan::with(['tugas', 'karyawan.divisi'])->findOrFail($id);
        return view('admin.tugas.pengumpulan.detail', compact('pengumpulan'));
    }

    public function approve($id)
    {
        $pengumpulan = Pengumpulan::with('tugas')->findOrFail($id);
        $tugas       = $pengumpulan->tugas;

        $waktuUpload = Carbon::parse($pengumpulan->waktu_upload);
        $deadline    = Carbon::parse($tugas->deadline);

        // ✅ Fix: lte = tepat waktu, sisanya = terlambat (sudah dicegah kalau lewat toleransi)
        if ($waktuUpload->lte($deadline)) {
            $pengumpulan->status       = 'disetujui';
            $pengumpulan->poin_didapat = $tugas->poin ?? 0;
        } else {
            $pengumpulan->status       = 'terlambat';
            $pengumpulan->poin_didapat = intval(($tugas->poin ?? 0) / 2);
        }

        $pengumpulan->save();

        return redirect('/approve-tugas?tab=antrean')->with('success', 'Tugas berhasil disetujui.');
    }

    public function reject($id)
    {
        $pengumpulan = Pengumpulan::findOrFail($id);
        $pengumpulan->update([
            'status'      => 'ditolak',
            'poin_didapat' => 0,
        ]);

        return redirect('/approve-tugas?tab=antrean')->with('success', 'Tugas ditolak. Menunggu karyawan upload ulang.');
    }

    public function lihatFile($id)
    {
        // Cari data pengumpulan berdasarkan ID tugas tanpa memfilter ID Karyawan
        $pengumpulan = Pengumpulan::where('id_pengumpulan', $id)->firstOrFail();

        // Arahkan path pencarian langsung ke folder public/uploads/tugas
        $path = public_path('uploads/tugas/' . $pengumpulan->file);

        // Cek apakah file fisik PDF benar-benar ada di folder tersebut
        if (!file_exists($path) || !$pengumpulan->file) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        // Tampilkan file PDF secara aman di tab baru browser admin
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
