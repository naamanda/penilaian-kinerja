<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\HasilAkhir;
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

        return view('karyawan.akun.unduh', compact('hasilAkhir', 'bulan', 'tahun', 'daftarBulan'));
    }

    // Sub-menu: Reward
    public function reward()
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        return view('karyawan.akun.reward');
    }

    // Sub-menu: Pelanggaran
    public function pelanggaran()
    {
        $idKaryawan = Session::get('id_karyawan');

        if (!$idKaryawan) {
            return redirect('/login');
        }

        return view('karyawan.akun.pelanggaran');
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

        // Menggunakan header stream HTML ke Excel (Langsung otomatis download .xls tanpa library)
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Kinerja_{$namaBulan}_{$tahun}.xls");
        header("Cache-Control: private, max-age=0, must-revalidate");
        header("Pragma: public");

        return view('karyawan.akun.cetak_excel', compact('hasilAkhir', 'namaBulan', 'tahun'));
    }
}
