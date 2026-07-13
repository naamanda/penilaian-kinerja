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
                <div class="flex items-center gap-1 mt-1 text-xs text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        {{ \Carbon\Carbon::parse($p->misi->waktu_mulai)->format('H:i') }}
                        – {{ \Carbon\Carbon::parse($p->misi->waktu_selesai)->format('H:i') }}
                    </span>
                </div>
            </div>

            <div class="shrink-0">
                {{-- WARNA BADGE REAL-TIME SESUAI REQUEST --}}
                @if($p->status == 'belum_mengerjakan')
                @if($p->bisa_upload)
                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">Kerjakan</span>
                @else
                {{-- ABU-ABU: Untuk yang belum masuk waktu mulai --}}
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full font-medium">Belum Mengerjakan</span>
                @endif
                @elseif($p->status == 'tidak_mengerjakan')
                {{-- MERAH: Jika waktu terlewat (Sinkron dengan data pelanggaran) --}}
                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-medium">Tidak Mengerjakan</span>
                @elseif($p->status == 'menunggu')
                <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full font-medium">Menunggu</span>
                @elseif($p->status == 'disetujui')
                {{-- HIJAU: Misi yang berhasil diselesaikan --}}
                <span class="text-xs bg-emerald-100 text-emerald-600 px-2 py-1 rounded-full font-medium">Disetujui</span>
                @elseif($p->status == 'terlambat')
                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full font-medium">Terlambat</span>
                @elseif($p->status == 'ditolak')
                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-medium">Ditolak</span>
                @endif
            </div>
        </div>

        <div class="mt-2 flex items-center justify-between">
            <span class="text-xs text-yellow-500 font-semibold">{{ $p->misi->poin }} poin</span>
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