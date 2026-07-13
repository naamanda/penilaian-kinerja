@extends('layouts.atasan')

@section('content')
{{-- 
  Menggunakan container w-full tanpa max-w-xl
  agar tampilan form melebar penuh serasi dengan halaman data lainnya.
--}}
<div class="w-full min-h-[calc(100vh-120px)] flex items-center justify-center px-4">
    <div class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
            
            {{-- Header Card --}}
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Edit Program Reward</h1>
                <p class="text-blue-100 text-[11px] opacity-80">ID Reward: #{{ $reward->id_reward }} - {{ $reward->nama_reward }}</p>
            </div>

            {{-- Form Body --}}
            <div class="p-6 pt-4">
                <form action="/reward-atasan/edit/{{ $reward->id_reward }}" method="POST" class="space-y-3" autocomplete="off">
                    @csrf
                    @method('PUT')

                    {{-- Nama Reward --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Nama Reward</label>
                        <input type="text" name="nama_reward" value="{{ old('nama_reward', $reward->nama_reward) }}" required placeholder="Contoh: Reward Karyawan Terbaik I"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                        @error('nama_reward')
                        <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Kualifikasi / Jenis --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Kualifikasi</label>
                            <div class="relative">
                                <select name="jenis" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none appearance-none cursor-pointer">
                                    {{-- Mengecek angka 1 atau romawi i pada nama reward --}}
                                    <option value="ranking" {{ old('jenis', $reward->jenis) == 'ranking' && (str_contains(strtolower($reward->nama_reward), '1') || str_contains(strtolower($reward->nama_reward), 'i')) ? 'selected' : '' }}>🎯 RANK 1</option>

                                    {{-- Mengecek angka 2 atau romawi ii pada nama reward --}}
                                    <option value="ranking" {{ old('jenis', $reward->jenis) == 'ranking' && (str_contains(strtolower($reward->nama_reward), '2') || str_contains(strtolower($reward->nama_reward), 'ii')) ? 'selected' : '' }}>🎯 RANK 2</option>

                                    {{-- Mengecek angka 3 atau romawi iii pada nama reward --}}
                                    <option value="ranking" {{ old('jenis', $reward->jenis) == 'ranking' && (str_contains(strtolower($reward->nama_reward), '3') || str_contains(strtolower($reward->nama_reward), 'iii')) ? 'selected' : '' }}>🎯 RANK 3</option>

                                    {{-- Murni mengecek tipe disiplin --}}
                                    <option value="disiplin" {{ old('jenis', $reward->jenis) == 'disiplin' ? 'selected' : '' }}>🎯 DISIPLIN</option>
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
                            <input type="number" name="nominal" value="{{ old('nominal', $reward->nominal) }}" required min="0" placeholder="Contoh: 1500000"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                            @error('nominal')
                            <p class="text-xs text-rose-500 mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <a href="/reward-atasan" class="flex-1 order-2 sm:order-1 text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all border border-gray-200 text-sm">
                            Batalkan
                        </a>
                        <button type="submit" class="flex-1 order-1 sm:order-2 bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-md text-sm tracking-wide">
                            Simpan Perubahan Reward
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection