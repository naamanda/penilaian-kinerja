@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-700">Dashboard Admin</h1>

    <div class="grid grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
            <div class="text-4xl">👤</div>
            <div>
                <p class="text-gray-500 text-sm">Total Karyawan</p>
                <p class="text-3xl font-bold text-[#234C92]">{{ $totalKaryawan }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
            <div class="text-4xl">🏢</div>
            <div>
                <p class="text-gray-500 text-sm">Total Divisi</p>
                <p class="text-3xl font-bold text-[#234C92]">{{ $totalDivisi }}</p>
            </div>
        </div>

    </div>
</div>
@endsection