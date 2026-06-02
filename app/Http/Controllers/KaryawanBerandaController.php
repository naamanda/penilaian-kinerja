<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Tugas;
use App\Models\Pengumpulan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class KaryawanBerandaController extends Controller
{
    // Inject HasilAkhirController sebagai sumber perhitungan
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

        $karyawan  = Karyawan::findOrFail($id);

        // ── Nilai realtime — memanggil HasilAkhirController ──
        $nilaiData = $this->hasilAkhir->hitungNilai($id, $bulan, $tahun);
        $nilai     = $nilaiData['akhir'];

        // ── Leaderboard realtime ──────────────────────────────
        $leaderboard = Karyawan::where('id_role', 2)
            ->get()
            ->map(function ($k) use ($bulan, $tahun) {
                $k->total_nilai = $this->hasilAkhir->hitungNilai($k->id_karyawan, $bulan, $tahun)['akhir'];
                return $k;
            })
            ->sortByDesc('total_nilai')
            ->take(5)
            ->values();

        // ── Kehadiran bulan ini ───────────────────────────────
        $absensi = Absensi::where('id_karyawan', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // 1. Ambil tanggal hari ini dan tentukan apakah hari ini sudah dihitung atau belum
        $hariIni = Carbon::now()->day;
        $jamSekarang = Carbon::now()->format('H:i');

        // Jika belum lewat jam 08:00, batas pengecekan hanya sampai kemarin (hari ini jangan dihitung dulu)
        $batasHariPengecekan = ($jamSekarang >= '08:10') ? $hariIni : $hariIni - 1;

        // 2. LOGIKA BARU: Hitung berapa jumlah hari Senin-Jumat yang sudah terlewat dari tanggal 1 sampai batas hari ini
        $hariKerjaBerjalan = 0;
        for ($d = 1; $d <= $batasHariPengecekan; $d++) {
            $tanggalCheck = Carbon::create($tahun, $bulan, $d);

            // isWeekday() memastikan hanya hari Senin s.d Jumat yang dihitung (Sabtu & Minggu di-skip)
            if ($tanggalCheck->isWeekday()) {
                $hariKerjaBerjalan++;
            }
        }

        $realHadir = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();

        // 3. Logika otomatis: Hari kerja efektif yang sudah lewat dikurangi kehadiran asli
        $tidakHadirOtomatis = max(0, $hariKerjaBerjalan - $realHadir);

        $kehadiran = [
            'total'       => $realHadir,
            'hari_kerja'  => 22, // Target total sebulan untuk tampilan UI
            'hadir'       => $absensi->where('status', 'hadir')->count(),
            'terlambat'   => $absensi->where('status', 'terlambat')->count(),
            'tidak_hadir' => $tidakHadirOtomatis,
        ];

        // ── Target tugas mingguan ─────────────────────────────
        $minggu = Carbon::now()->weekOfMonth;
        $tugas  = Tugas::where('minggu', $minggu)
            ->where('bulan', $bulan)
            ->where('id_divisi', $karyawan->id_divisi) // ← tambah ini
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
            'tugas'
        ));
    }
}
