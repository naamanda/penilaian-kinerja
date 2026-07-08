@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Data Divisi</h1>
        <a href="/data-divisi/tambah"
            class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Data
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white">
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Nama Divisi</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Tempat Kerja</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($divisi as $d)
                    <tr class="hover:bg-blue-50/60 transition-colors">
                        <td class="px-6 py-3.5 text-base font-medium text-gray-900">{{ $d->nama_divisi }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-600 font-medium italic">{{ $d->tempat_kerja }}</td>
                        <td class="px-6 py-3.5 text-center text-sm font-medium">
                            <div class="flex justify-center gap-3">

                                {{-- Edit Button --}}
                                <a href="/data-divisi/edit/{{ $d->id_divisi }}" class="text-amber-500 hover:text-amber-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>

                                {{-- Delete Button --}}
                                <form id="delete-form-{{ $d->id_divisi }}" action="/data-divisi/hapus/{{ $d->id_divisi }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="openDeleteModal('{{ $d->id_divisi }}', '{{ $d->nama_divisi }}')"
                                        class="text-rose-500 hover:text-rose-600 transition">
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
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada data divisi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4 flex justify-end">
        <div class="flex items-center gap-3">
            {{-- Showing info --}}
            <span class="text-sm text-gray-500">
                Showing {{ $divisi->firstItem() }} to {{ $divisi->lastItem() }} of {{ $divisi->total() }} results
            </span>

            {{-- Page number buttons only --}}
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= $divisi->lastPage(); $i++)
                    <a href="{{ $divisi->url($i) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-all
                    {{ $divisi->currentPage() == $i 
                        ? 'bg-[#1e3f7c] text-white' 
                        : 'bg-white text-gray-600 border border-gray-200 hover:bg-blue-50/60' }}">
                        {{ $i }}
                    </a>
                    @endfor
            </div>
        </div>
    </div>

    {{-- Custom Tailwind Modal Hapus --}}
    <div id="delete-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Content -->
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
                                    Apakah Anda yakin ingin menghapus data divisi <span id="modal-divisi" class="font-bold text-gray-800 not-italic"></span>? Tindakan ini tidak bisa dibatalkan.
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

    <script>
        let activeDeleteFormId = null;

        function openDeleteModal(id, name) {
            activeDeleteFormId = 'delete-form-' + id;
            document.getElementById('modal-divisi').innerText = name;

            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            activeDeleteFormId = null;
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (activeDeleteFormId) {
                document.getElementById(activeDeleteFormId).submit();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
</div>
@endsection