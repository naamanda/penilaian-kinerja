@extends('layouts.karyawan')

@section('content')

{{-- Header --}}
<div class="bg-[#1e3f7c] px-5 pt-6 pb-8">
    <div class="flex items-center justify-center gap-3 mb-1">
        <img src="{{ asset('assets/logo.png') }}" alt="logo" class="w-10 h-10 object-contain">
        <span class="text-white font-bold text-2xl tracking-wide">LifeSync</span>
    </div>
</div>

<div class="px-4 py-4 pb-24">
    <p class="font-bold text-gray-800 text-base mb-1">Tugas Mingguan</p>
    <p class="text-xs text-gray-400 mb-4">
        Minggu ke-{{ \Carbon\Carbon::now()->weekOfMonth }}
        — {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}
    </p>

    @forelse($pengumpulan as $p)
    <a href="/tugas-mingguan/{{ $p->id_pengumpulan }}"
        class="block bg-white rounded-2xl shadow-sm p-4 mb-3 border border-gray-100">

        <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">{{ $p->tugas->nama_tugas }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $p->tugas->deskripsi }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    📅 Deadline: {{ \Carbon\Carbon::parse($p->tugas->deadline)->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="shrink-0">
                {{-- REVISI: Bersihkan status lama 'belum_mengumpulkan' dan tambahkan handle status 'tidak_mengerjakan' --}}
                @if($p->status == 'belum_mengerjakan')
                @if($p->sudah_lewat)
                <span class="text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded-full">Terlewat</span>
                @else
                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Kerjakan</span>
                @endif
                @elseif($p->status == 'tidak_mengerjakan')
                <span class="text-xs bg-red-50 text-red-500 border border-red-100 px-2 py-1 rounded-full">❌ Pelanggaran</span>
                @elseif($p->status == 'menunggu')
                <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full">Menunggu</span>
                @elseif($p->status == 'disetujui')
                <span class="text-xs bg-emerald-100 text-emerald-600 px-2 py-1 rounded-full">✓ Disetujui</span>
                @elseif($p->status == 'terlambat')
                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">Terlambat</span>
                @elseif($p->status == 'ditolak')
                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Ditolak</span>
                @endif
            </div>
        </div>

        <div class="mt-2 flex items-center justify-between">
            <span class="text-xs text-yellow-500 font-semibold">🏅 {{ $p->tugas->poin }} poin</span>
            @if($p->poin_didapat > 0)
            <span class="text-xs text-green-600 font-semibold">+{{ $p->poin_didapat }} didapat</span>
            @endif
        </div>

    </a>
    @empty
    <div class="text-center text-gray-400 text-sm mt-10">
        Tidak ada tugas minggu ini.
    </div>
    @endforelse
</div>

@endsection