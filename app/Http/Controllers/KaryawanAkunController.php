<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\HasilAkhir;
use App\Models\Pelanggaran;
use App\Http\Controllers\HasilAkhirController;
use Illuminate\Support\Facades\Session;

class KaryawanAkunController extends Controller
{
    // Halaman Utama: Profil dan Menu Navigasi
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

        return view('karyawan.akun.index', compact('karyawan'));
    }

    // Sub-menu: Unduh Laporan Hasil
    public function unduh(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        // Ambil data filter bulan dan tahun
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        // Trigger kalkulasi hasil akhir otomatis
        $hasilAkhirCtrl = new HasilAkhirController();
        $hasilAkhirCtrl->executeGenerateInternal($bulan, $tahun);

        // Ambil hasil akhir berdasarkan filter
        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('karyawan.akun.unduh', compact('hasilAkhir', 'bulan', 'tahun', 'daftarBulan'));
    }

    // Sub-menu: Reward
    public function reward(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        // Trigger kalkulasi otomatis
        $hasilAkhirCtrl = new HasilAkhirController();
        $hasilAkhirCtrl->executeGenerateInternal($bulan, $tahun);

        // Ambil semua hasil akhir karyawan ini yang punya reward
        $daftarReward = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->with('reward')
            ->whereHas('reward')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
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
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
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

        // Hitung realtime
        $hasilAkhirCtrl = new HasilAkhirController();
        $dataNilai = $hasilAkhirCtrl->hitungNilai($idKaryawan, $bulan, $tahun);
        $pelanggaran = $dataNilai['pelanggaran'];

        // Ambil semua record pelanggaran dari DB (murni untuk dibaca/didownload filenya)
        $riwayat = Pelanggaran::where('id_karyawan', $idKaryawan)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('karyawan.akun.pelanggaran', compact(
            'pelanggaran',
            'riwayat',
            'bulan',
            'tahun',
            'daftarBulan'
        ));
    }

    // Method untuk menyiapkan data cetak PDF
    public function cetakPdf(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('karyawan')
            ->first();

        if (!$hasilAkhir) {
            return back()->with('error', 'Data laporan tidak ditemukan.');
        }

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulan];

        return view('karyawan.akun.cetak_pdf', compact('hasilAkhir', 'namaBulan', 'tahun'));
    }

    // Method untuk menyiapkan data cetak Excel
    public function cetakExcel(Request $request)
    {
        $idKaryawan = Session::get('id_karyawan');
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $hasilAkhir = HasilAkhir::where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with('karyawan')
            ->first();

        if (!$hasilAkhir) {
            return back()->with('error', 'Data laporan tidak ditemukan.');
        }

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulan];

        // Menggunakan header stream HTML ke Excel (Langsung otomatis download .xls tanpa library)
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Kinerja_{$namaBulan}_{$tahun}.xls");
        header("Cache-Control: private, max-age=0, must-revalidate");
        header("Pragma: public");

        return view('karyawan.akun.cetak_excel', compact('hasilAkhir', 'namaBulan', 'tahun'));
    }
}