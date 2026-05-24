@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="/approve-tugas?tab=antrean" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengumpulan Tugas</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- File Lampiran Dokumen Tugas --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Berkas Lampiran</h2>
                </div>
                <div class="p-5">
                    @if($pengumpulan->file)
                    <div class="rounded-xl border border-gray-100 bg-blue-50/40 p-4 flex flex-col items-center text-center gap-3">
                        <div class="w-14 h-14 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="w-full">
                            <p class="text-xs text-gray-400 font-medium truncate">Nama File</p>
                            <p class="text-sm font-bold text-gray-800 truncate px-2 mt-0.5" title="{{ $pengumpulan->file }}">
                                {{ $pengumpulan->file }}
                            </p>
                        </div>
                        <a href="/approve-tugas/{{ $pengumpulan->id_pengumpulan }}/file" target="_blank"
                            class="w-full bg-[#1e3f7c] hover:bg-blue-900 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh / Lihat File
                        </a>
                    </div>
                    @else
                    <div class="w-full aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center text-gray-400 gap-2 border border-dashed border-gray-200">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-400">Berkas tidak tersedia</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Detail Tugas & Karyawan --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Informasi Tugas</h2>

                    {{-- Badge Status Lengkap --}}
                    @if($pengumpulan->status == 'menunggu')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">Menunggu Persetujuan</span>
                    @elseif($pengumpulan->status == 'disetujui')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Disetujui</span>
                    @elseif($pengumpulan->status == 'terlambat')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">Disetujui (Terlambat)</span>
                    @elseif($pengumpulan->status == 'ditolak')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">Ditolak</span>
                    @else
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-500 border border-gray-100">Belum Mengerjakan</span>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">

                    {{-- Nama Karyawan & Divisi --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nama Karyawan</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">
                                {{ $pengumpulan->karyawan->nama ?? '-' }}
                                <span class="text-xs font-normal text-gray-400 ml-1">({{ $pengumpulan->karyawan->divisi->nama_divisi ?? '-' }})</span>
                            </p>
                        </div>
                    </div>

                    {{-- Nama Tugas --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Nama Tugas</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $pengumpulan->tugas->nama_tugas ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Batas Waktu (Deadline) --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Batas Waktu (Deadline)</p>
                            <p class="text-sm font-semibold text-rose-600 mt-0.5">
                                {{ $pengumpulan->tugas->deadline ? \Carbon\Carbon::parse($pengumpulan->tugas->deadline)->translatedFormat('d F Y - H:i') . ' WIB' : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Waktu Realisasi Upload --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Waktu Dikumpulkan Karyawan</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">
                                {{ $pengumpulan->tanggal_upload ? \Carbon\Carbon::parse($pengumpulan->tanggal_upload)->translatedFormat('d F Y') : '-' }}
                                @if($pengumpulan->waktu_upload)
                                Pukul {{ \Carbon\Carbon::parse($pengumpulan->waktu_upload)->format('H:i') }} WIB
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Poin Didapat --}}
                    <div class="px-6 py-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#1e3f7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Perolehan Poin</p>
                            <p class="text-sm font-bold text-[#1e3f7c] mt-0.5">
                                {{ $pengumpulan->poin_didapat ?? 0 }} <span class="text-gray-400 font-normal">/ {{ $pengumpulan->tugas->poin ?? 0 }} Max Poin</span>
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Action Button (Hanya jika status 'menunggu') --}}
                @if($pengumpulan->status == 'menunggu')
                <div class="px-6 py-4 flex gap-3 border-t border-gray-100 bg-gray-50/50">
                    <form action="/approve-tugas/{{ $pengumpulan->id_pengumpulan }}/approve" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui Tugas
                        </button>
                    </form>

                    <form action="/approve-tugas/{{ $pengumpulan->id_pengumpulan }}/reject" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 transition-all flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak & Suruh Re-upload
                        </button>
                    </form>
                </div>
                @elseif($pengumpulan->status == 'ditolak')
                <div class="px-6 py-4 border-t border-gray-100 bg-rose-50/30">
                    <p class="text-xs text-rose-600 font-bold flex items-center gap-1.5">
                        ⚠️ Laporan ini telah ditolak. Sistem menunggu karyawan mengunggah ulang dokumen revisi.
                    </p>
                </div>
                @elseif($pengumpulan->status == 'terlambat')
                <div class="px-6 py-4 border-t border-gray-100 bg-orange-50/30">
                    <p class="text-xs text-orange-600 font-bold flex items-center gap-1.5">
                        ⏰ Tugas disetujui, namun terkena potongan nilai 50% karena melewati batas tenggat toleransi.
                    </p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection