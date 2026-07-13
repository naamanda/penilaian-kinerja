@extends('layouts.atasan')

@section('content')
<div class="pt-4 px-8 pb-8 bg-gray-50/50 min-h-screen font-sans">

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-200/60 pb-5 gap-4">
        <div>
            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
            <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight">Dashboard Atasan</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">
                Pemantauan performa karyawan untuk periode <span class="text-[#1e3f7c] font-bold">{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</span>.
            </p>
        </div>

        {{-- Filter Dropdown Menu --}}
        <form action="/dashboard-atasan" method="GET" class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-400">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <select name="bulan" onchange="this.form.submit()" class="text-xs bg-transparent border-none outline-none font-bold text-gray-700 cursor-pointer focus:ring-0 px-1">
                @for ($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
            </select>
            <span class="text-gray-300 font-light">|</span>
            <select name="tahun" onchange="this.form.submit()" class="text-xs bg-transparent border-none outline-none font-bold text-gray-700 cursor-pointer focus:ring-0 px-1">
                @for ($y=date('Y')-2; $y<=date('Y')+2; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
            </select>
        </form>
    </div>

    {{-- 4-Columns Stats Card Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total Karyawan</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalKaryawan }}</h3>
                <span class="text-[11px] font-medium text-gray-400">Karyawan</span>
            </div>
        </div>

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total Divisi</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalDivisi }}</h3>
                <span class="text-[11px] font-medium text-gray-400">Divisi</span>
            </div>
        </div>

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Kasus Pelanggaran</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-2xl font-black text-rose-600 tracking-tight">{{ $totalPelanggaran }}</h3>
                <span class="text-[11px] font-semibold text-rose-500 flex items-center gap-1">
                    <span class="w-1 h-1 rounded-full bg-rose-500 inline-block animate-pulse"></span> Status SP
                </span>
            </div>
        </div>

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Estimasi Dana Reward</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-xl font-black text-emerald-600 tracking-tight">Rp {{ number_format($totalDanaReward, 0, ',', '.') }}</h3>
                <span class="text-[11px] font-medium text-gray-400">Bulan ini</span>
            </div>
        </div>

    </div>

    {{-- Judul Tabel Mandiri Terpisah --}}
    <div class="mb-4">
        <h2 class="text-xl font-extrabold text-gray-950 tracking-tight flex items-center gap-2">
            <span></span> Peringkat Nilai Karyawan
        </h2>
    </div>

    {{-- Kotak Tabel dengan Header Berwarna Navy Seirama Layout (#1e3f7c) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/90 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white uppercase text-[11px] font-bold tracking-wider">
                        <th class="px-8 py-4 text-center w-24">Rank</th>
                        <th class="px-6 py-4 text-left">Nama Karyawan</th>
                        <th class="px-6 py-4 text-left">Divisi</th>
                        <th class="px-6 py-4 text-center">Nilai Akhir</th>
                        <th class="px-8 py-4 text-center w-36">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($peringkatKaryawan as $index => $hk)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        {{-- Badge Medali/Rank --}}
                        <td class="px-8 py-4 text-center font-bold">
                            @if($index == 0)
                            <i class="fa-solid fa-1"></i>
                            @elseif($index == 1)
                            <i class="fa-solid fa-2"></i>
                            @elseif($index == 2)
                            <i class="fa-solid fa-3"></i>
                            @else
                            <span class="text-gray-400 font-bold text-xs">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 text-sm">{{ $hk['nama'] }}</td>
                        <td class="px-6 py-4 text-gray-500 font-medium text-xs">{{ $hk['divisi'] }}</td>
                        <td class="px-6 py-4 text-center font-black text-gray-800 tracking-tight text-sm">
                            {{ number_format($hk['nilai_akhir'], 2) }}
                        </td>
                        {{-- Tag Label Predikat --}}
                        <td class="px-8 py-4 text-center">
                            @if(in_array($hk['predikat'], ['A', 'AB']))
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                {{ $hk['predikat'] }}
                            </span>
                            @elseif(in_array($hk['predikat'], ['B', 'C']))
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                {{ $hk['predikat'] }}
                            </span>
                            @else
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200/60">
                                {{ $hk['predikat'] }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                            <svg class="w-8 h-8 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.945a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-xs mt-1 text-gray-400">Tidak ada data performa karyawan pada bulan ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection