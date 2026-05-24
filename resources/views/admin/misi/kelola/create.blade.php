@extends('layouts.admin')

@section('content')

<div class="px-4">

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Tambah Misi Baru</h1>
                <p class="text-blue-100 text-[11px] opacity-80">
                    Pastikan data yang dimasukkan sudah benar.
                </p>
            </div>

            <div class="p-5">

                <form action="/kelola-misi/tambah" method="POST" class="space-y-3">
                    @csrf

                    {{-- Nama Misi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-widest">
                            Nama Misi
                        </label>
                        <input type="text"
                            name="nama_misi"
                            value="{{ old('nama_misi') }}"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-widest">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                            rows="2"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none resize-none">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Poin & Waktu (Grid 3 Kolom agar responsif dan rapi) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Poin --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-widest">
                                Poin
                            </label>
                            <input type="number"
                                name="poin"
                                value="{{ old('poin') }}"
                                min="1"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                        </div>

                        {{-- Waktu Mulai --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-widest">
                                Waktu Mulai
                            </label>
                            <input type="time"
                                name="waktu_mulai"
                                value="{{ old('waktu_mulai') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                        </div>

                        {{-- Waktu Selesai --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 tracking-widest">
                                Waktu Selesai
                            </label>
                            <input type="time"
                                name="waktu_selesai"
                                value="{{ old('waktu_selesai') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none">
                        </div>

                    </div>

                    {{-- Button --}}
                    <div class="flex gap-3 pt-4">
                        <a href="/kelola-misi"
                            class="flex-1 text-center bg-gray-50 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition">
                            Batal
                        </a>

                        <button type="submit"
                            class="flex-1 bg-[#1e3f7c] text-white font-semibold py-2.5 rounded-xl hover:bg-blue-900 transition shadow-md">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection