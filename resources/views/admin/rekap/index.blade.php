@extends('layouts.admin')

@section('content')
<div class="p-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Rekap Kinerja Karyawan (Admin)</h2>
            <p class="text-xs text-gray-400">Total akumulasi kehadiran, misi harian, dan tugas mingguan karyawan.</p>
        </div>

        <a href="{{ route('admin.rekap.download', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
            class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download Rekap PDF
        </a>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm mb-6">
        <form action="{{ route('admin.rekap.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Bulan</label>
                <select name="bulan" class="p-2.5 border border-gray-200 rounded-xl text-sm bg-white min-w-[140px] focus:outline-none focus:border-blue-500">
                    @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month((int)$b)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tahun</label>
                <select name="tahun" class="p-2.5 border border-gray-200 rounded-xl text-sm bg-white min-w-[100px] focus:outline-none focus:border-blue-500">
                    @foreach(range(date('Y')-2, date('Y')) as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1 justify-end h-full pt-5">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[11px] font-bold border-b border-gray-100">
                        <th class="px-6 py-4">Nama Karyawan</th>
                        <th class="px-6 py-4 text-center">Total Hadir</th>
                        {{-- DIHAPUS: Total Terlambat --}}
                        <th class="px-6 py-4 text-center">Misi Selesai</th>
                        <th class="px-6 py-4 text-center">Tugas Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($rekapKaryawan as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ $item->nama }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                                {{ $item->total_hadir }} Hari
                            </span>
                        </td>
                        {{-- DIHAPUS: td total_terlambat --}}
                        <td class="px-6 py-4 text-center font-bold text-blue-600">
                            {{ $item->misi_count }} Misi
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-purple-600">
                            {{ $item->tugas_count }} Tugas
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            {{-- colspan dikurangi jadi 4 --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto mb-3 text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18a2.25 2.25 0 0 1 2.25 2.25v4.25A2.25 2.25 0 0 1 19.5 21h-15A2.25 2.25 0 0 1 2.25 19.5V15.75a2.25 2.25 0 0 1 2.25-2.25Zm0-4.5h18a2.25 2.25 0 0 0 2.25-2.25V5.25A2.25 2.25 0 0 0 19.5 3h-15A2.25 2.25 0 0 0 2.25 5.25v4.25A2.25 2.25 0 0 0 4.25 12Z" />
                            </svg>
                            <span class="text-sm">Belum ada data aktivitas pada periode ini.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection