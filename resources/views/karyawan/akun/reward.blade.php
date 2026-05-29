@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">
    <a href="{{ route('karyawan.akun') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-[#1e3f7c]">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Akun
    </a>

    <div class="bg-white border border-gray-100 shadow-sm p-4 rounded-2xl">
        <h3 class="text-base font-bold text-gray-800 mb-2">Riwayat Reward</h3>
        <p class="text-xs text-gray-500">Daftar penghargaan yang berhasil Anda raih akan muncul di sini.</p>
        
        {{-- Konten looping reward Anda di sini nanti --}}
    </div>
</div>
@endsection