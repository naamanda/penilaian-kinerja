@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="/absensi" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Absensi</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Foto --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Foto Absensi</h2>
                </div>
                <div class="p-4">
                    @if($absensi->foto)
                    <img src="{{ url('uploads/absensi/' . $absensi->foto) }}" alt="Foto Absensi" class="w-full rounded-xl object-cover aspect-square shadow-sm border border-gray-100">
                    @else
                    <div class="w-full aspect-square rounded-xl bg-gray-100 flex flex-col items-center justify-center text-gray-400 gap-2">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm font-medium">Tidak ada foto</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Info + Peta --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Info Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Informasi Absensi</h2>
                    {{-- Ganti bagian badge status agar mengambil dari data $absensi --}}
                    @if($absensi->status == 'menunggu')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">Menunggu</span>
                    @elseif($absensi->status == 'disetujui')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Disetujui</span>
                    @elseif($absensi->status == 'terlambat')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">Terlambat</span>
                    @elseif($absensi->status == 'belum_mengerjakan')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Belum Mengerjakan</span>
                    @elseif($absensi->status == 'ditolak')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">Ditolak</span>
                    @endif
                </div>
                <div class="divide-y divide-gray-100">

                    {{-- Nama --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nama Karyawan</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $absensi->karyawan->nama ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Tanggal</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Waktu --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Waktu Absen</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $absensi->waktu ? \Carbon\Carbon::parse($absensi->waktu)->format('H:i') . ' WIB' : '-' }}</p>
                        </div>
                    </div>

                    {{-- Koordinat --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Koordinat Lokasi</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5 font-mono">{{ $absensi->latitude }}, {{ $absensi->longitude }}</p>
                            <a href="https://maps.google.com/?q={{ $absensi->latitude }},{{ $absensi->longitude }}"
                                target="_blank"
                                class="text-xs text-[#1e3f7c] hover:underline font-medium mt-0.5 inline-block">
                                Buka di Google Maps →
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Peta --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Titik Lokasi Absensi</h2>
                </div>
                <div class="p-4">
                    @if($absensi->latitude && $absensi->longitude)
                    <div class="rounded-xl overflow-hidden border border-gray-100">
                        <iframe
                            width="100%"
                            height="280"
                            frameborder="0"
                            scrolling="no"
                            src="https://maps.google.com/maps?q={{ $absensi->latitude }},{{ $absensi->longitude }}&hl=id&z=17&output=embed">
                        </iframe>
                    </div>
                    @else
                    <div class="h-48 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                        <p class="text-sm font-medium">Data lokasi tidak tersedia</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection