<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Divisi;
use App\Models\Pengumpulan;
use Carbon\Carbon;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bulan  = (int) $request->get('bulan', date('n'));
        $tahun  = (int) $request->get('tahun', date('Y'));

        $query = Tugas::with('divisi')
            ->where('bulan', $bulan)
            ->whereYear('deadline', $tahun)
            ->orderBy('id_tugas', 'desc');

        if ($request->filled('id_divisi')) {
            $query->where('id_divisi', $request->id_divisi);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tugas', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        $data   = $query->paginate(5)->withQueryString();
        $divisi = Divisi::all();

        return view('admin.tugas.kelola.index', compact('data', 'divisi', 'search'));
    }

    public function create()
    {
        $divisi = Divisi::all();
        return view('admin.tugas.kelola.create', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'minggu'     => 'required|integer|min:1|max:5',
            'bulan'      => 'required|integer|min:1|max:12',
            'deadline'   => 'required|date',
            'poin'       => 'required|integer|min:1',
            'id_divisi'  => 'required|exists:divisi,id_divisi',
        ]);

        $tugas = Tugas::create([
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'minggu'     => $request->minggu,
            'bulan'      => $request->bulan,
            'deadline'   => $request->deadline,
            'poin'       => $request->poin,
            'id_divisi'  => $request->id_divisi,
        ]);

        $karyawanList = \App\Models\Karyawan::where('id_divisi', $request->id_divisi)->get();

        foreach ($karyawanList as $karyawan) {
            Pengumpulan::create([
                'id_tugas'     => $tugas->id_tugas,
                'id_karyawan'  => $karyawan->id_karyawan,
                'status'       => 'belum_mengerjakan',
                'poin_didapat' => 0,
                'file'         => null,
            ]);
        }

        return redirect('/kelola-tugas');
    }

    public function edit($id)
    {
        $tugas  = Tugas::findOrFail($id);
        $divisi = Divisi::all();
        return view('admin.tugas.kelola.edit', compact('tugas', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
            'minggu'     => 'required|integer|min:1|max:5',
            'bulan'      => 'required|integer|min:1|max:12',
            'deadline'   => 'required|date',
            'poin'       => 'required|integer|min:1',
            'id_divisi'  => 'required|exists:divisi,id_divisi',
        ]);

        $tugas = Tugas::findOrFail($id);
        $divisiLama = $tugas->id_divisi;

        $tugas->update([
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'minggu'     => $request->minggu,
            'bulan'      => $request->bulan,
            'deadline'   => $request->deadline,
            'poin'       => $request->poin,
            'id_divisi'  => $request->id_divisi,
        ]);

        // Jika divisi berubah, hapus pengumpulan lama & generate untuk divisi baru
        if ($request->id_divisi != $divisiLama) {
            Pengumpulan::where('id_tugas', $id)->delete();

            $karyawanList = \App\Models\Karyawan::where('id_divisi', $request->id_divisi)->get();
            foreach ($karyawanList as $karyawan) {
                Pengumpulan::create([
                    'id_tugas'     => $tugas->id_tugas,
                    'id_karyawan'  => $karyawan->id_karyawan,
                    'status'       => 'belum_mengerjakan',
                    'poin_didapat' => 0,
                    'file'         => null,
                ]);
            }
        } else {
            // REVISI LOGIKA UTAMA: Jika divisi tidak berubah tetapi tengat waktu diperpanjang oleh admin,
            // kembalikan status karyawan yang sempat terkunci di 'tidak_mengerjakan' menjadi 'belum_mengerjakan'
            if (Carbon::parse($request->deadline)->gt(Carbon::now())) {
                Pengumpulan::where('id_tugas', $id)
                    ->where('status', 'tidak_mengerjakan')
                    ->update(['status' => 'belum_mengerjakan']);
            }
        }

        return redirect('/kelola-tugas');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        Pengumpulan::where('id_tugas', $id)->delete();
        $tugas->delete();

        return back();
    }
}
