<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\HasilAkhir;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Pengerjaan;
use App\Models\Pengumpulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilAkhirController extends Controller
{
    /**
     * Hitung semua nilai karyawan untuk bulan & tahun tertentu.
     * Murni memisahkan Kehadiran, Kedisiplinan (Misi), dan Tugas.
     * Izin (status 'izin' di Absensi) diperlakukan netral: tidak menambah,
     * tidak mengurangi nilai maupun dihitung sebagai pelanggaran.
     *
     * @return array{kehadiran: float, kedisiplinan: float, tugas: float, akhir: float, predikat: array, pelanggaran: array}
     */
    public function hitungNilai(int $idKaryawan, int $bulan, int $tahun): array
    {
        $nilaiKehadiran    = $this->hitungNilaiKehadiran($idKaryawan, $bulan, $tahun);
        $nilaiKedisiplinan = $this->hitungNilaiMisi($idKaryawan, $bulan, $tahun);
        $nilaiTugas        = $this->hitungNilaiTugas($idKaryawan, $bulan, $tahun);

        $nilaiAkhir = round(($nilaiKehadiran + $nilaiKedisiplinan + $nilaiTugas) / 3, 2);

        return [
            'kehadiran'    => $nilaiKehadiran,
            'kedisiplinan' => $nilaiKedisiplinan,
            'tugas'        => $nilaiTugas,
            'akhir'        => $nilaiAkhir,
            'predikat'     => $this->hitungPredikat($nilaiAkhir),
            'pelanggaran'  => $this->hitungPelanggaran($idKaryawan, $bulan, $tahun),
        ];
    }

    /**
     * Hitung pelanggaran karyawan untuk bulan & tahun tertentu.
     * Izin dianggap sebagai catatan sah (bukan tidak hadir), jadi tidak dihitung
     * sebagai pelanggaran.
     *
     * @return array{terlambat: int, tidak_mengerjakan: int, total_poin: int, status: string}
     */
    public function hitungPelanggaran(int $idKaryawan, int $bulan, int $tahun): array
    {
        // ── 1. Terlambat (1 poin)
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

        // ── 2. Tidak Hadir Absensi (izin TIDAK dihitung tidak hadir)
        $karyawanData     = Karyawan::findOrFail($idKaryawan);
        $tanggalBergabung = $karyawanData->tanggal_bergabung
            ? Carbon::parse($karyawanData->tanggal_bergabung)
            : Carbon::create($tahun, $bulan, 1);

        $isCurrentMonth = ($bulan == Carbon::now()->month && $tahun == Carbon::now()->year);
        if ($isCurrentMonth) {
            $jamSekarang         = Carbon::now()->format('H:i');
            $hariIni             = Carbon::now()->day;
            $batasHariPengecekan = ($jamSekarang >= '18:00') ? $hariIni : $hariIni - 1;
        } else {
            $batasHariPengecekan = Carbon::create($tahun, $bulan)->daysInMonth;
        }

        $absensi = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $tidakHadirAbsensi = 0;
        for ($d = 1; $d <= $batasHariPengecekan; $d++) {
            $tanggalObj = Carbon::create($tahun, $bulan, $d);
            if (
                \App\Helpers\HariLiburHelper::isHariKerja($tanggalObj)
                && $tanggalObj->gte($tanggalBergabung)
            ) {
                // 'izin' ikut dianggap "ada catatan" -> tidak dihitung tidak hadir
                $adaAbsensi = $absensi->whereIn('status', ['hadir', 'terlambat', 'izin'])
                    ->where('tanggal', $tanggalObj->format('Y-m-d'))
                    ->count();
                if (!$adaAbsensi) $tidakHadirAbsensi++;
            }
        }

        // ── 3. Tidak Mengerjakan Misi & Tugas
        // (Pengerjaan berstatus 'izin' dikecualikan otomatis karena hilir/hulu
        // di executeGenerateInternal() sudah mencegahnya menjadi 'tidak_mengerjakan')
        $tidakMengerjakanMisi = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'tidak_mengerjakan')
            ->count();

        $tidakMengerjakanTugas = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan)
                ->where('id_divisi', $karyawanData->id_divisi))
            ->where('status', 'tidak_mengerjakan')
            ->count();

        $totalTidakMengerjakan = $tidakHadirAbsensi + $tidakMengerjakanMisi + $tidakMengerjakanTugas;

        // ── 4. Total Poin & Status
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

    //  ATASAN — INDEX (Otomatis panggil hitung data terbaru)
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $this->executeGenerateInternal($bulan, $tahun);

        $hasilAkhir = HasilAkhir::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('nilai_akhir')
            ->get();

        return view('atasan.hasilakhir.index', compact('hasilAkhir', 'bulan', 'tahun'));
    }

    //  ATASAN — GENERATE (Fungsi Inti Simpan Ke DB)
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

    public function executeGenerateInternal(int $bulan, int $tahun)
    {
        $karyawans = Karyawan::where('id_role', 2)->get();
        $misiList  = \App\Models\Misi::all();

        $hariMax = ($bulan == Carbon::now()->month && $tahun == Carbon::now()->year)
            ? Carbon::now()->day
            : Carbon::create($tahun, $bulan)->daysInMonth;

        foreach ($karyawans as $karyawan) {
            $tanggalBergabung = $karyawan->tanggal_bergabung
                ? Carbon::parse($karyawan->tanggal_bergabung)
                : Carbon::create($tahun, $bulan, 1);
            $awalBulan        = Carbon::create($tahun, $bulan, 1);
            $mulaiHari        = $tanggalBergabung->gt($awalBulan) ? $tanggalBergabung->day : 1;

            if (
                $tanggalBergabung->year > $tahun ||
                ($tanggalBergabung->year == $tahun && $tanggalBergabung->month > $bulan)
            ) {
                continue;
            }

            // Generate misi seperti biasa, TANPA skip untuk tanggal izin.
            // Status awal selalu 'belum_mengerjakan' — netral secara nilai & pelanggaran.
            for ($d = $mulaiHari; $d <= $hariMax; $d++) {
                $tanggalObj = Carbon::create($tahun, $bulan, $d);

                if (!\App\Helpers\HariLiburHelper::isHariKerja($tanggalObj) || $tanggalObj->gt(Carbon::today())) {
                    continue;
                }

                $formatTanggal = $tanggalObj->format('Y-m-d');

                foreach ($misiList as $misi) {
                    Pengerjaan::firstOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'id_misi'     => $misi->id_misi,
                            'tanggal'     => $formatTanggal,
                        ],
                        [
                            'waktu_upload' => null,
                            'foto'         => null,
                            'poin_didapat' => 0,
                            'status'       => 'belum_mengerjakan',
                        ]
                    );
                }
            }
        }

        // 1. TRIGGER OTOMATIS UNTUK TUGAS
        Pengumpulan::where('status', 'belum_mengerjakan')
            ->whereHas('tugas', function ($q) {
                $q->where('deadline', '<', Carbon::now());
            })->update(['status' => 'tidak_mengerjakan']);

        // 2. TRIGGER OTOMATIS UNTUK MISI (per karyawan, exclude tanggal izin)
        $liburBulan = [];
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $tgl = Carbon::create($tahun, $bulan, $d);
            if (!\App\Helpers\HariLiburHelper::isHariKerja($tgl)) {
                $liburBulan[] = $tgl->format('Y-m-d');
            }
        }

        foreach ($karyawans as $karyawan) {
            // Tanggal karyawan ini sedang izin -> jangan diubah ke 'tidak_mengerjakan',
            // biarkan tetap 'belum_mengerjakan' (netral, tidak dihitung pelanggaran/nilai)
            $tanggalIzinKaryawan = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('status', 'izin')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->pluck('tanggal')
                ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
                ->toArray();

            $tanggalDikecualikan = array_merge($liburBulan, $tanggalIzinKaryawan);

            Pengerjaan::where('id_karyawan', $karyawan->id_karyawan)
                ->where('status', 'belum_mengerjakan')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->whereNotIn('tanggal', $tanggalDikecualikan)
                ->where(function ($q) {
                    $q->where('tanggal', '<', Carbon::today())
                        ->orWhere(function ($q2) {
                            $q2->where('tanggal', Carbon::today())
                                ->whereHas('misi', fn($m) => $m->where(
                                    'waktu_selesai',
                                    '<',
                                    Carbon::now()->subMinutes(10)->format('H:i:s')
                                ));
                        });
                })->update(['status' => 'tidak_mengerjakan']);
        }

        // 3. HITUNG & SIMPAN HASIL AKHIR
        DB::beginTransaction();
        try {
            $totalHariKerja = \App\Helpers\HariLiburHelper::getTotalHariKerjaBulan($bulan, $tahun);

            foreach ($karyawans as $karyawan) {
                $data = $this->hitungNilai($karyawan->id_karyawan, $bulan, $tahun);

                HasilAkhir::updateOrCreate(
                    [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'bulan'       => $bulan,
                        'tahun'       => $tahun,
                    ],
                    [
                        'total_harikerja'    => $totalHariKerja,
                        'nilai_kehadiran'    => $data['kehadiran'],
                        'nilai_kedisiplinan' => $data['kedisiplinan'],
                        'nilai_tugas'        => $data['tugas'],
                        'nilai_akhir'        => $data['akhir'],
                        'predikat'           => $data['predikat']['kode'],
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal sinkronisasi otomatis hasil akhir: " . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    //  ATASAN — SHOW
    public function show($id)
    {
        $hasil = HasilAkhir::with(['karyawan', 'reward'])->findOrFail($id);

        return view('atasan.hasilakhir.show', compact('hasil'));
    }

    //  PRIVATE HELPERS

    /**
     * Nilai kehadiran murni. Izin (disetujui) dikeluarkan dari penyebut
     * sehingga bersifat netral: tidak menambah, tidak mengurangi nilai.
     */
    private function hitungNilaiKehadiran(int $idKaryawan, int $bulan, int $tahun): float
    {
        $absensi = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $hariKerja   = \App\Helpers\HariLiburHelper::getTotalHariKerjaBulan($bulan, $tahun);
        $jumlahIzin  = $absensi->where('status', 'izin')->count();
        $jumlahHadir = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();

        $hariKerjaEfektif = $hariKerja - $jumlahIzin;

        return $hariKerjaEfektif > 0 ? round(($jumlahHadir / $hariKerjaEfektif) * 100, 2) : 0;
    }

    private function hitungNilaiMisi(int $idKaryawan, int $bulan, int $tahun): float
    {
        // Total semua misi bulan ini
        $totalSemuaPoin = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->join('misi', 'pengerjaan.id_misi', '=', 'misi.id_misi')
            ->sum('misi.poin');

        $poinDisetujui = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'disetujui')
            ->sum('poin_didapat');

        $poinTerlambat = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'terlambat')
            ->sum('poin_didapat');

        $poinBelum = Pengerjaan::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'belum_mengerjakan')
            ->join('misi', 'pengerjaan.id_misi', '=', 'misi.id_misi')
            ->sum('misi.poin');

        // NOTE: karena hulu/hilir di executeGenerateInternal() sekarang mencegah
        // misi digenerate/diubah 'tidak_mengerjakan' untuk tanggal izin, poin misi
        // di hari izin otomatis tidak pernah masuk hitungan ini sama sekali
        // (baik sebagai totalSemuaPoin, poinDisetujui, poinTerlambat, maupun poinBelum).

        $totalPoin = $totalSemuaPoin - $poinDisetujui - $poinTerlambat - $poinBelum;

        $poinDidapat = $poinDisetujui + $poinTerlambat;

        return $totalPoin > 0 ? round(($poinDidapat / $totalPoin) * 100, 2) : 0;
    }

    private function hitungNilaiTugas(int $idKaryawan, int $bulan, int $tahun): float
    {
        $totalPoin = Pengumpulan::where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->whereNotIn('status', ['belum_mengerjakan'])
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
        if ($totalPoin >= 12) return 'SP2';
        if ($totalPoin >= 8) return 'SP1';
        return 'aman';
    }
}
