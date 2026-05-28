<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PelanggaranController extends Controller
{
    protected HasilAkhirController $hasilAkhir;

    public function __construct()
    {
        $this->hasilAkhir = new HasilAkhirController();
    }

    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('n'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Ambil semua karyawan aktif
        $karyawans = Karyawan::with('divisi')->where('id_role', 2)->get();

        // Ambil data pelanggaran yang sudah ada di DB (untuk file SP & tanggal_sp)
        $existingPelanggaran = Pelanggaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('id_karyawan'); // index by id_karyawan untuk lookup cepat

        $pelanggarans = new Collection();

        foreach ($karyawans as $k) {
            $dataNilai = $this->hasilAkhir->hitungNilai($k->id_karyawan, $bulan, $tahun);

            $poinData   = $dataNilai['pelanggaran'];
            $totalPoin  = $poinData['total_poin']       ?? 0;
            $terlambat  = $poinData['terlambat']         ?? 0;
            $tidakKerja = $poinData['tidak_mengerjakan'] ?? 0;
            $status     = $poinData['status'];

            $statusUpper = strtoupper($status === 'aman' ? 'AMAN' : $status);

            $existing = $existingPelanggaran->get($k->id_karyawan);

            // ✅ Auto-create record jika SP1/SP2 tapi belum ada di DB
            if (!$existing && in_array($statusUpper, ['SP1', 'SP2'])) {
                $existing = Pelanggaran::create([
                    'id_karyawan' => $k->id_karyawan,
                    'bulan'       => $bulan,
                    'tahun'       => $tahun,
                    'file_sp'     => null,
                    'tanggal_sp'  => null,
                ]);
                // Tambahkan ke collection agar lookup berikutnya tetap konsisten
                $existingPelanggaran->put($k->id_karyawan, $existing);
            }

            $item = new \stdClass();
            $item->id_pelanggaran         = $existing->id_pelanggaran ?? null;
            $item->id_karyawan            = $k->id_karyawan;
            $item->karyawan               = $k;
            $item->total_terlambat        = $terlambat;
            $item->total_tidakmengerjakan = $tidakKerja;
            $item->total_poinpl           = $totalPoin;
            $item->status                 = $statusUpper;
            $item->file_sp                = $existing->file_sp    ?? null;
            $item->tanggal_sp             = $existing->tanggal_sp ?? null;

            $pelanggarans->push($item);
        }

        // Urutkan: poin tertinggi di atas
        $pelanggarans = $pelanggarans->sortByDesc('total_poinpl')->values();

        return view('atasan.pelanggaran.index', compact('pelanggarans', 'bulan', 'tahun'));
    }

    public function uploadSp(Request $request, $id)
    {
        $request->validate([
            'file_sp' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Cari atau buat record pelanggaran berdasarkan id_pelanggaran
        $pelanggaran = Pelanggaran::findOrFail($id);

        if ($request->hasFile('file_sp')) {
            if ($pelanggaran->file_sp && file_exists(public_path('storage/sp_signed/' . $pelanggaran->file_sp))) {
                unlink(public_path('storage/sp_signed/' . $pelanggaran->file_sp));
            }

            $file     = $request->file('file_sp');
            $filename = 'SP_SIGNED_' . $pelanggaran->id_karyawan . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/sp_signed/'), $filename);

            $pelanggaran->update([
                'file_sp'    => $filename,
                'tanggal_sp' => now()->format('Y-m-d'),
            ]);
        }

        return redirect()->back()->with('success', 'Arsip file SP bertandatangan berhasil disimpan.');
    }

    public function deleteSp($id)
    {
        $pelanggaran = \App\Models\Pelanggaran::findOrFail($id);

        if ($pelanggaran->file_sp && Storage::exists('public/sp_signed/' . $pelanggaran->file_sp)) {
            Storage::delete('public/sp_signed/' . $pelanggaran->file_sp);
        }

        $pelanggaran->update([
            'file_sp' => null
        ]);

        return redirect()->back();
    }
}
