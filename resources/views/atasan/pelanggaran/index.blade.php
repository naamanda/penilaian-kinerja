@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pelanggaran Karyawan</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Monitoring pelanggaran otomatis untuk periode:
                <span class="font-semibold text-[#1e3f7c]">Bulan {{ $bulan }} / {{ $tahun }}</span>
            </p>
        </div>

        {{-- Filter Bulan & Tahun ala Kotak Reward --}}
        <form method="GET" action="{{ route('pelanggaran.index') }}" class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-200 native-layout w-fit self-end md:self-auto">
            {{-- Dropdown Bulan --}}
            <select name="bulan" class="bg-gray-50 border border-gray-200 text-xs rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#1e3f7c] outline-none cursor-pointer text-gray-700 font-medium">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
                @endforeach
            </select>

            {{-- Dropdown Tahun --}}
            <select name="tahun" class="bg-gray-50 border border-gray-200 text-xs rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#1e3f7c] outline-none cursor-pointer text-gray-700 font-medium">
                @foreach(range(date('Y')-2, date('Y')) as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            {{-- Tombol Submit Filter --}}
            <button type="submit" class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-xs font-semibold px-4 py-1.5 rounded-lg transition-all shadow-sm">
                Filter
            </button>
        </form>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
        ✅ {{ session('success') }}
    </div>
    @endif

    @php
    $totalAman = $pelanggarans->filter(fn($item) => strtoupper($item->status) === 'AMAN')->count();
    $totalSP1 = $pelanggarans->filter(fn($item) => strtoupper($item->status) === 'SP1')->count();
    $totalSP2 = $pelanggarans->filter(fn($item) => strtoupper($item->status) === 'SP2')->count();
    $totalSemua = $pelanggarans->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Total Karyawan</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalSemua }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Karyawan aktif</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Status Aman</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $totalAman }}</p>
            <p class="text-xs text-emerald-400 mt-0.5">• Tidak ada pelanggaran</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Surat Peringatan 1</p>
            <p class="text-3xl font-bold text-amber-500">{{ $totalSP1 }}</p>
            <p class="text-xs text-amber-400 mt-0.5">• Poin ≥ 8</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Surat Peringatan 2</p>
            <p class="text-3xl font-bold text-rose-600">{{ $totalSP2 }}</p>
            <p class="text-xs text-rose-400 mt-0.5">• Poin ≥ 12</p>
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white text-sm text-left whitespace-nowrap">
                        <th class="px-8 py-4 font-semibold uppercase tracking-wider text-left">Nama Karyawan</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Terlambat</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Tidak Mengerjakan</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Total Poin</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggarans as $p)
                    <tr class="hover:bg-blue-50/40 transition-colors whitespace-nowrap">

                        {{-- Nama Karyawan --}}
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-9 h-9 rounded-full bg-[#1e3f7c]/10 flex items-center justify-center text-[#1e3f7c] font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($p->karyawan->nama ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $p->karyawan->nama ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ is_string($p->karyawan->divisi) ? $p->karyawan->divisi : ($p->karyawan->divisi->nama_divisi ?? '-') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Terlambat --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                {{ $p->total_terlambat > 0 ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-400' }}">
                                {{ $p->total_terlambat }}
                            </span>
                        </td>

                        {{-- Tidak Mengerjakan --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                {{ $p->total_tidakmengerjakan > 0 ? 'bg-rose-50 text-rose-600' : 'bg-gray-50 text-gray-400' }}">
                                {{ $p->total_tidakmengerjakan }}
                            </span>
                        </td>

                        {{-- Total Poin --}}
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold
                                @if($p->total_poinpl >= 9) bg-rose-50 text-rose-700 border border-rose-100
                                @elseif($p->total_poinpl >= 5) bg-amber-50 text-amber-700 border border-amber-100
                                @else bg-gray-50 text-gray-500 border border-gray-100
                                @endif">
                                {{ $p->total_poinpl }} poin
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if(strtoupper($p->status) === 'SP2')
                            <span class="px-3 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">SP2</span>
                            @elseif(strtoupper($p->status) === 'SP1')
                            <span class="px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">SP1</span>
                            @else
                            <span class="px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Aman</span>
                            @endif
                        </td>

                        {{-- Kolom Aksi Tunggal (Upload / Lihat + Cancel) --}}
                        <td class="px-6 py-4 text-center">
                            @if($p->id_pelanggaran)
                            @if($p->file_sp)
                            <div class="flex items-center justify-center gap-2 mx-auto">
                                {{-- Tombol Lihat Berkas --}}
                                <a href="{{ asset('uploads/sp_signed/' . $p->file_sp) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 transition shadow-sm">
                                    📄 Lihat File
                                </a>
                                {{-- Tombol Cancel / Hapus Berkas --}}
                                <form action="{{ route('pelanggaran.deleteSp', $p->id_pelanggaran) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus/membatalkan file Surat Peringatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-100 transition inline-flex items-center justify-center" title="Hapus Berkas SP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @else
                            {{-- Jika Status SP1/SP2 tapi belum upload berkas --}}
                            @if(strtoupper($p->status) !== 'AMAN')
                            <button
                                onclick="document.getElementById('modal-{{ $p->id_pelanggaran }}').classList.remove('hidden')"
                                class="p-2 bg-[#1e3f7c]/10 text-[#1e3f7c] rounded-xl hover:bg-[#1e3f7c]/20 transition flex items-center justify-center mx-auto"
                                title="Unggah Surat Peringatan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </button>
                            @else
                            <span class="text-xs text-gray-300">-</span>
                            @endif
                            @endif
                            @else
                            <span class="text-xs text-amber-400 italic">ID Kosong</span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                            Tidak ada data pelanggaran untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Keterangan Poin --}}
    <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500">
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span> Terlambat = 1 poin</span>
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span> Tidak Mengerjakan = 2 poin</span>
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> SP1: Poin ≥ 8</span>
        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> SP2: poin ≥ 12</span>
    </div>

</div>

{{-- Modal Upload --}}
@foreach($pelanggarans as $p)
@if($p->id_pelanggaran)
<div id="modal-{{ $p->id_pelanggaran }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Unggah Surat Peringatan</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $p->karyawan->nama ?? '-' }} &middot;
                    <span class="font-semibold text-[#1e3f7c]">{{ $p->status }}</span>
                </p>
            </div>
            <button onclick="document.getElementById('modal-{{ $p->id_pelanggaran }}').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('pelanggaran.uploadSp', $p->id_pelanggaran) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center mb-4 hover:border-[#1e3f7c]/40 transition">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <label class="cursor-pointer">
                    <span class="text-sm font-semibold text-[#1e3f7c]">Pilih file berkas SP</span>
                    <input type="file" name="file_sp" accept=".pdf,.jpg,.png" class="hidden" required
                        onchange="document.getElementById('filename-{{ $p->id_pelanggaran }}').textContent = this.files[0]?.name ?? ''">
                </label>
                <p id="filename-{{ $p->id_pelanggaran }}" class="text-xs text-gray-400 mt-1 truncate px-2"></p>
            </div>

            <div class="flex gap-2">
                <button type="button"
                    onclick="document.getElementById('modal-{{ $p->id_pelanggaran }}').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-[#1e3f7c] hover:bg-[#152c58] text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm">
                    Simpan Berkas
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

<script>
    document.querySelectorAll('[id^="modal-"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });
</script>
@endsection