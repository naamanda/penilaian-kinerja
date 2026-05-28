<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Pengerjaan;
use App\Models\Pengumpulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilAkhirController extends Controller
{
    // =========================================================
    //  PUBLIC CALCULATION METHODS
    //  Dipanggil dari controller lain, termasuk KaryawanBerandaController
    // =========================================================

    /**
     * Hitung semua nilai karyawan untuk bulan & tahun tertentu.
     * Bisa dipanggil realtime (tanpa simpan) atau sebelum generate.
     *
     * @return array{kehadiran: float, misi: float, tugas: float, akhir: float, predikat: array, pelanggaran: array}
     */
    public function hitungNilai(int $idKaryawan, int $bulan, int $tahun): array
    {
        $nilaiKehadiran = $this->hitungNilaiKehadiran($idKaryawan, $bulan, $tahun);
        $nilaiMisi      = $this->hitungNilaiMisi($idKaryawan, $bulan, $tahun);
        $nilaiTugas     = $this->hitungNilaiTugas($idKaryawan, $bulan, $tahun);
        $nilaiAkhir     = round(($nilaiKehadiran + $nilaiMisi + $nilaiTugas) / 3, 2);

        return [
            'kehadiran'   => $nilaiKehadiran,
            'misi'        => $nilaiMisi,
            'tugas'       => $nilaiTugas,
            'akhir'       => $nilaiAkhir,
            'predikat'    => $this->hitungPredikat($nilaiAkhir),
            'pelanggaran' => $this->hitungPelanggaran($idKaryawan, $bulan, $tahun),
        ];
    }

    /**
     * Hitung pelanggaran karyawan untuk bulan & tahun tertentu.
     *
     * @return array{terlambat: int, tidak_mengerjakan: int, total_poin: int, status: string}
     */
    public function hitungPelanggaran(int $idKaryawan, int $bulan, int $tahun): array
    {
        // ── Terlambat (1 poin) ────────────────────────────────
        $terlambatAbsensi = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'terlambat')
            ->count();

        $terlambatMisi = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'terlambat')
            ->count();

        $terlambatTugas = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->where('status', 'terlambat')
            ->count();

        $totalTerlambat = $terlambatAbsensi + $terlambatMisi + $terlambatTugas;

        // ── Tidak mengerjakan / tidak hadir (2 poin) ─────────
        $hariKerjaSelesai = 22;
        $realHadir = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $tidakHadirAbsensi = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'tidak_hadir')
            ->count();

        $tidakMengerjakanMisi = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereIn('status', ['tidak_mengerjakan', 'belum_mengerjakan'])
            ->count();

        $tidakMengerjakanTugas = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->whereIn('status', ['tidak_mengerjakan', 'belum_mengerjakan'])
            ->count();

        $totalTidakMengerjakan = $tidakHadirAbsensi + $tidakMengerjakanMisi + $tidakMengerjakanTugas;

        // ── Total poin & status ───────────────────────────────
        $totalPoin = ($totalTerlambat * 1) + ($totalTidakMengerjakan * 2);

        return [
            'terlambat'         => $totalTerlambat,
            'tidak_mengerjakan' => $totalTidakMengerjakan,
            'total_poin'        => $totalPoin,
            'status'            => $this->hitungStatusPelanggaran($totalPoin),
        ];
    }

    /**
     * Tentukan predikat dari nilai akhir.
     *
     * @return array{kode: string, label: string}
     */
    public function hitungPredikat(float $nilai): array
    {
        if ($nilai >= 90) return ['kode' => 'A',  'label' => 'Sangat Baik'];
        if ($nilai >= 80) return ['kode' => 'AB', 'label' => 'Baik Sekali'];
        if ($nilai >= 70) return ['kode' => 'B',  'label' => 'Baik'];
        if ($nilai >= 60) return ['kode' => 'C',  'label' => 'Cukup'];
        return                   ['kode' => 'D',  'label' => 'Kurang'];
    }

    // =========================================================
    //  ATASAN — INDEX (Otomatis panggil hitung data terbaru)
    // =========================================================
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        // Pemicu Otomatis: Jalankan fungsi generate internal sebelum mengambil data view
        $this->executeGenerateInternal($bulan, $tahun);

        // Ambil data hasil akhir yang sudah dipastikan paling update di DB
        $hasilAkhir = HasilAkhir::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('nilai_akhir')
            ->get();

        return view('atasan.hasilakhir.index', compact('hasilAkhir', 'bulan', 'tahun'));
    }

    // =========================================================
    //  ATASAN — GENERATE (Fungsi Inti Simpan Ke DB)
    // =========================================================
    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $this->executeGenerateInternal($request->bulan, $request->tahun);

        return redirect()->route('atasan.hasilakhir.index', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
            ->with('success', "Hasil akhir bulan {$request->bulan}/{$request->tahun} berhasil diperbarui.");
    }

    /**
     * Fungsi Helper Internal agar kalkulasi otomatis bisa dipakai bersama
     */
    public function executeGenerateInternal(int $bulan, int $tahun)
    {
        $karyawans = Karyawan::where('id_role', 2)->get(); // Hanya staff/karyawan

        DB::beginTransaction();
        try {
            foreach ($karyawans as $karyawan) {
                $data = $this->hitungNilai($karyawan->id_karyawan, $bulan, $tahun);

                HasilAkhir::updateOrCreate(
                    [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'bulan'       => $bulan,
                        'tahun'       => $tahun,
                    ],
                    [
                        'total_harikerja'    => 22,
                        'nilai_kehadiran'    => $data['kehadiran'],
                        'nilai_kedisiplinan' => max(0, 100 - ($data['pelanggaran']['total_poin'] * 5)),
                        'nilai_tugas'        => $data['tugas'],
                        'nilai_akhir'        => $data['akhir'],
                        'predikat'           => $data['predikat']['kode'],
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal sinkronisasi otomatis hasil akhir: " . $e->getMessage());
        }
    }

    // =========================================================
    //  ATASAN — SHOW
    // =========================================================
    public function show($id)
    {
        $hasil = HasilAkhir::with(['karyawan', 'reward'])->findOrFail($id);

        return view('atasan.hasilakhir.show', compact('hasil'));
    }

    // =========================================================
    //  PRIVATE HELPERS
    // =========================================================
    private function hitungNilaiKehadiran(int $idKaryawan, int $bulan, int $tahun): float
    {
        $absensi = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $hariKerja   = 22;
        $jumlahHadir = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();

        return $hariKerja > 0 ? round(($jumlahHadir / $hariKerja) * 100, 2) : 0;
    }

    private function hitungNilaiMisi(int $idKaryawan, int $bulan, int $tahun): float
    {
        $totalPoin = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->join('misi', 'pengerjaan.id_misi', '=', 'misi.id_misi')
            ->sum('misi.poin');

        $poinDidapat = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereIn('status', ['disetujui', 'terlambat'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('poin_didapat');

        return $totalPoin > 0 ? round(($poinDidapat / $totalPoin) * 100, 2) : 0;
    }

    private function hitungNilaiTugas(int $idKaryawan, int $bulan, int $tahun): float
    {
        $totalPoin = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->join('tugas', 'pengumpulan.id_tugas', '=', 'tugas.id_tugas')
            ->sum('tugas.poin');

        $poinDidapat = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereIn('status', ['disetujui', 'terlambat'])
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->sum('poin_didapat');

        return $totalPoin > 0 ? round(($poinDidapat / $totalPoin) * 100, 2) : 0;
    }

    private function hitungStatusPelanggaran(int $totalPoin): string
    {
        if ($totalPoin >= 9) return 'SP2';
        if ($totalPoin >= 5) return 'SP1';
        return 'aman';
    }
}
