<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Tugas;
use App\Models\Pengumpulan;
use App\Models\Pengerjaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class KaryawanBerandaController extends Controller
{
    protected HasilAkhirController $hasilAkhir;

    public function __construct()
    {
        $this->hasilAkhir = new HasilAkhirController();
    }

    public function beranda()
    {
        $id    = Session::get('id_karyawan');
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        $karyawan = Karyawan::findOrFail($id);

        // ── Nilai + Pelanggaran realtime ──────────────────────
        $nilaiData   = $this->hasilAkhir->hitungNilai($id, $bulan, $tahun);

        $nilai        = $nilaiData['akhir'];
        $pelanggaran  = $nilaiData['pelanggaran']; // ← pakai hasil hitungPelanggaran()

        // ── Leaderboard realtime ──────────────────────────────
        $leaderboard = Karyawan::where('id_role', 2)
            ->get()
            ->map(function ($k) use ($bulan, $tahun) {
                $k->total_nilai = $this->hasilAkhir->hitungNilai($k->id_karyawan, $bulan, $tahun)['akhir'];
                return $k;
            })
            ->sortByDesc('total_nilai')
            ->values();

        // ── Kehadiran bulan ini ───────────────────────────────
        $absensi = Absensi::where('id_karyawan', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $hariIni     = Carbon::now()->day;
        $jamSekarang = Carbon::now()->format('H:i');

        $batasHariPengecekan = ($jamSekarang >= '09:00') ? $hariIni : $hariIni - 1;

        $tanggalBergabung = $karyawan->tanggal_bergabung
            ? Carbon::parse($karyawan->tanggal_bergabung)
            : Carbon::create($tahun, $bulan, 1);

        $hariKerjaBerjalan = 0;
        for ($d = 1; $d <= $batasHariPengecekan; $d++) {
            $tanggalCheck = Carbon::create($tahun, $bulan, $d);
            if (
                \App\Helpers\HariLiburHelper::isHariKerja($tanggalCheck)
                && $tanggalCheck->gte($tanggalBergabung)
            ) {
                $hariKerjaBerjalan++;
            }
        }

        $realHadir         = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();
        $tidakHadirAbsensi = 0;
        for ($d = 1; $d <= $batasHariPengecekan; $d++) {
            $tanggalObj = Carbon::create($tahun, $bulan, $d);
            if (
                \App\Helpers\HariLiburHelper::isHariKerja($tanggalObj)
                && $tanggalObj->gte($tanggalBergabung)
            ) {
                $adaAbsensi = $absensi->whereIn('status', ['hadir', 'terlambat'])
                    ->where('tanggal', $tanggalObj->format('Y-m-d'))
                    ->count();
                if (!$adaAbsensi) $tidakHadirAbsensi++;
            }
        }

        $kehadiran = [
            'total'       => $realHadir,
            'hari_kerja'  => \App\Helpers\HariLiburHelper::getTotalHariKerjaBulan($bulan, $tahun),
            'hadir'       => $absensi->where('status', 'hadir')->count(),
            'terlambat'   => $absensi->where('status', 'terlambat')->count(),
            'tidak_hadir' => $tidakHadirAbsensi,
        ];

        // ── Detail Pelanggaran (breakdown per kategori untuk modal) ───
        $detailTerlambat = [
            'absensi' => Absensi::where('id_karyawan', $id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'terlambat')
                ->count(),
            'misi' => Pengerjaan::where('id_karyawan', $id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'terlambat')
                ->count(),
            'tugas' => Pengumpulan::where('id_karyawan', $id)
                ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
                ->where('status', 'terlambat')
                ->count(),
        ];

        $detailTidakMengerjakan = [
            'absensi' => $tidakHadirAbsensi, // ← langsung pakai hasil loop di atas
            'misi'    => Pengerjaan::where('id_karyawan', $id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'tidak_mengerjakan')
                ->count(),
            'tugas'   => Pengumpulan::where('id_karyawan', $id)
                ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
                ->where('status', 'tidak_mengerjakan')
                ->count(),
        ];

        // ── Target tugas mingguan ─────────────────────────────
        $minggu = Carbon::now()->weekOfMonth;
        $tugas  = Tugas::where('minggu', $minggu)
            ->where('bulan', $bulan)
            ->where('id_divisi', $karyawan->id_divisi)
            ->get()
            ->map(function ($t) use ($id) {
                $t->selesai = Pengumpulan::where('id_tugas', $t->id_tugas)
                    ->where('id_karyawan', $id)
                    ->whereIn('status', ['disetujui', 'terlambat'])
                    ->count();
                $t->total = 1;
                return $t;
            });

        return view('karyawan.beranda', compact(
            'karyawan',
            'nilai',
            'nilaiData',
            'leaderboard',
            'kehadiran',
            'tugas',
            'pelanggaran',        // ← summary card pakai ini
            'detailTerlambat',
            'detailTidakMengerjakan'
        ));
    }
}
