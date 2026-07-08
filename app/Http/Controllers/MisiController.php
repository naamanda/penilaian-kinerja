<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Misi;
use App\Models\Pengerjaan;

class MisiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $data = Misi::when($search, function ($query) use ($search) {
            $query->where('nama_misi', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%');
        })
            ->orderBy('id_misi', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('admin.misi.kelola.index', compact('data', 'search'));
    }

    public function create()
    {
        return view('admin.misi.kelola.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_misi'     => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'poin'          => 'required|integer|min:1',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
        ]);

        Misi::create([
            'nama_misi'     => $request->nama_misi,
            'deskripsi'     => $request->deskripsi,
            'poin'          => $request->poin,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
        ]);

        return redirect('/kelola-misi')->with('success', 'Misi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $misi = Misi::findOrFail($id);
        return view('admin.misi.kelola.edit', compact('misi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_misi'     => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'poin'          => 'required|integer|min:1',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
        ]);

        $misi = Misi::findOrFail($id);
        $misi->update([
            'nama_misi'     => $request->nama_misi,
            'deskripsi'     => $request->deskripsi,
            'poin'          => $request->poin,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
        ]);

        return redirect('/kelola-misi')->with('success', 'Misi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $misi = Misi::findOrFail($id);
        Pengerjaan::where('id_misi', $id)->delete();
        $misi->delete();

        return back()->with('success', 'Misi berhasil dihapus.');
    }
}
