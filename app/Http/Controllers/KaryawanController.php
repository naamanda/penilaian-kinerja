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
            'password' => md5($request->password),
            'id_role' => $request->id_role,
            'id_divisi' => $request->id_divisi,
            'tanggal_bergabung'  => \Carbon\Carbon::today()->toDateString(),
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

        $divisiLama = $karyawan->id_divisi;

        $karyawan->nama     = $request->nama;
        $karyawan->username = $request->username;
        if ($request->password) {
            $karyawan->password = md5($request->password);
        }
        $karyawan->id_role  = $request->id_role;
        $karyawan->id_divisi = $request->id_divisi;
        $karyawan->save();

        if ($request->id_divisi != $divisiLama) {
            $minggu = \Carbon\Carbon::now()->weekOfMonth;
            $bulan  = \Carbon\Carbon::now()->month;

            $tugasAktif = \App\Models\Tugas::where('id_divisi', $request->id_divisi)
                ->where('minggu', $minggu)
                ->where('bulan', $bulan)
                ->get();

            foreach ($tugasAktif as $tugas) {
                \App\Models\Pengumpulan::firstOrCreate(
                    ['id_tugas'    => $tugas->id_tugas, 'id_karyawan' => $karyawan->id_karyawan],
                    ['status'      => 'belum_mengerjakan', 'poin_didapat' => 0, 'file' => null]
                );
            }
        }

        return redirect('/data-karyawan');
    }

    public function destroy($id)
    {
        Karyawan::findOrFail($id)->delete();
        return redirect('/data-karyawan')->with('success', 'Karyawan berhasil dihapus!');
    }
}
