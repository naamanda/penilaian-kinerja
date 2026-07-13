@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">
<link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="/approve-misi" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengerjaan Misi</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Foto --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Foto Bukti</h2>
                </div>
                <div class="p-4">
                    @if($misi->foto)
                        <img src="{{ asset('uploads/misi/' . $misi->foto) }}"
                             alt="Foto Bukti"
                             class="w-full rounded-xl object-cover aspect-square shadow-sm border border-gray-100">
                    @else
                        <div class="w-full aspect-square rounded-xl bg-gray-100 flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-sm font-medium">Tidak ada foto</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Informasi Misi</h2>

                    {{-- FIXED: Badge status lengkap --}}
                    @if($misi->status == 'menunggu')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">Menunggu</span>
                    @elseif($misi->status == 'disetujui')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Disetujui</span>
                    @elseif($misi->status == 'terlambat')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">Terlambat</span>
                    @elseif($misi->status == 'ditolak')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">Ditolak</span>
                    @else
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-500 border border-gray-100">Belum Mengerjakan</span>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">

                    {{-- Nama Karyawan --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nama Karyawan</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $misi->karyawan->nama ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Nama Misi --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nama Misi</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $misi->misi->nama_misi ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-align-right"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Deskripsi</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $misi->misi->deskripsi ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Waktu Upload --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Waktu Upload</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $misi->waktu_upload ? \Carbon\Carbon::parse($misi->waktu_upload)->format('H:i') . ' WIB' : '-' }}</p>
                        </div>
                    </div>

                    {{-- Poin --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-regular fa-star"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Poin</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $misi->poin_didapat }} / {{ $misi->misi->poin ?? 0 }} poin</p>
                        </div>
                    </div>

                </div>

                {{-- ✅ FIXED: Tombol approve/reject hanya muncul untuk status yang belum diproses --}}
                @if($misi->status == 'menunggu')
                <div class="px-6 py-4 flex gap-3 border-t border-gray-100">
                    <form action="/approve-misi/{{ $misi->id_pengerjaan }}/approve" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Setujui
                        </button>
                    </form>
                    <form action="/approve-misi/{{ $misi->id_pengerjaan }}/reject" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>
                    </form>
                </div>
                @elseif($misi->status == 'ditolak')
                {{-- ✅ Karyawan sudah ditolak, tampilkan info agar admin tau bisa diproses ulang --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    <p class="text-xs text-rose-500 font-medium">Misi ini telah ditolak. Menunggu karyawan upload ulang.</p>
                </div>
                @elseif($misi->status == 'terlambat')
                {{-- ✅ Info terlambat tapi sudah disetujui --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    <p class="text-xs text-orange-500 font-medium">Misi disetujui namun dikirim melewati batas waktu toleransi.</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection