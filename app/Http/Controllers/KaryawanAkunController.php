<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Pengerjaan;
use App\Models\Pengumpulan;
use App\Models\HasilAkhir;
use App\Models\Pelanggaran;
use App\Http\Controllers\HasilAkhirController;
use Illuminate\Support\Facades\Session;
// Ganti baris ini agar Intelephense VS Code tidak error:
use Barryvdh\DomPDF\Facade\Pdf;

class KaryawanAkunController extends Controller
{
    // Halaman Utama: Profil dan Menu Navigasi Ringkasan Nilai
    public function index()
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        $karyawan = Karyawan::where('id_karyawan', $idKaryawan)->first();

        if (!$karyawan) {
            return redirect('/login');
        }

        // Ambil data nilai bulan dan tahun berjalan untuk ringkasan menu utama
        $bulanSekarang = date('n');
        $tahunSekarang = date('Y');

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->first();

        // Kirim $hasilAkhir ke view index
        return view('karyawan.akun.index', compact('karyawan', 'hasilAkhir'));
    }

    public function unduh(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        // Normalisasi status otomatis sebelum tampil
        $hasilAkhirCtrl = new HasilAkhirController();
        $hasilAkhirCtrl->executeGenerateInternal($bulan, $tahun);

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // Ambil data absensi dari database
        $absensiData = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy('tanggal');

        // Hitung jumlah hari berjalan
        $hariMax = ($bulan == \Carbon\Carbon::now()->month && $tahun == \Carbon\Carbon::now()->year)
            ? \Carbon\Carbon::now()->day
            : \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;

        // Generate log absensi hanya hari kerja
        $detailAbsensi = [];
        for ($d = 1; $d <= $hariMax; $d++) {
            $tanggalObj = \Carbon\Carbon::create($tahun, $bulan, $d);

            if ($tanggalObj->isWeekday() && $tanggalObj->lte(\Carbon\Carbon::today())) {
                $formatTanggal = $tanggalObj->format('Y-m-d');

                if (isset($absensiData[$formatTanggal])) {
                    $detailAbsensi[] = $absensiData[$formatTanggal];
                } else {
                    $detailAbsensi[] = (object)[
                        'tanggal' => $formatTanggal,
                        'status'  => 'Tidak Hadir',
                    ];
                }
            }
        }

        // Log Misi dengan relasi misi (untuk ambil nama & poin)
        // Ambil data misi dari DB, group by tanggal
        $misiRawData = Pengerjaan::with('misi')
            ->where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('tanggal');

        // Generate log misi hanya hari kerja (sama seperti absensi)
        $detailMisi = collect();
        for ($d = 1; $d <= $hariMax; $d++) {
            $tanggalObj = \Carbon\Carbon::create($tahun, $bulan, $d);

            if ($tanggalObj->isWeekday() && $tanggalObj->lte(\Carbon\Carbon::today())) {
                $formatTanggal = $tanggalObj->format('Y-m-d');

                if (isset($misiRawData[$formatTanggal])) {
                    foreach ($misiRawData[$formatTanggal] as $misi) {
                        $detailMisi->push($misi);
                    }
                }
            }
        }

        // Log Tugas
        $detailTugas = Pengumpulan::with('tugas')
            ->where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->get();

        $daftarBulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return view('karyawan.akun.unduh', compact(
            'hasilAkhir',
            'detailAbsensi',
            'detailMisi',
            'detailTugas',
            'bulan',
            'tahun',
            'daftarBulan'
        ));
    }

    // Sub-menu: Reward
    public function reward(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');
        if (!$idKaryawan) return redirect('/login');

        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $hasilAkhirCtrl = new HasilAkhirController();
        $hasilAkhirCtrl->executeGenerateInternal($bulan, $tahun);

        $daftarReward = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->with('reward')
            ->whereHas('reward')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return view('karyawan.akun.reward', compact('daftarReward', 'daftarBulan'));
    }

    public function unduhSertifikat($idReward)
    {
        $idKaryawan = Session::get('id_karyawan');
        if (!$idKaryawan) return redirect('/login');

        $reward = \App\Models\Reward::with(['hasilakhir.karyawan'])
            ->where('id_reward', $idReward)
            ->whereHas('hasilakhir', fn($q) => $q->where('id_karyawan', $idKaryawan))
            ->firstOrFail();

        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$reward->hasilakhir->bulan];
        $tahun = $reward->hasilakhir->tahun;

        return view('karyawan.akun.sertifikat_pdf', compact('reward', 'namaBulan', 'tahun'));
    }

    // Sub-menu: Pelanggaran
    public function pelanggaran()
    {
        $idKaryawan = Session::get('id_karyawan');
        if (!$idKaryawan) return redirect('/login');

        $bulan = date('n');
        $tahun = date('Y');

        $hasilAkhirCtrl = new HasilAkhirController();
        $dataNilai = $hasilAkhirCtrl->hitungNilai($idKaryawan, $bulan, $tahun);
        $pelanggaran = $dataNilai['pelanggaran'];

        $riwayat = Pelanggaran::where('id_karyawan', $idKaryawan)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return view('karyawan.akun.pelanggaran', compact('pelanggaran', 'riwayat', 'bulan', 'tahun', 'daftarBulan'));
    }

    public function cetakPdf(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with(['karyawan.divisi'])
            ->first();

        if (!$hasilAkhir) {
            return back()->with('error', 'Data laporan tidak ditemukan.');
        }

        $absensiData = Absensi::where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy('tanggal');

        // Tentukan batas hari penulisan PDF sesuai aturan controller pusat
        $hariMax = ($bulan == \Carbon\Carbon::now()->month && $tahun == \Carbon\Carbon::now()->year)
            ? \Carbon\Carbon::now()->day
            : \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;

        $detailAbsensi = [];
        for ($d = 1; $d <= $hariMax; $d++) {
            $tanggalObj = \Carbon\Carbon::create($tahun, $bulan, $d);

            if ($tanggalObj->isWeekday() && $tanggalObj->lte(\Carbon\Carbon::today())) {
                $formatTanggal = $tanggalObj->format('Y-m-d');

                if (isset($absensiData[$formatTanggal])) {
                    $detailAbsensi[] = $absensiData[$formatTanggal];
                } else {
                    $detailAbsensi[] = (object)[
                        'tanggal' => $formatTanggal,
                        'status'  => 'Tidak Hadir'
                    ];
                }
            }
        }

        $detailMisi = Pengerjaan::with('misi')
            ->where('id_karyawan', $idKaryawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $detailTugas = Pengumpulan::with('tugas')
            ->where('id_karyawan', $idKaryawan)
            ->whereHas('tugas', fn($q) => $q->where('bulan', $bulan))
            ->get();

        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulan];

        $data = [
            'hasilAkhir'    => $hasilAkhir,
            'namaBulan'     => $namaBulan,
            'tahun'         => $tahun,
            'detailAbsensi' => $detailAbsensi,
            'detailMisi'    => $detailMisi,
            'detailTugas'   => $detailTugas
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('karyawan.akun.cetak_pdf', $data);
        return $pdf->download("Laporan_Kinerja_{$hasilAkhir->karyawan->nama}_{$namaBulan}_{$tahun}.pdf");
    }

    // ── METHOD CETAK EXCEL: Perbaikan Anti ERR_INVALID_RESPONSE ──
    public function cetakExcel(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with(['karyawan.divisi'])
            ->first();

        if (!$hasilAkhir) {
            return back()->with('error', 'Data laporan tidak ditemukan.');
        }

        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulan];

        $filename = "Laporan_Kinerja_{$hasilAkhir->karyawan->nama}_{$namaBulan}_{$tahun}.xls";

        // Menggunakan standard Response Laravel agar browser langsung mendownload tanpa crash
        return response()->view('karyawan.akun.cetak_excel', compact('hasilAkhir', 'namaBulan', 'tahun'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename={$filename}")
            ->header('Cache-Control', 'private, max-age=0, must-revalidate')
            ->header('Pragma', 'public');
    }
}
