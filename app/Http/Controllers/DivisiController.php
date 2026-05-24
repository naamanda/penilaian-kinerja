<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $divisi = Divisi::query() 
        ->when($request->search, function ($query) use ($request) {
                $query->where('nama_divisi', 'like', '%' . $request->search . '%')
                    ->orWhere('tempat_kerja', 'like', '%' . $request->search . '%');
            })
            ->paginate(5)
            ->withQueryString();
        return view('admin.divisi.index', compact('divisi'));
    }

    public function create()
    {
        return view('admin.divisi.create');
    }

    public function store(Request $request)
    {
        Divisi::create([
            'nama_divisi' => $request->nama_divisi,
            'tempat_kerja' => $request->tempat_kerja,
        ]);
        return redirect('/data-divisi');
    }

    public function edit($id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('admin.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, $id)
    {
        $divisi = Divisi::findOrFail($id);
        $divisi->nama_divisi = $request->nama_divisi;
        $divisi->tempat_kerja = $request->tempat_kerja;
        $divisi->save();
        return redirect('/data-divisi');
    }

    public function destroy($id)
    {
        Divisi::findOrFail($id)->delete();
        return redirect('/data-divisi');
    }
}