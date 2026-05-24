@extends('layouts.admin')

@section('content')

<div class="pt-2 px-6 pb-6">

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="bg-[#1e3f7c] px-6 py-4">
                <h1 class="text-lg font-bold text-white">Tambah Tugas Baru</h1>
                <p class="text-blue-100 text-[11px] opacity-80 mt-0.5">
                    Pastikan data tugas yang dimasukkan sudah benar.
                </p>
            </div>

            <div class="p-6">

                <form action="/kelola-tugas/tambah" method="POST" class="space-y-4">
                    @csrf

                    {{-- Nama Tugas --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest">
                            Nama Tugas
                        </label>
                        <input type="text"
                            name="nama_tugas"
                            value="{{ old('nama_tugas') }}"
                            placeholder="Masukkan nama tugas..."
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                            rows="3"
                            placeholder="Masukkan deskripsi tugas..."
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none resize-none transition-all">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Divisi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest">
                            Divisi
                        </label>
                        <select name="id_divisi" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisi as $d)
                                <option value="{{ $d->id_divisi }}"
                                    {{ old('id_divisi') == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Detail Penjadwalan & Poin --}}
                    <div style="display: grid; grid-template-columns: 80px 80px 1fr 100px; gap: 12px; align-items: end;">

                        {{-- Minggu Ke- --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest whitespace-nowrap">
                                Minggu Ke-
                            </label>
                            <input type="number"
                                name="minggu"
                                value="{{ old('minggu') }}"
                                min="1"
                                max="5"
                                placeholder="1"
                                required
                                style="-moz-appearance: textfield;"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                        {{-- Bulan Ke- --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest whitespace-nowrap">
                                Bulan Ke-
                            </label>
                            <input type="number"
                                name="bulan"
                                value="{{ date('n') }}"
                                readonly
                                style="-moz-appearance: textfield;"
                                class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                        {{-- Batas Waktu (Deadline) --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest">
                                Batas Waktu (Deadline)
                            </label>
                            <input type="datetime-local"
                                name="deadline"
                                value="{{ old('deadline') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#1e3f7c] focus:border-[#1e3f7c] outline-none transition-all">
                        </div>

                        {{-- Poin Tugas --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 tracking-widest whitespace-nowrap">
                                Poin Tugas
                            </label>
                            <input type="number"
                                name="poin"
                                value="100"
                                readonly
                                style="-moz-appearance: textfield;"
                                class="w-full bg-gray-100 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>

                    </div>

                    {{-- Actions Button --}}
                    <div class="flex gap-3 pt-2">
                        <a href="/kelola-tugas"
                            class="flex-1 text-center bg-gray-50 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition-all text-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="flex-1 bg-[#1e3f7c] text-white font-semibold py-2.5 rounded-xl hover:bg-blue-900 transition-all shadow-sm text-sm">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection