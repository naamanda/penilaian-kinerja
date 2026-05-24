@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header --}}
    <div class="flex items-center gap-2 mb-6">
        <a href="/reward-atasan" class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Program Reward Baru</h1>
    </div>

    {{-- Form Box --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="/reward-atasan/tambah" method="POST">
            @csrf

            {{-- Input Nama Reward --}}
            <div class="mb-5">
                <label for="nama_reward" class="block text-sm font-semibold text-gray-700 mb-2">Nama Reward</label>
                <input type="text" name="nama_reward" id="nama_reward" value="{{ old('nama_reward') }}" placeholder="Contoh: Karyawan Terbaik Bulan Ini / Karyawan Terdisiplin" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none transition focus:border-[#1e3f7c] focus:ring-1 focus:ring-[#1e3f7c]" required>
                @error('nama_reward')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Jenis Hadiah Berdasarkan Kriteria Sistem --}}
            <div class="mb-5">
                <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-2">Kriteria Penilaian (Jenis)</label>
                <select name="jenis" id="jenis" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none transition focus:border-[#1e3f7c] focus:ring-1 focus:ring-[#1e3f7c]" required>
                    <option value="">-- Pilih Sistem Kualifikasi Hadiah --</option>
                    <option value="ranking" {{ old('jenis') == 'ranking' ? 'selected' : '' }}>🏆 RANKING (Mengambil Tiga Peringkat Nilai Akhir Tertinggi)</option>
                    <option value="disiplin" {{ old('jenis') == 'disiplin' ? 'selected' : '' }}>✨ DISIPLIN (Mengambil Seluruh Karyawan Ber-nilai Disiplin Sempurna 100)</option>
                    <option value="termalas" {{ old('jenis') == 'termalas' ? 'selected' : '' }}>⚠️ TERMALAS (Mengambil Karyawan dengan Predikat D / Perlu Evaluasi)</option>
                </select>
                @error('jenis')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Nominal Uang --}}
            <div class="mb-6">
                <label for="nominal" class="block text-sm font-semibold text-gray-700 mb-2">Nominal Uang (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-medium text-sm">Rp</span>
                    <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}" placeholder="Contoh: 300000" min="0" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 outline-none transition focus:border-[#1e3f7c] focus:ring-1 focus:ring-[#1e3f7c]" required>
                </div>
                @error('nominal')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-50">
                <a href="/reward-atasan" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1e3f7c] hover:bg-blue-900 text-white text-sm font-semibold transition shadow-sm">Simpan Reward</button>
            </div>
        </form>
    </div>

</div>
@endsection