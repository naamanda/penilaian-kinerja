<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\HasilAkhir;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    /**
     * Tampilkan List Kategori Program Reward (Halaman Utama)
     */
    public function index(Request $request)
    {
        $reward = Reward::paginate(10);
        $bulanAktif = date('n');
        $tahunAktif = date('Y');

        return view('atasan.reward.index', compact('reward', 'bulanAktif', 'tahunAktif'));
    }

    /**
     * Tampilkan Halaman Formulir Tambah Program Reward Baru
     */
    public function create()
    {
        return view('atasan.reward.create');
    }

    /**
     * Proses Simpan Kategori Reward Baru ke Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'jenis'       => 'required|string|max:255', // rank_1, rank_2, rank_3, atau disiplin
            'nominal'     => 'required|numeric|min:0',
        ]);

        Reward::create($request->only(['nama_reward', 'jenis', 'nominal']));

        return redirect('/reward-atasan')->with('success', 'Kategori program reward baru berhasil ditambahkan.');
    }

    /**
     * Fitur Otomatisasi Pencarian Pemenang Berdasarkan Kriteria & Kalkulasi Live Sistem
     */
    public function detail($id)
    {
        $reward = Reward::findOrFail($id);

        $bulanAktif = date('n');
        $tahunAktif = date('Y');

        // 1. Jalankan kalkulasi internal agar tabel hasil_akhir terisi kembali
        $hasilAkhirController = new HasilAkhirController();
        $hasilAkhirController->executeGenerateInternal($bulanAktif, $tahunAktif);

        $queryHasil = HasilAkhir::with(['karyawan.divisi'])
            ->where('bulan', $bulanAktif)
            ->where('tahun', $tahunAktif);

        $pemenang = collect();
        $kriteriaNama = strtolower($reward->nama_reward);

        // 2. Tentukan pemenang berdasarkan ranking
        if (str_contains($kriteriaNama, 'ranking 1') || str_contains($kriteriaNama, 'peringkat 1')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(0)->take(1)->get();
        } elseif (str_contains($kriteriaNama, 'ranking 2') || str_contains($kriteriaNama, 'peringkat 2')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(1)->take(1)->get();
        } elseif (str_contains($kriteriaNama, 'ranking 3') || str_contains($kriteriaNama, 'peringkat 3')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(2)->take(1)->get();
        }

        // 3. KUNCI UTAMA: Update id_hasilakhir di tabel reward secara permanen ke database
        if ($pemenang->isNotEmpty()) {
            $reward->id_hasilakhir = $pemenang->first()->id_hasilakhir;
            $reward->save(); // Menyimpan perubahan ke tabel reward
        }

        return view('atasan.reward.detail', compact('reward', 'pemenang', 'bulanAktif', 'tahunAktif'));
    }

    /**
     * Tampilkan Halaman Formulir Edit Kategori Reward
     */
    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        return view('atasan.reward.edit', compact('reward'));
    }

    /**
     * Proses Ubah Data Kategori Reward
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'jenis'       => 'required|string|max:255',
            'nominal'     => 'required|numeric|min:0',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update($request->only(['nama_reward', 'jenis', 'nominal']));

        return redirect('/reward-atasan')->with('success', 'Kategori program reward berhasil diperbarui.');
    }

    /**
     * Hapus Kategori Reward
     */
    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return redirect('/reward-atasan')->with('success', 'Kategori program reward sukses dihapus.');
    }
}
