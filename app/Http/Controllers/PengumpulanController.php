<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumpulan;
use Carbon\Carbon;

class PengumpulanController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'antrean');
        $search = $request->input('search');
        $query  = Pengumpulan::with(['tugas', 'karyawan']);

        if ($tab == 'antrean') {
            $query->whereIn('status', ['menunggu', 'ditolak'])
                ->orderByRaw("FIELD(status, 'menunggu', 'ditolak') ASC");
        } elseif ($tab == 'belum_mengerjakan') {
            $query->where('status', 'belum_mengerjakan');
        } elseif ($tab == 'selesai') {
            $query->whereIn('status', ['disetujui', 'terlambat']);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tugas', function ($q2) use ($search) {
                    $q2->where('nama_tugas', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        $data = $query->orderBy('tanggal_upload', 'desc')
            ->paginate(5)
            ->withQueryString();

        $stat = [
            'belum'     => Pengumpulan::where('status', 'belum_mengerjakan')->count(),
            'menunggu'  => Pengumpulan::where('status', 'menunggu')->count(),
            'terlambat' => Pengumpulan::where('status', 'terlambat')->count(),
            'disetujui' => Pengumpulan::where('status', 'disetujui')->count(),
        ];

        return view('admin.tugas.pengumpulan.index', compact('data', 'stat', 'tab', 'search'));
    }

    public function show($id)
    {
        $pengumpulan = Pengumpulan::with(['tugas', 'karyawan.divisi'])->findOrFail($id);
        return view('admin.tugas.pengumpulan.detail', compact('pengumpulan'));
    }

    public function approve($id)
    {
        $pengumpulan = Pengumpulan::with('tugas')->findOrFail($id);
        $tugas       = $pengumpulan->tugas;

        $waktuUpload = Carbon::parse($pengumpulan->waktu_upload);
        $deadline    = Carbon::parse($tugas->deadline);

        // ✅ Fix: lte = tepat waktu, sisanya = terlambat (sudah dicegah kalau lewat toleransi)
        if ($waktuUpload->lte($deadline)) {
            $pengumpulan->status       = 'disetujui';
            $pengumpulan->poin_didapat = $tugas->poin ?? 0;
        } else {
            $pengumpulan->status       = 'terlambat';
            $pengumpulan->poin_didapat = intval(($tugas->poin ?? 0) / 2);
        }

        $pengumpulan->save();

        return redirect('/approve-tugas?tab=antrean')->with('success', 'Tugas berhasil disetujui.');
    }

    public function reject($id)
    {
        $pengumpulan = Pengumpulan::findOrFail($id);
        $pengumpulan->update([
            'status'      => 'ditolak',
            'poin_didapat' => 0,
        ]);

        return redirect('/approve-tugas?tab=antrean')->with('success', 'Tugas ditolak. Menunggu karyawan upload ulang.');
    }

    public function lihatFile($id)
    {
        // Cari data pengumpulan berdasarkan ID tugas tanpa memfilter ID Karyawan
        $pengumpulan = Pengumpulan::where('id_pengumpulan', $id)->firstOrFail();

        // Arahkan path pencarian langsung ke folder public/uploads/tugas
        $path = public_path('uploads/tugas/' . $pengumpulan->file);

        // Cek apakah file fisik PDF benar-benar ada di folder tersebut
        if (!file_exists($path) || !$pengumpulan->file) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        // Tampilkan file PDF secara aman di tab baru browser admin
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
