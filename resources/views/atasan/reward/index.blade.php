@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Reward</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Periode Aktif Penilaian: <span class="font-semibold text-[#1e3f7c]">Bulan {{ $bulanAktif }} / {{ $tahunAktif }}</span>
            </p>
        </div>
        <a href="/reward-atasan/tambah" class="bg-[#1e3f7c] hover:bg-[#152c58] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
            ➕ Tambah Data
        </a>
    </div>

    {{-- Flash Message Success --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Tabel Utama Kelola Reward --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white text-sm text-left">
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nama Reward</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Kriteria Kualifikasi</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Nominal Hadiah</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reward as $r)
                    <tr class="hover:bg-blue-50/40 transition-colors">
                        {{-- Nama Reward --}}
                        <td class="px-6 py-3.5 text-base font-semibold text-gray-900">
                            {{ $r->nama_reward }}
                        </td>

                        {{-- Jenis Kriteria --}}
                        <td class="px-6 py-3.5 text-sm text-gray-700">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-md 
                                {{ str_contains($r->jenis, 'rank') ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                🎯 {{ strtoupper(str_replace('_', ' ', $r->jenis)) }}
                            </span>
                        </td>

                        {{-- Nominal --}}
                        <td class="px-6 py-3.5">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Rp {{ number_format($r->nominal, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="px-6 py-3.5 text-center text-sm font-medium">
                            <div class="flex justify-center gap-2">
                                {{-- Tombol Lihat Detail (Icon Mata) --}}
                                <a href="/reward-atasan/{{ $r->id_reward }}" class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition flex items-center justify-center" title="Lihat Detail Pemenang">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                {{-- Tombol Edit (Icon Pensil) --}}
                                <a href="/reward-atasan/edit/{{ $r->id_reward }}" class="p-2 bg-amber-50 text-amber-500 hover:text-amber-600 rounded-xl hover:bg-amber-100 transition flex items-center justify-center" title="Ubah Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>

                                {{-- Tombol Hapus (Icon Tong Sampah) --}}
                                <form action="/reward-atasan/hapus/{{ $r->id_reward }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program reward ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-500 hover:text-rose-600 rounded-xl hover:bg-rose-100 transition flex items-center justify-center outline-none" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium">
                            😔 Belum ada data program reward yang ditambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $reward->links() }}
    </div>

</div>
@endsection