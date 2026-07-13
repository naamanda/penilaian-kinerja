@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-5">

    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-6 px-4 text-center">
        <div class="w-20 h-20 rounded-full bg-[#1e3f7c] flex items-center justify-center mx-auto shadow-md mb-3">
            <span class="text-3xl font-bold text-white">
                {{ strtoupper(substr($karyawan->nama ?? $karyawan->name ?? 'K', 0, 1)) }}
            </span>
        </div>
        <h2 class="text-xl font-bold text-gray-800">{{ $karyawan->nama ?? $karyawan->name }}</h2>
        <p class="text-gray-400 text-sm font-medium mt-0.5">{{ $karyawan->divisi->nama_divisi ?? $karyawan->jabatan ?? 'Karyawan' }}</p>

        @if($hasilAkhir)
        <div class="inline-block mt-3 px-4 py-1.5 bg-amber-50 border border-amber-200 rounded-full">
            <span class="text-xs font-semibold text-amber-800">Predikat Kinerja Bulan Ini: <strong class="text-sm font-bold ml-0.5">{{ $hasilAkhir->predikat }}</strong></span>
        </div>
        @endif
    </div>

    {{-- Judul Seksi Menu Nilai --}}
    <div class="flex items-center justify-between px-1">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Komponen Nilai Bulan Ini</span>
        <span class="text-xs text-gray-400 font-medium">Klik untuk lihat log detail</span>
    </div>

    {{-- Menu List: Berbentuk Score Cards Ringkas --}}
    <div class="grid grid-cols-1 gap-3">

        {{-- Komponen Nilai Absensi --}}
        <a href="{{ route('karyawan.akun.unduh') }}#absensi" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-slate-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-user-check text-blue-600 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Nilai Kehadiran</p>
                    <p class="text-lg font-bold text-gray-800">{{ $hasilAkhir->nilai_kehadiran ?? '0.00' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-blue-500 font-medium opacity-0 group-hover:opacity-100 transition-opacity">Lihat Log</span>
                <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        {{-- Komponen Nilai Kedisiplinan / Misi --}}
        <a href="{{ route('karyawan.akun.unduh') }}#misi" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-slate-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-orange-100 p-3 rounded-xl">
                    <i class="fas fa-bolt text-orange-500 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Nilai Kedisiplinan (Misi)</p>
                    <p class="text-lg font-bold text-gray-800">{{ $hasilAkhir->nilai_kedisiplinan ?? '0.00' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-orange-500 font-medium opacity-0 group-hover:opacity-100 transition-opacity">Lihat Log</span>
                <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        {{-- Komponen Nilai Tugas --}}
        <a href="{{ route('karyawan.akun.unduh') }}#tugas" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-slate-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-tasks text-purple-600 text-lg w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Nilai Pengumpulan Tugas</p>
                    <p class="text-lg font-bold text-gray-800">{{ $hasilAkhir->nilai_tugas ?? '0.00' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-purple-500 font-medium opacity-0 group-hover:opacity-100 transition-opacity">Lihat Log</span>
                <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <div class="border-t border-gray-100 my-2"></div>

        {{-- Link Menu Tambahan --}}
        <a href="{{ route('karyawan.akun.reward') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-gray-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-amber-100 p-2.5 rounded-xl">
                    <i class="fas fa-trophy text-amber-500 text-sm w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Reward</p>
                    <p class="text-xs text-gray-400">Penghargaan Yang Diterima</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

        <a href="{{ route('karyawan.akun.pelanggaran') }}" class="flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-gray-50 transition group">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 p-2.5 rounded-xl">
                    <i class="fas fa-file-alt text-red-500 text-sm w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Pelanggaran</p>
                    <p class="text-xs text-gray-400">Riwayat Sanksi</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </a>

        {{-- Logout --}}
        <button onclick="openLogoutModal()" class="w-full text-left flex items-center justify-between bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:bg-red-50 transition group focus:outline-none">
            <div class="flex items-center space-x-4">
                <div class="bg-gray-100 p-2.5 rounded-xl group-hover:bg-red-100 transition">
                    <i class="fas fa-sign-out-alt text-gray-700 group-hover:text-red-600 text-sm w-5 text-center"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-red-600 transition">Logout</p>
                    <p class="text-xs text-gray-400">Keluar Dari Akun</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:translate-x-1 transition-transform"></i>
        </button>

    </div>
</div>

{{-- ================= MODAL LOGOUT KUSTOM ================= --}}
<div id="logoutModal" class="fixed inset-0 z-50 invisible opacity-0 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeLogoutModal()"></div>

    <div class="relative bg-white rounded-3xl shadow-xl border border-gray-100 max-w-sm w-full p-6 text-center z-10" id="modalContainer">

        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-red-50 text-red-500 mb-4">
            <i class="fa-solid fa-arrow-right-from-bracket text-red-600"></i>
        </div>

        <h3 class="text-lg font-extrabold text-gray-900">Konfirmasi Keluar</h3>
        <p class="text-xs text-gray-500 mt-2 px-2 leading-relaxed">
            Apakah Anda yakin ingin mengakhiri sesi ini dan keluar dari aplikasi <span class="font-semibold text-[#1e3f7c]">LifeSync</span>?
        </p>

        <div class="mt-6 flex gap-3">
            <button type="button" onclick="closeLogoutModal()" class="flex-1 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl cursor-pointer">
                Batal
            </button>
            <a href="{{ route('logout') }}" class="flex-1 py-2.5 px-4 bg-red-500 hover:bg-red-600 text-white font-bold text-xs rounded-xl shadow-md shadow-red-200 text-center flex items-center justify-center cursor-pointer">
                Ya, Keluar
            </a>
        </div>
    </div>
</div>

{{-- Script Logika Show/Hide Instan --}}
<script>
    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('invisible', 'opacity-0');
        modal.classList.add('visible', 'opacity-100');

        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('visible', 'opacity-100');
        modal.classList.add('invisible', 'opacity-0');

        document.body.style.overflow = 'auto';
    }
</script>
@endsection