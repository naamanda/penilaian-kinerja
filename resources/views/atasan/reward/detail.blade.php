@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Pemenang Reward</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Periode Aktif Penilaian: <span class="font-semibold text-[#1e3f7c]">Bulan {{ $bulanAktif }} / {{ $tahunAktif }}</span>
            </p>
        </div>
        <a href="/reward-atasan" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
            ← Kembali
        </a>
    </div>

    {{-- Info Kartu Program Reward --}}
    <div class="bg-[#1e3f7c] text-white rounded-2xl p-5 mb-6 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-200 mb-1">Program Reward</p>
            <h2 class="text-xl font-bold">{{ $reward->nama_reward }}</h2>
            <span class="inline-block mt-2 px-3 py-1 rounded-md text-xs font-bold bg-white/20 text-white">
                🎯 {{ strtoupper(str_replace('_', ' ', $reward->jenis)) }}
            </span>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-200 mb-1">Total Hadiah</p>
            <p class="text-2xl font-extrabold text-emerald-300">Rp {{ number_format($reward->nominal, 0, ',', '.') }}</p>
            <p class="text-xs text-blue-200 mt-0.5">+ Sertifikat Penghargaan</p>
        </div>
    </div>

    {{-- Pemenang --}}
    @if($pemenang->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-4xl mb-3">😔</p>
            <p class="text-gray-500 font-medium">Belum ada data hasil akhir untuk periode ini.</p>
            <p class="text-gray-400 text-sm mt-1">Pastikan data penilaian bulan {{ $bulanAktif }}/{{ $tahunAktif }} sudah digenerate.</p>
        </div>
    @else
        @foreach($pemenang as $p)
        @php
            // Normalisasi teks untuk penentuan tema warna komponen view
            $namaRewardLower = strtolower($reward->nama_reward);
            $jenisLower = strtolower($reward->jenis);

            // Deteksi Kategori & Set Tema Warna Secara Akurat
            if (str_contains($namaRewardLower, '1') || $jenisLower === 'rank_1') {
                $medal = '🥇';
                $titleBanner = 'Karyawan Terbaik — Peringkat 1';
                $bgGradient = 'from-amber-400 to-amber-200 text-amber-950';
            } elseif (str_contains($namaRewardLower, '2') || $jenisLower === 'rank_2') {
                $medal = '🥈';
                $titleBanner = 'Karyawan Terbaik — Peringkat 2';
                $bgGradient = 'from-slate-300 to-slate-100 text-slate-900';
            } elseif (str_contains($namaRewardLower, '3') || $jenisLower === 'rank_3') {
                $medal = '🥉';
                $titleBanner = 'Karyawan Terbaik — Peringkat 3';
                $bgGradient = 'from-orange-400/80 to-orange-200 text-orange-950';
            } else {
                $medal = '🏅';
                $titleBanner = 'Karyawan Terdisiplin Bulan Ini';
                $bgGradient = 'from-cyan-500 to-blue-400 text-white';
            }
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">

            {{-- Banner Judul Medali Terbuka (Dinamis Sesuai Juara) --}}
            <div class="bg-gradient-to-r {{ $bgGradient }} px-6 py-4 flex items-center gap-3">
                <span class="text-3xl">{{ $medal }}</span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-75">Pemenang Terpilih</p>
                    <p class="text-base font-extrabold">{{ $titleBanner }}</p>
                </div>
            </div>

            {{-- Detail Karyawan --}}
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Nama Karyawan --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Nama Karyawan</p>
                        <p class="text-lg font-bold text-gray-800">{{ $p->karyawan->nama ?? '-' }}</p>
                    </div>

                    {{-- Divisi --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Divisi</p>
                        <p class="text-base font-semibold text-gray-700">{{ $p->karyawan->divisi->nama_divisi ?? 'Tidak Ada Divisi' }}</p>
                    </div>

                    {{-- Predikat --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Predikat Kinerja</p>
                        @php
                            $predikatStyle = match($p->predikat) {
                                'A'  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                'AB' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                'B'  => 'bg-sky-50 text-sky-700 border border-sky-200',
                                'C'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                default => 'bg-rose-50 text-rose-700 border border-rose-200',
                            };
                            $predikatLabel = match($p->predikat) {
                                'A'  => 'Sangat Baik',
                                'AB' => 'Baik Sekali',
                                'B'  => 'Baik',
                                'C'  => 'Cukup',
                                default => 'Kurang',
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold w-fit {{ $predikatStyle }}">
                            {{ $p->predikat }} — {{ $predikatLabel }}
                        </span>
                    </div>

                    {{-- Nilai Akhir --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Nilai Akhir Sistem</p>
                        <p class="text-2xl font-extrabold text-[#1e3f7c]">{{ number_format($p->nilai_akhir, 2) }}
                            <span class="text-sm font-semibold text-gray-400">/ 100</span>
                        </p>
                    </div>

                </div>

                {{-- Divider --}}
                <hr class="my-5 border-gray-100">

                {{-- Hadiah yang Diterima --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Hadiah yang Diterima</p>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-2.5 shadow-sm">
                            <span class="text-xl">💵</span>
                            <div>
                                <p class="text-xs text-emerald-600 font-semibold">Uang Tunai</p>
                                <p class="text-base font-extrabold text-emerald-700">Rp {{ number_format($reward->nominal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5 shadow-sm">
                            <span class="text-xl">📜</span>
                            <div>
                                <p class="text-xs text-blue-600 font-semibold">Sertifikat Penghargaan</p>
                                <p class="text-sm font-bold text-blue-700">Resmi Penghargaan {{ $reward->nama_reward }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    @endif

</div>
@endsection