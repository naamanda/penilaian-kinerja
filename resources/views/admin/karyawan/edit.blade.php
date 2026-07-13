@extends('layouts.admin')

@section('content')
{{-- Card akan otomatis melebar penuh ke kanan-kiri mengikuti wrapper utama --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
    <div class="bg-[#1e3f7c] px-6 py-3">
        <h1 class="text-lg font-bold text-white">Edit Data Karyawan</h1>
        <p class="text-blue-100 text-[11px] opacity-80">ID: #{{ $karyawan->id_karyawan }} - {{ $karyawan->nama }}</p>
    </div>

    <div class="p-6 pt-4">
        <form action="/data-karyawan/edit/{{ $karyawan->id_karyawan }}" method="POST" class="space-y-3" autocomplete="off">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ $karyawan->nama }}" required placeholder="Masukkan nama"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Username</label>
                    <input type="text" name="username" value="{{ $karyawan->username }}" required placeholder="Username" autocomplete="new-password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Ganti Password <span class="lowercase font-normal text-[9px]">(Opsional)</span></label>
                    <input type="password" name="password" placeholder="Isi jika perlu" autocomplete="new-password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Role</label>
                    <div class="relative">
                        <select name="id_role" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none cursor-pointer">
                            @foreach($roles as $r)
                                <option value="{{ $r->id_role }}" {{ $karyawan->id_role == $r->id_role ? 'selected' : '' }}>{{ $r->nama_role }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Divisi</label>
                    <div class="relative">
                        <select name="id_divisi" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none cursor-pointer">
                            @foreach($divisi as $d)
                                <option value="{{ $d->id_divisi }}" {{ $karyawan->id_divisi == $d->id_divisi ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" class="w-full bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-md">
                    Update Data Karyawan
                </button>
                <a href="/data-karyawan" class="w-full text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all">
                    Batalkan
                </a>
            </div>
        </form>
    </div>
</div>
@endsection