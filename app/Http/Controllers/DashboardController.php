<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Absensi;
use App\Models\Pengerjaan;
use App\Models\Pengumpulan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::where('id_role', 2)->count();
        $totalDivisi   = Divisi::count();

        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $today = Carbon::today()->toDateString();

        $hadirHariIni     = Absensi::whereDate('tanggal', $today)->whereIn('status', ['hadir', 'terlambat'])->count();
        $terlambatHariIni = Absensi::whereDate('tanggal', $today)->where('status', 'terlambat')->count();
        $menungguMisi     = Pengerjaan::where('status', 'menunggu')->whereDate('tanggal', $today)->count();
        $menungguTugas    = Pengumpulan::where('status', 'menunggu')->count();

        $hasilAkhirCtrl = new HasilAkhirController();
        $leaderboard = Karyawan::where('id_role', 2)
            ->get()
            ->map(function ($k) use ($bulan, $tahun, $hasilAkhirCtrl) {
                $k->total_nilai = $hasilAkhirCtrl->hitungNilai($k->id_karyawan, $bulan, $tahun)['akhir'];
                return $k;
            })
            ->sortByDesc('total_nilai')
            ->values();

        return view('admin.dashboard', compact(
            'totalKaryawan', 'totalDivisi',
            'hadirHariIni', 'terlambatHariIni',
            'menungguMisi', 'menungguTugas',
            'leaderboard'
        ));
    }
}