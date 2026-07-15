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

        // Gunakan DB Transaction agar jika ada satu proses yang gagal, 
        // kita bisa menangkap (catch) pesan error aslinya.
        \DB::beginTransaction();

        try {
            // 1. Simpan misi
            $misi = Misi::create([
                'nama_misi'     => $request->nama_misi,
                'deskripsi'     => $request->deskripsi,
                'poin'          => $request->poin,
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
            ]);

            $today = \Carbon\Carbon::today()->toDateString();
            $karyawanList = \App\Models\Karyawan::where('id_role', 2)->get();

            // Lakukan dump data karyawan dulu untuk memastikan data id_karyawan tidak null
            if ($karyawanList->isEmpty()) {
                throw new \Exception("Daftar karyawan dengan id_role 2 kosong!");
            }

            // 2. Coba buat pengerjaan
            foreach ($karyawanList as $k) {
                // Pastikan id_karyawan tidak null sebelum insert
                if (!$k->id_karyawan) {
                    throw new \Exception("Karyawan bernama {$k->nama_karyawan} tidak memiliki id_karyawan!");
                }

                \App\Models\Pengerjaan::create([
                    'id_karyawan'  => $k->id_karyawan,
                    'id_misi'      => $misi->id_misi,
                    'tanggal'      => $today,
                    'status'       => 'belum_mengerjakan',
                    'poin_didapat' => 0,
                ]);
            }

            \DB::commit();

            return redirect('/kelola-misi')->with('success', 'Misi berhasil ditambahkan.');
        } catch (\Exception $e) {
            \DB::rollBack();

            // Tampilkan error asli dari SQL/Laravel ke layar Anda!
            dd([
                'Pesan Error' => $e->getMessage(),
                'Line Error'  => $e->getLine(),
                'File Error'  => $e->getFile()
            ]);
        }
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
