@extends('layouts.karyawan')

@section('content')

<div class="bg-[#1e3f7c] px-5 pt-10 pb-6 flex items-center justify-between">
    <img src="{{ asset('assets/logo.png') }}" alt="logo" class="w-8 h-8 object-contain">
    <span class="text-white font-bold text-lg">LifeSync</span>
    <div class="w-8"></div>
</div>

<div class="px-4 py-4 pb-24">
    <p class="font-bold text-gray-800 text-base mb-1">Aktivitas Misi</p>
    <p class="text-xs text-gray-400 mb-4">
        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
    </p>

    @forelse($pengerjaan as $p)
    <a href="/aktivitas-misi/{{ $p->id_pengerjaan }}"
       class="block bg-white rounded-2xl shadow-sm p-4 mb-3 border border-gray-100">

        <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">{{ $p->misi->nama_misi }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $p->misi->deskripsi }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    ⏰ {{ \Carbon\Carbon::parse($p->misi->waktu_mulai)->format('H:i') }}
                    – {{ \Carbon\Carbon::parse($p->misi->waktu_selesai)->format('H:i') }}
                </p>
            </div>

            <div>
                @if($p->status == 'belum_mengerjakan')
                    @if($p->sudah_lewat)
                        <span class="text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded-full">Terlewat</span>
                    @elseif($p->bisa_upload)
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Kerjakan</span>
                    @else
                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full">Belum Mulai</span>
                    @endif
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
            <span class="text-xs text-yellow-500 font-semibold">🏅 {{ $p->misi->poin }} poin</span>
            @if($p->poin_didapat > 0)
                <span class="text-xs text-green-600 font-semibold">+{{ $p->poin_didapat }} didapat</span>
            @endif
        </div>

    </a>
    @empty
    <div class="text-center text-gray-400 text-sm mt-10">
        Tidak ada misi hari ini.
    </div>
    @endforelse
</div>

@endsection