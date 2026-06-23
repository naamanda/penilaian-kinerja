@extends('layouts.atasan')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section dengan Filter Rekap Dropdown --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Reward</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Monitoring program reward otomatis untuk periode: <span class="font-semibold text-[#1e3f7c]">Bulan {{ $bulanAktif }} / {{ $tahunAktif }}</span>
            </p>
        </div>
        
        {{-- Form Filter Periode & Tombol Tambah --}}
        <div class="flex flex-wrap items-center gap-3 native-layout">
            <form action="/reward-atasan" method="GET" class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-200">
                {{-- Dropdown Bulan --}}
                <select name="bulan" class="bg-gray-50 border border-gray-200 text-xs rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#1e3f7c] outline-none cursor-pointer text-gray-700 font-medium">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulanAktif == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>

                {{-- Dropdown Tahun --}}
                <select name="tahun" class="bg-gray-50 border border-gray-200 text-xs rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#1e3f7c] outline-none cursor-pointer text-gray-700 font-medium">
                    @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

                {{-- Tombol Submit Filter --}}
                <button type="submit" class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-xs font-semibold px-4 py-1.5 rounded-lg transition-all shadow-sm">
                    Filter
                </button>
            </form>

            {{-- Tombol Tambah Data --}}
            <a href="/reward-atasan/tambah"
                class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Data
            </a>
        </div>
    </div>

    {{-- Flash Message Success --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Bagian Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white">
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Nama Reward</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Kualifikasi</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Nominal Hadiah</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reward as $r)
                    <tr class="hover:bg-blue-50/60 transition-colors">
                        {{-- Nama Reward --}}
                        <td class="px-6 py-3.5 text-base font-medium text-gray-900">{{ $r->nama_reward }}</td>

                        {{-- Kualifikasi (Badge ala Karyawan) --}}
                        <td class="px-6 py-3.5">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold 
                                {{ str_contains($r->jenis, 'rank') ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                🎯 {{ strtoupper(str_replace('_', ' ', $r->jenis)) }}
                            </span>
                        </td>

                        {{-- Nominal Hadiah --}}
                        <td class="px-6 py-3.5 text-sm text-gray-600 font-medium italic">
                            Rp {{ number_format($r->nominal, 0, ',', '.') }}
                        </td>

                        {{-- Kolom Aksi Terintegrasi Modal JS --}}
                        <td class="px-6 py-3.5 text-center text-sm font-medium">
                            <div class="flex justify-center gap-3">
                                {{-- Tombol Lihat Detail --}}
                                <a href="/reward-atasan/{{ $r->id_reward }}" class="text-blue-500 hover:text-blue-600 transition" title="Lihat Detail Pemenang">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                {{-- Tombol Edit --}}
                                <a href="/reward-atasan/edit/{{ $r->id_reward }}" class="text-amber-500 hover:text-amber-600 transition" title="Ubah Data">
                                    <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form id="delete-form-{{ $r->id_reward }}" action="/reward-atasan/hapus/{{ $r->id_reward }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="openDeleteModal('{{ $r->id_reward }}', '{{ addslashes($r->nama_reward) }}')"
                                        class="text-rose-500 hover:text-rose-600 transition outline-none" title="Hapus Data">
                                        <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada data program reward yang ditambahkan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Showing Halaman & Pagination ala Karyawan --}}
    @if ($reward->total() > 0)
    <div class="mt-4 flex justify-end">
        <div class="flex items-center gap-3">
            {{-- Showing info --}}
            <span class="text-sm text-gray-500">
                Showing {{ $reward->firstItem() }} to {{ $reward->lastItem() }} of {{ $reward->total() }} results
            </span>

            {{-- Nomor halaman only --}}
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= $reward->lastPage(); $i++)
                    <a href="{{ $reward->appends(request()->query())->url($i) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-all
                    {{ $reward->currentPage() == $i 
                        ? 'bg-[#1e3f7c] text-white' 
                        : 'bg-white text-gray-600 border border-gray-200 hover:bg-blue-50/60' }}">
                        {{ $i }}
                    </a>
                @endfor
            </div>
        </div>
    </div>
    @endif

    {{-- Pop Up Hapus Kustom Tailwind --}}
    <div id="delete-modal" class="fixed inset-0 z-[9999] hidden overflow-hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0 font-sans">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left font-sans">
                            <h3 class="text-lg font-bold text-gray-900 leading-6">Konfirmasi Hapus</h3>
                            <div class="mt-2 text-sans">
                                <p class="text-sm text-gray-500 italic">
                                    Apakah Anda yakin ingin menghapus data reward <span id="modal-reward" class="font-bold text-gray-800 not-italic"></span>? Tindakan ini tidak bisa dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 font-sans">
                    <button type="button" id="confirm-delete-btn"
                        class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition-all sm:w-auto">
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-50/60 transition-all sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // SCRIPT UTAMA UNTUK MENGHILANGKAN SCROLLBAR DI SAMPING KANAN LAYAR UTAMA
    document.body.style.overflow = 'hidden';

    let activeDeleteFormId = null;

    // munculin popup
    function openDeleteModal(id, name) {
        activeDeleteFormId = 'delete-form-' + id;
        document.getElementById('modal-reward').innerText = name;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    // sembunyiin popup
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        activeDeleteFormId = null;
    }

    // Eksekusi Hapus Form
    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (activeDeleteFormId) {
            document.getElementById(activeDeleteFormId).submit();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endsection