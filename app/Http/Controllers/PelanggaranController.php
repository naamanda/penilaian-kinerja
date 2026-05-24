<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    /**
     * Monitoring Pelanggaran dari Sistem (Read-Only List)
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $pelanggarans = Pelanggaran::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('total_poinpl')
            ->get();

        return view('atasan.pelanggaran.index', compact('pelanggarans', 'bulan', 'tahun'));
    }

    /**
     * Atasan mengunggah file SP yang sudah dicetak dan ditandatangani karyawan (Validasi)
     */
    public function uploadSp(Request $request, $id)
    {
        $request->validate([
            'file_sp' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $pelanggaran = Pelanggaran::findOrFail($id);

        if ($request->hasFile('file_sp')) {
            // Hapus file lama jika ada
            if ($pelanggaran->file_sp && file_exists(public_path('storage/sp_signed/' . $pelanggaran->file_sp))) {
                unlink(public_path('storage/sp_signed/' . $pelanggaran->file_sp));
            }

            $file = $request->file('file_sp');
            $filename = 'SP_SIGNED_' . $pelanggaran->id_karyawan . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/sp_signed/'), $filename);

            $pelanggaran->update([
                'file_sp'    => $filename,
                'tanggal_sp' => now()->format('Y-m-d')
            ]);
        }

        return redirect()->back()->with('success', 'Arsip file SP bertandatangan berhasil disimpan.');
    }
}