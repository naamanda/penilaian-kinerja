<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DashboardAtasanController extends Controller
{
    protected HasilAkhirController $hasilAkhir;

    public function __construct()
    {
        $this->hasilAkhir = new HasilAkhirController();
    }

    public function index(Request $request)
    {
        // Ambil filter bulan & tahun, default ke bulan dan tahun berjalan saat ini
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // 1. Total Seluruh Karyawan (Role 2)
        $totalKaryawan = Karyawan::where('id_role', 2)->count();

        // 2. Total Divisi
        $totalDivisi = Divisi::count();

        // Variabel penampung untuk kalkulasi realtime
        $totalPelanggaran = 0;
        $totalDanaReward = 0;
        $allKaryawanData = collect();

        // Ambil semua karyawan aktif untuk dihitung nilainya satu per satu secara realtime
        $karyawans = Karyawan::with('divisi')->where('id_role', 2)->get();

        foreach ($karyawans as $k) {
            // Hitung nilai & pelanggaran realtime menggunakan method milik HasilAkhirController
            $dataNilai = $this->hasilAkhir->hitungNilai($k->id_karyawan, $bulan, $tahun);
            
            $nilaiAkhir = $dataNilai['akhir'];
            $statusSP = $dataNilai['pelanggaran']['status']; // Menghasilkan 'SP1', 'SP2', atau 'aman'

            // 3. Hitung akumulasi Kasus Pelanggaran (Jika statusnya mendapat SP1 atau SP2)
            if (in_array($statusSP, ['SP1', 'SP2'])) {
                $totalPelanggaran++;
            }

            // 4. Hitung estimasi reward berdasarkan nominal kelayakan nilai
            $allKaryawanData->push([
                'karyawan'    => $k,
                'nama'        => $k->nama,
                'divisi'      => $k->divisi->nama_divisi ?? '-',
                'nilai_akhir' => $nilaiAkhir,
                'predikat'    => $dataNilai['predikat']['kode']
            ]);
        }

        // 5. Urutkan peringkat karyawan berdasarkan Nilai Akhir tertinggi (Realtime Leaderboard)
        $peringkatKaryawan = $allKaryawanData->sortByDesc('nilai_akhir')->values();

        // Contoh perhitungan dana reward dari database atau alokasi statis
        $totalDanaReward = Reward::whereHas('hasilakhir', function($q) use ($bulan, $tahun) {
            $q->where('bulan', $bulan)->where('tahun', $tahun);
        })->sum('nominal');

        return view('atasan.dashboard', compact(
            'bulan', 
            'tahun', 
            'totalKaryawan', 
            'totalDivisi', 
            'peringkatKaryawan', 
            'totalPelanggaran', 
            'totalDanaReward'
        ));
    }
}