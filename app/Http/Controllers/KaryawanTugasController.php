<?php
// app/Http/Controllers/KaryawanTugasController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Pengumpulan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class KaryawanTugasController extends Controller
{
    public function index()
    {
        $id_karyawan = Session::get('id_karyawan');
        $minggu      = Carbon::now()->weekOfMonth;
        $bulan       = Carbon::now()->month;

        $pengumpulan = Pengumpulan::with('tugas')
            ->where('id_karyawan', $id_karyawan)
            ->whereHas('tugas', fn($q) => $q->where('minggu', $minggu)->where('bulan', $bulan))
            ->get()
            ->map(function ($p) {
                if (!$p->tugas) return $p;

                $now      = Carbon::now();
                $deadline = Carbon::parse($p->tugas->deadline);

                $p->sudah_lewat = $now->gt($deadline);
                $p->bisa_upload = !$p->sudah_lewat
                    && in_array($p->status, ['belum_mengerjakan', 'belum_mengumpulkan', 'ditolak']);

                return $p;
            });

        return view('karyawan.tugas.index', compact('pengumpulan'));
    }

    public function show($id)
    {
        $id_karyawan = Session::get('id_karyawan');

        $pengumpulan = Pengumpulan::with('tugas')
            ->where('id_pengumpulan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->firstOrFail();

        $now      = Carbon::now();
        $deadline = Carbon::parse($pengumpulan->tugas->deadline);

        $bisaUpload = $now->lte($deadline)
            && in_array($pengumpulan->status, ['belum_mengerjakan', 'belum_mengumpulkan', 'ditolak']);

        return view('karyawan.tugas.detail', compact('pengumpulan', 'bisaUpload', 'deadline'));
    }

    public function upload(Request $request, $id)
    {
        $id_karyawan = Session::get('id_karyawan');
        $now         = Carbon::now();

        $pengumpulan = Pengumpulan::with('tugas')
            ->where('id_pengumpulan', $id)
            ->where('id_karyawan', $id_karyawan)
            ->firstOrFail();

        if (!in_array($pengumpulan->status, ['belum_mengerjakan', 'belum_mengumpulkan', 'ditolak'])) {
            return response()->json(['message' => 'Tugas ini tidak bisa diupload ulang.'], 403);
        }

        $deadline = Carbon::parse($pengumpulan->tugas->deadline);
        if ($now->gt($deadline)) {
            return response()->json(['message' => 'Deadline sudah lewat.'], 403);
        }

        if (!$request->file) {
            return response()->json(['message' => 'File bukti wajib diupload.'], 422);
        }

        // Ambil string base64
        $image_parts  = explode(";base64,", $request->file);
        $image_base64 = base64_decode($image_parts[1]);

        // Kunci ekstensi wajib .pdf
        $fileName     = $id_karyawan . '_tugas_' . $id . '_' . time() . '.pdf';

        // Tentukan path tujuan langsung ke folder public/uploads/tugas
        $destinationPath = public_path('uploads/tugas');

        // Buat foldernya otomatis jika belum ada di folder public
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // Simpan file fisiknya menggunakan fungsi bawaan PHP asli (file_put_contents)
        file_put_contents($destinationPath . '/' . $fileName, $image_base64);

        // Hapus file lama di folder public jika ada
        if ($pengumpulan->file && file_exists($destinationPath . '/' . $pengumpulan->file)) {
            unlink($destinationPath . '/' . $pengumpulan->file);
        }

        $pengumpulan->update([
            'file'           => $fileName,
            'tanggal_upload' => $now->toDateString(),
            'waktu_upload'  => $now->toTimeString(),
            'status'        => 'menunggu',
        ]);

        return response()->json(['message' => 'Tugas berhasil diupload! Menunggu persetujuan.']);
    }

    public function lihatFile($id)
    {
        $id_karyawan = Session::get('id_karyawan');

        $pengumpulan = Pengumpulan::where('id_pengumpulan', $id);
        if ($id_karyawan) {
            $pengumpulan->where('id_karyawan', $id_karyawan);
        }
        $pengumpulan = $pengumpulan->firstOrFail();

        // Arahkan pencarian ke folder public/uploads/tugas
        $path = public_path('uploads/tugas/' . $pengumpulan->file);

        if (!file_exists($path) || !$pengumpulan->file) {
            abort(404, 'File PDF fisik tidak ditemukan di folder public/uploads/tugas.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
