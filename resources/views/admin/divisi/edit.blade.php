@extends('layouts.admin')

@section('content')
<div class="px-4">
    <div class="max-w-xl mx-auto mt-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Edit Data Divisi</h1>
                <p class="text-blue-100 text-[11px] opacity-80">ID: #{{ $divisi->id_divisi }} - {{ $divisi->nama_divisi }}</p>
            </div>

            <div class="p-6 pt-4">
                <form action="/data-divisi/edit/{{ $divisi->id_divisi }}" method="POST" class="space-y-3" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nama Divisi</label>
                        <input type="text" name="nama_divisi" value="{{ old('nama_divisi', $divisi->nama_divisi) }}" required placeholder="Masukkan nama divisi"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Tempat Kerja</label>
                        <div class="relative">
                            <select name="tempat_kerja" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none cursor-pointer">
                                <option value="kantor" {{ old('tempat_kerja', $divisi->tempat_kerja) == 'kantor' ? 'selected' : '' }}>Kantor</option>
                                <option value="lapangan" {{ old('tempat_kerja', $divisi->tempat_kerja) == 'lapangan' ? 'selected' : '' }}>Lapangan</option>
                                <option value="lab" {{ old('tempat_kerja', $divisi->tempat_kerja) == 'lab' ? 'selected' : '' }}>Lab</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 pt-4">
                        <button type="submit" class="w-full bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-md">
                            Simpan Perubahan
                        </button>
                        <a href="/data-divisi" class="w-full text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all">
                            Batalkan
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection