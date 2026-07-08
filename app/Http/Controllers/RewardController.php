<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\HasilAkhir;
use Illuminate\Http\Request;

class RewardController extends Controller
{

    public function index(Request $request)
    {
        // Mengambil bulan dan tahun aktif dari request filter, default ke bulan berjalan saat ini
        $bulanAktif = $request->input('bulan', date('n'));
        $tahunAktif = $request->input('tahun', date('Y'));

        $search = $request->input('search');

        // Saring data reward berdasarkan bulan dan tahun yang ada di dalam relasi hasilakhir
        $query = Reward::whereHas('hasilakhir', function ($query) use ($bulanAktif, $tahunAktif) {
            $query->where('bulan', $bulanAktif)
                ->where('tahun', $tahunAktif);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_reward', 'LIKE', "%{$search}%")
                    ->orWhere('jenis', 'LIKE', "%{$search}%");
            });
        }

        $reward = $query->paginate(10)->withQueryString();

        return view('atasan.reward.index', compact('reward', 'bulanAktif', 'tahunAktif'));
    }

    public function create()
    {
        return view('atasan.reward.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'jenis'       => 'required|in:ranking,disiplin',
            'nominal'     => 'required|numeric|min:0',
        ]);

        // 1. Buat data reward terlebih dahulu
        $reward = Reward::create($request->only(['nama_reward', 'jenis', 'nominal']));

        // Ini agar reward yang baru dibuat langsung mendapatkan 'id_hasilakhir' sesuai bulan berjalan
        return $this->detail($reward->id_reward);
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
        if (str_contains($kriteriaNama, 'ranking 1') || str_contains($kriteriaNama, 'peringkat 1') || str_contains($kriteriaNama, 'rank 1')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(0)->take(1)->get();
        } elseif (str_contains($kriteriaNama, 'ranking 2') || str_contains($kriteriaNama, 'peringkat 2') || str_contains($kriteriaNama, 'rank 2')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(1)->take(1)->get();
        } elseif (str_contains($kriteriaNama, 'ranking 3') || str_contains($kriteriaNama, 'peringkat 3') || str_contains($kriteriaNama, 'rank 3')) {
            $pemenang = $queryHasil->orderByDesc('nilai_akhir')->skip(2)->take(1)->get();
        } elseif ($reward->jenis === 'disiplin' || str_contains($kriteriaNama, 'disiplin')) {
            $pemenang = $queryHasil->orderByDesc('nilai_kedisiplinan')->take(1)->get();
        }

        if ($pemenang->isNotEmpty()) {
            $reward->id_hasilakhir = $pemenang->first()->id_hasilakhir;
            $reward->save();
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
            'jenis'       => 'required|in:ranking,disiplin',
            'nominal'     => 'required|numeric|min:0',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update($request->only(['nama_reward', 'jenis', 'nominal']));

        return redirect('/reward-atasan');
    }

    /**
     * Hapus Kategori Reward
     */
    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return redirect('/reward-atasan');
    }
}
