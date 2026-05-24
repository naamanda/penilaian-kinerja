@extends('layouts.karyawan')

@section('content')

{{-- Header --}}
<div class="bg-[#1e3f7c] px-5 pt-10 pb-8">
    <div class="flex items-center justify-between mb-5">
        <img src="{{ asset('assets/logo.png') }}" alt="logo" class="w-8 h-8 object-contain">
        <span class="text-white font-bold text-lg">LifeSync</span>
        <div class="w-8"></div>
    </div>
    <p class="text-blue-200 text-sm">Selamat Datang,</p>
    <p class="text-white text-xl font-bold">{{ $karyawan->nama }}! 👋</p>
</div>

{{-- Nilai Card --}}
<div class="px-4 -mt-5">
    <div class="bg-white rounded-2xl shadow-md p-4 flex items-center gap-4">
        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">⭐</div>
        <div class="flex-1">
            <p class="text-xs text-gray-400 font-medium">Nilai Sekarang</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-bold text-gray-800">{{ $nilai }}</p>
                <span class="text-sm font-bold px-2 py-0.5 rounded-lg
                    {{ $nilaiData['predikat']['kode'] == 'A'  ? 'bg-emerald-100 text-emerald-600' :
                      ($nilaiData['predikat']['kode'] == 'AB' ? 'bg-blue-100 text-blue-600' :
                      ($nilaiData['predikat']['kode'] == 'B'  ? 'bg-sky-100 text-sky-600' :
                      ($nilaiData['predikat']['kode'] == 'C'  ? 'bg-yellow-100 text-yellow-600' :
                       'bg-rose-100 text-rose-600'))) }}">
                    {{ $nilaiData['predikat']['kode'] }} — {{ $nilaiData['predikat']['label'] }}
                </span>
            </div>
            <p class="text-xs text-blue-500 font-medium mt-0.5">Terus tingkatkan performa kamu!</p>
        </div>
    </div>
</div>

<div class="px-4 mt-4 flex flex-col gap-4 pb-24">

    {{-- Leaderboard --}}
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="font-bold text-gray-800">Leaderboard</p>
            <a href="#" class="text-xs text-blue-500 font-medium">Lihat Semua</a>
        </div>
        <div class="flex flex-col gap-1">
            @foreach($leaderboard as $i => $lb)
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                {{ $lb->id_karyawan == Session::get('id_karyawan') ? 'bg-blue-50 border border-blue-100' : '' }}">
                <div class="w-6 text-center flex-shrink-0">
                    @if($i == 0) <span class="text-base">🥇</span>
                    @elseif($i == 1) <span class="text-base">🥈</span>
                    @elseif($i == 2) <span class="text-base">🥉</span>
                    @else <span class="text-xs font-bold text-gray-400">{{ $i + 1 }}</span>
                    @endif
                </div>
                <p class="flex-1 text-sm font-semibold text-gray-700">
                    {{ $lb->nama }}
                    @if($lb->id_karyawan == Session::get('id_karyawan'))
                        <span class="text-blue-500">(Anda)</span>
                    @endif
                </p>
                <div class="flex items-center gap-1">
                    <span class="text-sm">🪙</span>
                    <span class="text-sm font-bold {{ $lb->id_karyawan == Session::get('id_karyawan') ? 'text-blue-600' : 'text-gray-600' }}">
                        {{ $lb->total_nilai }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Kehadiran --}}
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <p class="font-bold text-gray-800 mb-3">
            Kehadiran {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F') }}
        </p>
        <div class="flex items-baseline gap-1 mb-3">
            <span class="text-3xl font-bold text-blue-600">{{ $kehadiran['total'] }}</span>
            <span class="text-gray-400 text-sm">/ {{ $kehadiran['hari_kerja'] }} hari</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-blue-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-blue-600">{{ $kehadiran['hadir'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Hadir</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-orange-500">{{ $kehadiran['terlambat'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Terlambat</p>
            </div>
            <div class="bg-rose-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-rose-500">{{ $kehadiran['tidak_hadir'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Tidak Hadir</p>
            </div>
        </div>
    </div>

    {{-- Pelanggaran --}}
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="font-bold text-gray-800">Pelanggaran Bulan Ini</p>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg
                {{ $nilaiData['pelanggaran']['status'] == 'Aman' ? 'bg-emerald-100 text-emerald-600' :
                  ($nilaiData['pelanggaran']['status'] == 'SP1'  ? 'bg-orange-100 text-orange-600' :
                   'bg-rose-100 text-rose-600') }}">
                {{ $nilaiData['pelanggaran']['status'] }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-orange-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-orange-500">{{ $nilaiData['pelanggaran']['terlambat'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Terlambat</p>
            </div>
            <div class="bg-rose-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-rose-500">{{ $nilaiData['pelanggaran']['tidak_mengerjakan'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Tidak Mengerjakan</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-gray-600">{{ $nilaiData['pelanggaran']['total_poin'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Poin</p>
            </div>
        </div>
    </div>

    {{-- Target Mingguan --}}
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <p class="font-bold text-gray-800 mb-3">Target Mingguan</p>
        @forelse($tugas as $t)
        <div class="mb-3 last:mb-0">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm text-gray-700 flex-1 pr-2">{{ $t->nama_tugas }}</p>
                <p class="text-xs font-bold text-gray-500 flex-shrink-0">{{ $t->selesai }}/{{ $t->total }}</p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-[#1e3f7c] h-2 rounded-full"
                     style="width: {{ $t->total > 0 ? ($t->selesai / $t->total) * 100 : 0 }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                Deadline: {{ \Carbon\Carbon::parse($t->deadline)->locale('id')->translatedFormat('l, H:i') }}
            </p>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-3">Tidak ada tugas minggu ini</p>
        @endforelse
    </div>

</div>

@endsection