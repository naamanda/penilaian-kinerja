@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">
    {{-- Tombol Kembali --}}
    <a href="{{ route('karyawan.akun.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition duration-200 shadow-sm w-fit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Akun
    </a>

    {{-- Kotak Utama Tunggal (All-in-One Card) --}}
    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">

        {{-- 1. Bagian Header Kotak (Status & Bulan) --}}
        <div class="px-4 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Periode Evaluasi</p>
                <p class="text-sm font-bold text-gray-800">{{ $daftarBulan[$bulan] }} {{ $tahun }}</p>
            </div>
            <div>
                {{-- REVISI: Gunakan strtoupper agar pengecekan string status tidak sensitif huruf besar/kecil --}}
                @php $statusUser = strtoupper($pelanggaran['status']); @endphp

                @if($statusUser === 'SP2')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Surat Peringatan 2
                </span>
                @elseif($statusUser === 'SP1')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Surat Peringatan 1
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Status Aman
                </span>
                @endif
            </div>
        </div>

        <div class="p-4 space-y-4">
            {{-- 2. Bagian Statistik Poin Pelanggaran (3 Kolom) --}}
            <div class="grid grid-cols-3 gap-2">
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100/50">
                    <p class="text-[11px] font-medium text-gray-400 mb-0.5">Terlambat</p>
                    <p class="text-lg font-bold {{ $pelanggaran['terlambat'] > 0 ? 'text-amber-500' : 'text-gray-400' }}">
                        {{ $pelanggaran['terlambat'] }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100/50">
                    <p class="text-[11px] font-medium text-gray-400 mb-0.5">Tidak Mengerjakan</p>
                    <p class="text-lg font-bold {{ $pelanggaran['tidak_mengerjakan'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                        {{ $pelanggaran['tidak_mengerjakan'] }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100/50">
                    <p class="text-[11px] font-medium text-gray-400 mb-0.5">Total Poin</p>
                    <p class="text-lg font-bold {{ $pelanggaran['total_poin'] >= 5 ? 'text-rose-600' : 'text-gray-700' }}">
                        {{ $pelanggaran['total_poin'] }}
                    </p>
                </div>
            </div>

            {{-- 3. Keterangan Aturan Poin Pelanggaran --}}
            <div class="bg-blue-50/30 rounded-xl p-3 border border-blue-50/50">
                <p class="text-[11px] font-semibold text-gray-500 mb-1 flex items-center gap-1">
                    <i class="fas fa-info-circle text-[#1e3f7c]/70"></i> Informasi Poin Pelanggaran:
                </p>
                <div class="grid grid-cols-2 gap-y-1 gap-x-2 text-[10px] text-gray-400">
                    <span class="flex items-center gap-1">• Terlambat: <b class="text-gray-500">1 Poin</b></span>
                    <span class="flex items-center gap-1">• SP1: <b class="text-amber-600">Poin ≥ 8</b></span>
                    <span class="flex items-center gap-1">• Tidak Mengerjakan: <b class="text-gray-500">2 Poin</b></span>
                    <span class="flex items-center gap-1">• SP2: <b class="text-rose-600">Poin ≥ 12</b></span>
                </div>
            </div>

            {{-- 4. Surat Peringatan Resmi --}}
            @php
            $spBulanIni = $riwayat->first(fn($r) => $r->bulan == $bulan && $r->tahun == $tahun && !empty($r->file_sp));
            @endphp

            @if($spBulanIni)
            <div class="pt-3 border-t border-gray-100">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                    <div class="max-w-[60%]">
                        <p class="text-xs font-bold text-gray-800 flex items-center gap-1">
                            <i class="fas fa-file-signature text-[#1e3f7c]"></i> File SP
                        </p>
                        <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $spBulanIni->file_sp }}</p>
                    </div>
                    <a href="{{ asset('uploads/sp_signed/' . $spBulanIni->file_sp) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold text-white bg-[#1e3f7c] hover:bg-[#152c58] transition shadow-sm">
                        <i class="fas fa-eye text-[10px]"></i> Lihat Surat
                    </a>
                </div>
            </div>
            @elseif($statusUser !== 'AMAN')
            {{-- REVISI: Sudah disesuaikan menggunakan variabel $statusUser hasil kapitalisasi --}}
            <div class="pt-2 text-center text-[11px] text-amber-500 font-medium italic">
                Berkas fisik Surat Peringatan sedang diproses oleh Atasan.
            </div>
            @endif

        </div>
    </div>
</div>
@endsection