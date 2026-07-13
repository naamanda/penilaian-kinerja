@extends('layouts.admin')

@section('content')
{{-- 
  Menggunakan container w-full tanpa max-w-3xl 
  agar tampilan form melebar penuh serasi dengan halaman data lainnya.
--}}
<div class="w-full min-h-[calc(100vh-120px)] flex items-center justify-center px-4">
    <div class="w-full">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">

            {{-- Header --}}
            <div class="bg-[#1e3f7c] px-6 py-4">
                <h1 class="text-lg font-bold text-white">Edit Tugas</h1>
                <p class="text-blue-100 text-[11px] opacity-80 mt-0.5">
                    ID: #{{ $tugas->id_tugas }} &mdash; {{ $tugas->nama_tugas }}
                </p>
            </div>

            <div class="p-6">

                <form action="/kelola-tugas/edit/{{ $tugas->id_tugas }}"
                      method="POST"
                      class="space-y-4"
                      autocomplete="off">

                    @csrf
                    @method('PUT')

                    {{-- Nama Tugas --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">
                            Nama Tugas
                        </label>
                        <input type="text"
                            name="nama_tugas"
                            value="{{ old('nama_tugas', $tugas->nama_tugas) }}"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                            rows="3"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none resize-none transition-all">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    </div>

                    {{-- Divisi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">
                            Divisi
                        </label>
                        <select name="id_divisi" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ old('id_divisi', $tugas->id_divisi) == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Detail Penjadwalan & Poin (Grid Kustom Menggunakan Tailwind Utility) --}}
                    <div class="grid grid-cols-2 md:grid-cols-12 gap-4 items-end">

                        {{-- Minggu Ke- --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest whitespace-nowrap">
                                Minggu Ke-
                            </label>
                            <input type="number"
                                name="minggu"
                                value="{{ old('minggu', $tugas->minggu) }}"
                                min="1"
                                max="5"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                        {{-- Bulan Ke- --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest whitespace-nowrap">
                                Bulan Ke-
                            </label>
                            <input type="number"
                                name="bulan"
                                value="{{ old('bulan', $tugas->bulan) }}"
                                min="1"
                                max="12"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                        {{-- Batas Waktu (Deadline) --}}
                        <div class="col-span-2 md:col-span-6">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest">
                                Batas Waktu (Deadline)
                            </label>
                            <input type="datetime-local"
                                name="deadline"
                                value="{{ old('deadline', $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('Y-m-d\TH:i') : '') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                        </div>

                        {{-- Poin Tugas --}}
                        <div class="col-span-2 md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 ml-1 tracking-widest whitespace-nowrap">
                                Poin Tugas
                            </label>
                            <input type="number"
                                name="poin"
                                value="{{ old('poin', $tugas->poin) }}"
                                min="1"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                    </div>

                    {{-- Actions Button --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <a href="/kelola-tugas"
                            class="flex-1 order-2 sm:order-1 text-center bg-gray-50 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-100 transition-all border border-gray-200 text-sm">
                            Batalkan
                        </a>
                        <button type="submit"
                            class="flex-1 order-1 sm:order-2 bg-[#1e3f7c] text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-md text-sm">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
@endsection