<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class KaryawanIzinController extends Controller
{
    public function index()
    {
        $id    = Session::get('id_karyawan');
        $today = Carbon::today()->toDateString();

        $absensiHariIni = Absensi::where('id_karyawan', $id)
            ->where('tanggal', $today)
            ->first();

        $riwayatIzin = Izin::where('id_karyawan', $id)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->take(10)
            ->get();

        return view('karyawan.izin', [
            'sudahAdaCatatanHariIni' => (bool) $absensiHariIni,
            'statusHariIni'          => $absensiHariIni?->status,
            'riwayatIzin'            => $riwayatIzin,
        ]);
    }

    public function store(Request $request)
    {
        $id = Session::get('id_karyawan');

        // Tanggal izin SELALU hari ini, ditentukan di server.
        // Sengaja tidak diambil dari input client supaya tidak bisa dimanipulasi
        // (misal lewat Inspect Element mengganti value tanggal ke hari lain).
        $tanggal = Carbon::today()->toDateString();

        $request->validate([
            'file_izin'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Cegah dobel: sudah absen atau sudah ada izin di tanggal yang sama
        $sudahAdaCatatan = Absensi::where('id_karyawan', $id)
            ->where('tanggal', $tanggal)
            ->exists();
        if ($sudahAdaCatatan) {
            return response()->json(['message' => 'Sudah ada catatan absensi/izin untuk tanggal tersebut.'], 403);
        }

        return DB::transaction(function () use ($request, $id, $tanggal) {

            // Simpan file surat izin
            $file     = $request->file('file_izin');
            $fileName = $id . '_izin_' . time() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/izin/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);

            // Buat record absensi berstatus 'izin' untuk tanggal tsb
            $absensi = Absensi::create([
                'id_karyawan' => $id,
                'tanggal'     => $tanggal,
                'waktu'       => Carbon::now()->toTimeString(),
                'latitude'    => null,
                'longitude'   => null,
                'foto'        => null,
                'status'      => 'izin',
            ]);

            // Buat record pengajuan izin, menunggu approval admin/atasan
            Izin::create([
                'id_karyawan'       => $id,
                'id_absensi'        => $absensi->id_absensi,
                'tanggal_izin'      => $tanggal,
                'file_izin'         => $fileName,
                'keterangan'        => $request->keterangan,
                'status'            => 'menunggu',
                'tanggal_pengajuan' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Pengajuan izin berhasil dikirim, menunggu persetujuan admin.',
            ]);
        });
    }
}