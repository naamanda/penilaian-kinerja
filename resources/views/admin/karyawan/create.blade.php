@extends('layouts.admin')

@section('content')
{{-- 
  Menghilangkan pembatasan lebar max-w-xl agar card bisa melebar penuh 
  mengikuti ruang kanan dan kiri yang tersedia secara responsif.
--}}
<div class="w-full min-h-[calc(100vh-120px)] flex items-center justify-center px-4">
    <div class="w-full"> {{-- Mengubah max-w-xl menjadi w-full penuh --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
            {{-- Header dibuat lebih ringkas (py-3) --}}
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Tambah Karyawan Baru</h1>
                <p class="text-blue-100 text-[11px] opacity-80">Pastikan data yang dimasukkan sudah benar.</p>
            </div>

            <div class="p-6 pt-4"> {{-- pt-4 agar konten form lebih naik ke atas --}}
                <form action="/data-karyawan/tambah" method="POST" class="space-y-3" autocomplete="off">
                    @csrf
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required placeholder="User baru" autocomplete="new-password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Password</label>
                            <input type="password" name="password" required placeholder="******" autocomplete="new-password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Role Akses</label>
                            <select name="id_role" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none">
                                <option value="">-- Pilih --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id_role }}" {{ old('id_role') == $r->id_role ? 'selected' : '' }}>{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Divisi Pekerjaan</label>
                            <select name="id_divisi" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none">
                                <option value="">-- Pilih --</option>
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id_divisi }}" {{ old('id_divisi') == $d->id_divisi ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 pt-4">
                        {{-- Tombol Utama --}}
                        <button type="submit" class="w-full bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-md">
                            Simpan Karyawan
                        </button>
                        {{-- Tombol Batalkan --}}
                        <a href="/data-karyawan" class="w-full text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all">
                            Batalkan
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection