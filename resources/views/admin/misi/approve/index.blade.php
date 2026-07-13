@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Approve Misi Harian</h1>

    {{-- 1. Statistik Cards (4 Kotak) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-rose-500">
            <p class="text-xs text-gray-500 font-bold uppercase">Belum Mengerjakan</p>
            <p class="text-2xl font-black text-rose-600">{{ $stat['belum'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-blue-500">
            <p class="text-xs text-gray-500 font-bold uppercase">Menunggu Approve</p>
            <p class="text-2xl font-black text-blue-600">{{ $stat['menunggu'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-orange-500">
            <p class="text-xs text-gray-500 font-bold uppercase">Total Terlambat</p>
            <p class="text-2xl font-black text-orange-600">{{ $stat['terlambat'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-xs text-gray-500 font-bold uppercase">Total Disetujui</p>
            <p class="text-2xl font-black text-emerald-600">{{ $stat['disetujui'] }}</p>
        </div>

    </div>

    {{-- 2. Tab Navigation --}}
    <div class="flex items-center border-b border-gray-200 mb-6 gap-8">
        <a href="?tab=antrean" class="pb-4 px-2 text-sm font-bold transition-all {{ $tab == 'antrean' ? 'border-b-2 border-[#1e3f7c] text-[#1e3f7c]' : 'text-gray-400 hover:text-gray-600' }}">
            PENDING APPROVE
        </a>
        <a href="?tab=belum_mengerjakan" class="pb-4 px-2 text-sm font-bold transition-all {{ $tab == 'belum_mengerjakan' ? 'border-b-2 border-[#1e3f7c] text-[#1e3f7c]' : 'text-gray-400 hover:text-gray-600' }}">
            BELUM MENGERJAKAN
        </a>
        <a href="?tab=selesai" class="pb-4 px-2 text-sm font-bold transition-all {{ $tab == 'selesai' ? 'border-b-2 border-[#1e3f7c] text-[#1e3f7c]' : 'text-gray-400 hover:text-gray-600' }}">
            RIWAYAT SELESAI
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-4 p-3 bg-emerald-100 text-emerald-700 rounded-xl text-sm font-bold">✓ {{ session('success') }}</div>
    @endif

    {{-- 3. Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[#1e3f7c] text-white text-xs uppercase tracking-widest">
                    <th class="px-6 py-4">Nama Karyawan</th>
                    <th class="px-6 py-4">Misi</th>
                    <th class="px-6 py-4">Waktu Upload</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900">{{ $p->karyawan->nama ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">ID: {{ $p->karyawan->id_karyawan ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $p->misi->nama_misi ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $p->waktu_upload ? \Carbon\Carbon::parse($p->waktu_upload)->format('H:i') . ' WIB' : '---' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($p->status == 'menunggu')
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black bg-amber-100 text-amber-700 uppercase">Menunggu</span>
                        @elseif($p->status == 'ditolak')
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black bg-rose-100 text-rose-700 uppercase">Ditolak</span>
                        @elseif($p->status == 'disetujui')
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">Disetujui</span>
                        @elseif($p->status == 'terlambat')
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black bg-orange-100 text-orange-700 uppercase">Terlambat</span>
                        @else
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 uppercase">Belum Mengerjakan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tab == 'antrean')
                        <a href="/approve-misi/{{ $p->id_pengerjaan }}" class="bg-[#1e3f7c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-900 transition">
                            Detail
                        </a>
                        @else
                        <a href="/approve-misi/{{ $p->id_pengerjaan }}" class="text-gray-400 hover:text-[#1e3f7c]">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Data tidak ditemukan pada kategori ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $data->links() }}
    </div>
</div>
@endsection