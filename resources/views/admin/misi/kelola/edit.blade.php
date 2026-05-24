@extends('layouts.admin')

@section('content')

<div class="px-4">

    <div class="max-w-3xl mx-auto mt-0">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="bg-[#1e3f7c] px-6 py-3">
                <h1 class="text-lg font-bold text-white">Edit Misi</h1>
                <p class="text-blue-100 text-[11px] opacity-80">
                    ID: #{{ $misi->id_misi }} - {{ $misi->nama_misi }}
                </p>
            </div>

            <div class="p-6 pt-4">

                <form action="/kelola-misi/edit/{{ $misi->id_misi }}"
                      method="POST"
                      class="space-y-3"
                      autocomplete="off">

                    @csrf
                    @method('PUT')

                    {{-- Nama Misi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">
                            Nama Misi
                        </label>
                        <input type="text"
                            name="nama_misi"
                            value="{{ old('nama_misi', $misi->nama_misi) }}"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                            rows="2"
                            required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none resize-none transition-all">{{ old('deskripsi', $misi->deskripsi) }}</textarea>
                    </div>

                    {{-- Poin, Waktu Mulai & Waktu Selesai (Grid 3 Kolom) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Poin --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">
                                Poin
                            </label>
                            <input type="number"
                                name="poin"
                                value="{{ old('poin', $misi->poin) }}"
                                min="1"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                        </div>

                        {{-- Waktu Mulai --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">
                                Waktu Mulai
                            </label>
                            <input type="time"
                                name="waktu_mulai"
                                value="{{ old('waktu_mulai', $misi->waktu_mulai ? \Carbon\Carbon::parse($misi->waktu_mulai)->format('H:i') : '') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                        </div>

                        {{-- Waktu Selesai --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">
                                Waktu Selesai
                            </label>
                            <input type="time"
                                name="waktu_selesai"
                                value="{{ old('waktu_selesai', $misi->waktu_selesai ? \Carbon\Carbon::parse($misi->waktu_selesai)->format('H:i') : '') }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-[#1e3f7c] outline-none transition-all">
                        </div>

                    </div>

                    {{-- Button Actions (Diselaraskan layout horizontal/flex demi konsistensi) --}}
                    <div class="flex gap-3 pt-4">
                        <a href="/kelola-misi"
                            class="flex-1 text-center bg-gray-50 text-gray-600 font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition-all">
                            Batalkan
                        </a>

                        <button type="submit"
                            class="flex-1 bg-[#1e3f7c] text-white font-semibold py-2.5 rounded-xl hover:bg-blue-900 transition-all shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection