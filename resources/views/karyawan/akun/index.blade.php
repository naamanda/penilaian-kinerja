@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">

    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-6 px-4 text-center">
        <div class="w-20 h-20 rounded-full bg-[#1e3f7c] flex items-center justify-center mx-auto shadow-md mb-3">
            <span class="text-3xl font-bold text-white">
                {{ strtoupper(substr($karyawan->nama ?? $karyawan->name ?? 'K', 0, 1)) }}
            </span>
        </div>
        <h2 class="text-xl font-bold text-gray-800">{{ $karyawan->nama ?? $karyawan->name }}</h2>
        <p class="text-gray-400 text-sm font-medium mt-0.5">{{ $karyawan->jabatan ?? 'IT Officer' }}</p>
    </div>

    {{-- Menu List --}}
    <div class="space-y-2">

        {{-- Link ke Unduh Laporan --}}
        <a href="{{ route('karyawan.akun.unduh') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-gray-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-orange-100 p-2.5 rounded-xl">
                    <i class="fas fa-arrow-down text-orange-500 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Unduh Laporan Hasil</p>
                    <p class="text-xs text-gray-400">Laporan Kinerja Bulanan</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

        {{-- Link ke Reward --}}
        <a href="{{ route('karyawan.akun.reward') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-gray-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-amber-100 p-2.5 rounded-xl">
                    <i class="fas fa-trophy text-amber-500 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Reward</p>
                    <p class="text-xs text-gray-400">Penghargaan Yang Diterima</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

        {{-- Link ke Pelanggaran --}}
        <a href="{{ route('karyawan.akun.pelanggaran') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-gray-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 p-2.5 rounded-xl">
                    <i class="fas fa-file-alt text-red-500 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Pelanggaran</p>
                    <p class="text-xs text-gray-400">Riwayat Sanksi</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

        {{-- Logout --}}
        <a href="{{ route('logout') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-red-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-gray-100 p-2.5 rounded-xl">
                    <i class="fas fa-sign-out-alt text-gray-700 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Logout</p>
                    <p class="text-xs text-gray-400">Keluar Dari Akun</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

    </div>
</div>
@endsection