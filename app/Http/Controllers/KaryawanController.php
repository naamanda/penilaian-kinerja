<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\Divisi;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = Karyawan::with(['role', 'divisi'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            })
            ->paginate(5)
            ->withQueryString();

        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $roles = Role::all();
        $divisi = Divisi::all();
        return view('admin.karyawan.create', compact('roles', 'divisi'));
    }

    public function store(Request $request)
    {
        Karyawan::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => md5($request->password), // tambah md5
            'id_role' => $request->id_role,
            'id_divisi' => $request->id_divisi,
        ]);
        return redirect('/data-karyawan');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $roles = Role::all();
        $divisi = Divisi::all();
        return view('admin.karyawan.edit', compact('karyawan', 'roles', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::find($id);
        $karyawan->nama = $request->nama;
        $karyawan->username = $request->username;
        if ($request->password) {
            $karyawan->password = md5($request->password); // tambah md5
        }
        $karyawan->id_role = $request->id_role;
        $karyawan->id_divisi = $request->id_divisi;
        $karyawan->save();

        return redirect('/data-karyawan');
    }

    public function destroy($id)
    {
        Karyawan::findOrFail($id)->delete();
        return redirect('/data-karyawan')->with('success', 'Karyawan berhasil dihapus!');
    }
}
