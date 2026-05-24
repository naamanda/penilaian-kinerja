<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Divisi;
use App\Models\Pengumpulan;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = Tugas::with('divisi')->orderBy('id_tugas', 'desc');

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

        Tugas::create([
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'minggu'     => $request->minggu,
            'bulan'      => $request->bulan,
            'deadline'   => $request->deadline,
            'poin'       => $request->poin,
            'id_divisi'  => $request->id_divisi,
        ]);

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
        $tugas->update([
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'minggu'     => $request->minggu,
            'bulan'      => $request->bulan,
            'deadline'   => $request->deadline,
            'poin'       => $request->poin,
            'id_divisi'  => $request->id_divisi,
        ]);

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
