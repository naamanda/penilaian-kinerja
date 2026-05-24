@extends('layouts.atasan')

@section('content')
<div class="pt-4 px-8 pb-8 bg-gray-50/50 min-h-screen font-sans">

    {{-- Header Section --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-200/60 pb-5 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight">Dashboard Atasan</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">
                Pemantauan performa karyawan untuk periode <span class="text-[#1e3f7c] font-bold">{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</span>.
            </p>
        </div>
        
        {{-- Filter Dropdown Menu --}}
        <form action="/dashboard-atasan" method="GET" class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl shadow-sm border border-gray-200">
            <div class="text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
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
                <div class="w-7 h-7 bg-blue-50 text-[#1e3f7c] rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total Karyawan</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalKaryawan }}</h3>
                <span class="text-[11px] font-medium text-gray-400">Karyawan aktif</span>
            </div>
        </div>

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <div class="w-7 h-7 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 21V5.25A2.25 2.25 0 0017.25 3H6.75A2.25 2.25 0 004.5 5.25V21m15 0h-15m15 0h-1.5v-3A2.25 2.25 0 0015.75 15H13.5M4.5 21H6m0 0V12m0 9h12"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total Divisi</p>
            </div>
            <div class="flex items-baseline justify-between mt-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $totalDivisi }}</h3>
                <span class="text-[11px] font-medium text-gray-400">Unit organisasi</span>
            </div>
        </div>

        <div class="bg-white p-5 h-28 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 text-gray-400">
                <div class="w-7 h-7 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                    </svg>
                </div>
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
                <div class="w-7 h-7 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.214-.145A3.75 3.75 0 0112.465 15h.07a3.75 3.75 0 011.527 3.423l-.112.634m-4.903-4.577a3.75 3.75 0 002.513 3.423h.07a3.75 3.75 0 001.527-3.423l-.112-.634m-4.903 0C8.552 11.722 9.48 11.25 10.5 11.25h1c1.02 0 1.948.472 2.457 1.218M12 3v3m0 12v3"></path>
                    </svg>
                </div>
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
            <span>🏆</span> Peringkat Nilai Karyawan
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
                                <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">🥇 1</span>
                            @elseif($index == 1)
                                <span class="inline-flex items-center bg-slate-100 text-slate-700 border border-slate-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">🥈 2</span>
                            @elseif($index == 2)
                                <span class="inline-flex items-center bg-orange-50 text-orange-800 border border-orange-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">🥉 3</span>
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
                            <p class="text-base">📭</p>
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