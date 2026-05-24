<?php
// app/Http/Controllers/KaryawanAbsensiController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class KaryawanAbsensiController extends Controller
{
    public function index()
    {
        $id          = Session::get('id_karyawan');
        $today       = Carbon::today()->toDateString();
        $absensiHari = Absensi::where('id_karyawan', $id)
            ->where('tanggal', $today)
            ->first();

        return view('karyawan.absensi', [
            'sudahAbsen'  => (bool) $absensiHari,
            'fotoAbsensi' => $absensiHari?->foto ?? null,
            'waktuAbsen'  => $absensiHari
                ? Carbon::parse($absensiHari->waktu)->format('H:i') . ' WIB'
                : null,
            'statusAbsen' => $absensiHari?->status ?? null,
        ]);
    }

    public function simpan(Request $request)
    {
        $id    = Session::get('id_karyawan');
        $now   = Carbon::now();
        $today = $now->toDateString();

        if (Absensi::where('id_karyawan', $id)->where('tanggal', $today)->exists()) {
            return response()->json(['message' => 'Kamu sudah absen hari ini.'], 403);
        }

        if ($now->format('H:i') < '07:30') {
            return response()->json(['message' => 'Absensi belum dibuka. Silahkan tunggu Besok jam 07:30.'], 403);
        }

        if (!$request->foto) {
            return response()->json(['message' => 'Foto wajib diambil.'], 422);
        }

        $status       = ($now->format('H:i') <= '08:00') ? 'hadir' : 'terlambat';
        $image_parts  = explode(";base64,", $request->foto);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName     = $id . '_' . time() . '.png';
        Storage::put('uploads/absensi/' . $fileName, $image_base64);

        Absensi::create([
            'id_karyawan' => $id,
            'tanggal'     => $today,
            'waktu'       => $now->toTimeString(),
            'latitude'    => $request->latitude ?? null,
            'longitude'   => $request->longitude ?? null,
            'foto'        => $fileName,
            'status'      => $status,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil!',
            'status'  => $status,
        ]);
    }
}