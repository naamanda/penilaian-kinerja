@extends('layouts.atasan')

@section('content')
<div class="px-4">
    {{-- mt-0 untuk benar-benar memposisikan card di paling atas --}}
    <div class="max-w-xl mx-auto mt-0"> 
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header dibuat lebih ringkas (py-3) --}}
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Tambah Program Reward</h1>
                <p class="text-blue-100 text-[11px] opacity-80">Pastikan kriteria dan nominal hadiah yang dimasukkan sudah sesuai.</p>
            </div>

            <div class="p-6 pt-4"> {{-- pt-4 agar konten form lebih naik ke atas --}}
                <form action="/reward-atasan/tambah" method="POST" class="space-y-3" autocomplete="off">
                    @csrf
                    
                    {{-- Nama Reward --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nama Reward</label>
                        <input type="text" name="nama_reward" value="{{ old('nama_reward') }}" required placeholder="Contoh: Reward Karyawan Terbaik I"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                        @error('nama_reward')
                            <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Kualifikasi (Jenis) --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Kualifikasi</label>
                            <div class="relative">
                                <select name="jenis" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>-- Pilih Kriteria --</option>
                                    <option value="rank_1" {{ old('jenis') == 'rank_1' ? 'selected' : '' }}>🎯 RANK 1</option>
                                    <option value="rank_2" {{ old('jenis') == 'rank_2' ? 'selected' : '' }}>🎯 RANK 2</option>
                                    <option value="rank_3" {{ old('jenis') == 'rank_3' ? 'selected' : '' }}>🎯 RANK 3</option>
                                    <option value="disiplin" {{ old('jenis') == 'disiplin' ? 'selected' : '' }}>🎯 DISIPLIN</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('jenis')
                                <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nominal Hadiah --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nominal Hadiah (Rp)</label>
                            <input type="number" name="nominal" value="{{ old('nominal') }}" required min="0" placeholder="Masukkan nominal angka saja"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                            @error('nominal')
                                <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-col gap-2 pt-4">
                        {{-- Tombol Utama --}}
                        <button type="submit" class="w-full bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-[#152c58] transition-all shadow-md text-sm tracking-wide">
                            Simpan Program Reward
                        </button>
                        {{-- Tombol Batalkan --}}
                        <a href="/reward-atasan" class="w-full text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all text-sm">
                            Batalkan
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection