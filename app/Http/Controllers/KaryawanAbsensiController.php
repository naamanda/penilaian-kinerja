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
    // Tambahkan properti radius koordinat yang sama persis seperti di Blade & Admin Controller
    private $office_lat =  -7.678603;
    private $office_lng =  109.035448;
    private $radius_km  = 0.1; // 100 meter toleransi jarak

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

        // 1. Validasi Input Ge geolocation
        $request->validate([
            'latitude'  => 'required',
            'longitude' => 'required',
            'foto'      => 'required'
        ]);

        if (Absensi::where('id_karyawan', $id)->where('tanggal', $today)->exists()) {
            return response()->json(['message' => 'Kamu sudah absen hari ini.'], 403);
        }

        if ($now->format('H:i') < '07:30') {
            return response()->json(['message' => 'Absensi belum dibuka. Silahkan tunggu Besok jam 07:30.'], 403);
        }

        // 2. Validasi Geofencing Jarak Kantor di Backend Karyawan
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $this->office_lat, $this->office_lng);
        if ($distance > $this->radius_km) {
            return response()->json(['message' => 'Gagal! Posisi Anda terdeteksi di luar radius kantor.'], 403);
        }

        $status       = ($now->format('H:i') <= '08:00') ? 'hadir' : 'terlambat';
        $image_parts  = explode(";base64,", $request->foto);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName     = $id . '_' . time() . '.png';

        $destinationPath = public_path('uploads/absensi/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        file_put_contents($destinationPath . $fileName, $image_base64);

        Absensi::create([
            'id_karyawan' => $id,
            'tanggal'     => $today,
            'waktu'       => $now->toTimeString(),
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'foto'        => $fileName,
            'status'      => $status,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil!',
            'status'  => $status,
        ]);
    }

    /**
     * Tambahkan fungsi helper hitung rumus Haversine/Lingkaran Besar di bawah ini
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos(min(max($dist, -1.0), 1.0));
        $dist = rad2deg($dist);
        return ($dist * 60 * 1.1515) * 1.609344;
    }
}