<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab    = $request->input('tab', 'menunggu'); // menunggu | disetujui | ditolak | semua

        $query = Izin::with('karyawan');

        if ($tab !== 'semua') {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%{$search}%"));
        }

        $data = $query->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('admin.izin.index', [
            'data' => $data,
            'tab'  => $tab,
        ]);
    }

    public function show($id)
    {
        $izin = Izin::with(['karyawan', 'absensi'])->findOrFail($id);
        return view('admin.izin.detail', compact('izin'));
    }

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $izin = Izin::findOrFail($id);
            $izin->status = 'disetujui';
            $izin->save();

            if ($izin->id_absensi) {
                Absensi::where('id_absensi', $izin->id_absensi)
                    ->update(['status' => 'izin']);
            }

            return back()->with('success', 'Pengajuan izin disetujui.');
        });
    }

    public function reject($id)
    {
        return DB::transaction(function () use ($id) {
            $izin = Izin::findOrFail($id);
            $izin->status = 'ditolak';
            $izin->save();

            // Izin ditolak diperlakukan sama seperti tidak hadir
            if ($izin->id_absensi) {
                Absensi::where('id_absensi', $izin->id_absensi)
                    ->update(['status' => 'tidak_hadir']);
            }

            return back()->with('success', 'Pengajuan izin ditolak.');
        });
    }

    public function destroy($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->file_izin && file_exists(public_path('uploads/izin/' . $izin->file_izin))) {
            unlink(public_path('uploads/izin/' . $izin->file_izin));
        }

        $izin->delete();

        return back()->with('success', 'Data izin berhasil dihapus.');
    }
}

