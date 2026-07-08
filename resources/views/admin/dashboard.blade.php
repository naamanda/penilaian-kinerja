@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Total Karyawan</p>
                <p class="text-3xl font-bold text-[#1e3f7c]">{{ $totalKaryawan }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Total Divisi</p>
                <p class="text-3xl font-bold text-[#1e3f7c]">{{ $totalDivisi }}</p>
            </div>
        </div>
    </div>

    {{-- Kehadiran & Pending dalam 1 baris --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Kehadiran Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <p class="font-bold text-gray-700 text-sm mb-3">Kehadiran Hari Ini</p>
            @if($hariIniLibur)
            <div class="flex flex-col items-center justify-center py-4 text-center gap-2">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l.7 2.154a1 1 0 00.95.69h2.264c.969 0 1.371 1.24.588 1.81l-1.832 1.331a1 1 0 00-.364 1.118l.7 2.154c.3.921-.755 1.688-1.54 1.118l-1.832-1.331a1 1 0 00-1.176 0l-1.832 1.331c-.784.57-1.838-.197-1.539-1.118l.7-2.154a1 1 0 00-.364-1.118L5.597 7.581c-.783-.57-.38-1.81.588-1.81H8.45a1 1 0 00.95-.69l.7-2.154z" />
                </svg>
                <p class="text-sm font-semibold text-gray-400">Hari ini libur</p>
                <p class="text-xs text-gray-300">Tidak ada absensi hari ini</p>
            </div>
            @else
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between bg-emerald-50 rounded-xl px-4 py-2.5">
                    <p class="text-xs text-gray-500">Hadir</p>
                    <p class="text-lg font-bold text-emerald-600">{{ $hadirHariIni }}</p>
                </div>
                <div class="flex items-center justify-between bg-orange-50 rounded-xl px-4 py-2.5">
                    <p class="text-xs text-gray-500">Terlambat</p>
                    <p class="text-lg font-bold text-orange-500">{{ $terlambatHariIni }}</p>
                </div>
                <div class="flex items-center justify-between bg-rose-50 rounded-xl px-4 py-2.5">
                    <p class="text-xs text-gray-500">Tidak Hadir</p>
                    <p class="text-lg font-bold text-rose-500">{{ $tidakHadirHariIni }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Menunggu Persetujuan --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <p class="font-bold text-gray-700 text-sm mb-3">Menunggu Persetujuan</p>
            <div class="flex flex-col gap-2">
                <a href="/approve-misi?tab=antrean"
                    class="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-2.5 hover:bg-blue-100 transition">
                    <p class="text-xs text-gray-500">Misi Harian</p>
                    <p class="text-lg font-bold text-blue-600">{{ $menungguMisi }}</p>
                </a>
                <a href="/approve-tugas"
                    class="flex items-center justify-between bg-yellow-50 rounded-xl px-4 py-2.5 hover:bg-yellow-100 transition">
                    <p class="text-xs text-gray-500">Tugas Mingguan</p>
                    <p class="text-lg font-bold text-yellow-600">{{ $menungguTugas }}</p>
                </a>
            </div>
        </div>

    </div>

    {{-- Leaderboard --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 21h8M12 17v4M7 4h10v3a5 5 0 01-10 0V4zm10 1h2a2 2 0 010 4h-2M7 5H5a2 2 0 000 4h2" />
            </svg>
            <p class="font-bold text-gray-700">Leaderboard Bulan Ini</p>
        </div>
        <div class="flex flex-col gap-1 max-h-64 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
            @foreach($leaderboard as $i => $lb)
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition">
                <div class="w-7 text-center flex-shrink-0">
                    <div class="w-6 flex justify-center flex-shrink-0">
                        @if($i == 0)
                        <span class="w-5 h-5 flex items-center justify-center bg-yellow-400 text-white text-xs font-bold rounded-full">1</span>
                        @elseif($i == 1)
                        <span class="w-5 h-5 flex items-center justify-center bg-gray-300 text-gray-700 text-xs font-bold rounded-full">2</span>
                        @elseif($i == 2)
                        <span class="w-5 h-5 flex items-center justify-center bg-amber-600 text-white text-xs font-bold rounded-full">3</span>
                        @else
                        <span class="text-xs font-bold text-gray-400">{{ $i + 1 }}</span>
                        @endif
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-[#1e3f7c] flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($lb->nama, 0, 1)) }}</span>
                </div>
                <p class="flex-1 text-sm font-semibold text-gray-700">{{ $lb->nama }}</p>
                <span class="text-xs text-gray-400 hidden md:block">{{ $lb->divisi->nama_divisi ?? '-' }}</span>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="w-4 h-4 text-amber-500">
                        <path fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM9 10.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5h-4.5A.75.75 0 0 1 9 10.5Zm0 3a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5h-4.5A.75.75 0 0 1 9 13.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-bold text-gray-600">{{ $lb->total_nilai }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection