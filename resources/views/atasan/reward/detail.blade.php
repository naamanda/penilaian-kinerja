@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Pemenang Reward</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Periode Penilaian: <span class="font-semibold text-[#1e3f7c]">Bulan {{ $bulanAktif }} / {{ $tahunAktif }}</span>
            </p>
        </div>
        <a href="/reward-atasan" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
            ← Kembali
        </a>
    </div>

    {{-- Pemenang Section (Satu Kotak Utuh) --}}
    @if($pemenang->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-500 font-medium">Belum ada data hasil akhir untuk periode ini.</p>
            <p class="text-gray-400 text-sm mt-1">Pastikan data penilaian bulan {{ $bulanAktif }}/{{ $tahunAktif }} sudah digenerate.</p>
        </div>
    @else
        @foreach($pemenang as $p)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            
            {{-- Header Card: Kombinasi Nama Reward & Peringkat (Warna Navy Sesuai Konsep) --}}
            <div class="bg-[#1e3f7c] text-white px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Program Reward — Terpilih</p>
                    <h2 class="text-xl font-extrabold tracking-wide mt-1">{{ $reward->nama_reward }}</h2>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded text-[11px] font-bold bg-white/20 text-white uppercase tracking-wider">
                        {{ str_replace('_', ' ', $reward->jenis) }}
                    </span>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Total Nominal Hadiah</p>
                    <p class="text-2xl font-black text-emerald-300 mt-0.5">Rp {{ number_format($reward->nominal, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Body Card: Informasi Karyawan --}}
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Nama Karyawan</p>
                        <p class="text-lg font-bold text-gray-800">{{ $p->karyawan->nama ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Divisi Kerja</p>
                        <p class="text-base font-semibold text-gray-700">{{ $p->karyawan->divisi->nama_divisi ?? 'Tidak Ada Divisi' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Predikat Kinerja</p>
                        @php
                            $predikatClass = match($p->predikat) {
                                'A'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'AB' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'B'  => 'bg-sky-50 text-sky-700 border-sky-200',
                                'C'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-rose-50 text-rose-700 border-rose-200',
                            };
                            $predikatLabel = match($p->predikat) {
                                'A'  => 'Sangat Baik',
                                'AB' => 'Baik Sekali',
                                'B'  => 'Baik',
                                'C'  => 'Cukup',
                                default => 'Kurang',
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold border {{ $predikatClass }}">
                            {{ $p->predikat }} — {{ $predikatLabel }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Nilai Akhir Sistem</p>
                        <p class="text-2xl font-extrabold text-[#1e3f7c]">
                            {{ number_format($p->nilai_akhir, 2) }} <span class="text-xs font-semibold text-gray-400">/ 100</span>
                        </p>
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                {{-- Bagian Bawah: Output Benefit Hadiah --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Apresiasi yang Diberikan</p>
                    <div class="flex flex-wrap gap-3">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-2.5 min-w-[160px]">
                            <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">Uang Tunai</p>
                            <p class="text-base font-extrabold text-emerald-700 mt-0.5">Rp {{ number_format($reward->nominal, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5 min-w-[220px]">
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Sertifikat Penghargaan</p>
                            <p class="text-sm font-bold text-blue-700 mt-0.5">Resmi {{ $reward->nama_reward }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    @endif

</div>
@endsection