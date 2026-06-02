@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">
    {{-- Tombol Kembali --}}
    <a href="{{ route('karyawan.akun') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-[#1e3f7c] transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Akun
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
                @if($pelanggaran['status'] === 'SP2')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Surat Peringatan 2
                    </span>
                @elseif($pelanggaran['status'] === 'SP1')
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
                    <span class="flex items-center gap-1">• SP1: <b class="text-amber-600">Poin 5–8</b></span>
                    <span class="flex items-center gap-1">• Tidak Mengerjakan: <b class="text-gray-500">2 Poin</b></span>
                    <span class="flex items-center gap-1">• SP2: <b class="text-rose-600">Poin ≥ 9</b></span>
                </div>
            </div>

            {{-- 4. Surat Peringatan Resmi (Hanya muncul jika file dari atasan tersedia) --}}
            @php
                // Mencari berkas SP khusus untuk bulan dan tahun berjalan ini
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
            @elseif($pelanggaran['status'] !== 'AMAN')
                {{-- Jika mendapat SP tapi berkas belum diupload oleh atasan --}}
                <div class="pt-2 text-center text-[11px] text-amber-500 font-medium italic">
                    ⚠️ Berkas fisik Surat Peringatan sedang diproses oleh Atasan.
                </div>
            @endif

        </div>
    </div>
</div>
@endsection