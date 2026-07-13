{{-- resources/views/pages/admin/backup/index.blade.php --}}
{{-- Halaman Backup: menampilkan statistik database & riwayat file backup --}}

@extends('layouts.admin') {{-- Sesuaikan dengan nama layout admin yang kamu pakai --}}

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ==================== HEADER HALAMAN ==================== --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1e3f7c]">Backup & Restore</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola backup database dan source code aplikasi LIFESYNC.</p>
    </div>

    {{-- ==================== ALERT SUCCESS / ERROR ==================== --}}
    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        {{ session('error') }}
    </div>
    @endif

    {{-- ==================== KARTU STATISTIK ==================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Jumlah Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-table text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jumlah Tabel</p>
                <p class="text-lg font-bold text-gray-800">{{ $tableCount }}</p>
            </div>
        </div>

        {{-- Ukuran Database --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-database text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Ukuran Database</p>
                <p class="text-lg font-bold text-gray-800">
                    @php
                    // Format ukuran database (bytes ke B/KB/MB/GB) langsung di view
                    // karena formatFileSize() di controller bersifat private
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $i = $dbSize > 0 ? floor(log($dbSize, 1024)) : 0;
                    echo $dbSize > 0 ? round($dbSize / pow(1024, $i), 2) . ' ' . $units[$i] : '0 B';
                    @endphp
                </p>
            </div>
        </div>

        {{-- Backup Database Terakhir --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-file text-blue-600" style="width:16px; flex-shrink:0;"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Backup Database Terakhir</p>
                <p class="text-sm font-semibold text-gray-800">{{ $lastDbBackup['created_at'] ?? 'Belum ada' }}</p>
            </div>
        </div>

        {{-- Backup Source Code Terakhir --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-code text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Backup Source Code Terakhir</p>
                <p class="text-sm font-semibold text-gray-800">{{ $lastCodeBackup['created_at'] ?? 'Belum ada' }}</p>
            </div>
        </div>
    </div>

    {{-- ==================== TOMBOL AKSI BACKUP ==================== --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Buat Backup Baru</h2>
        <div class="flex flex-wrap gap-3">

            {{-- Form backup database --}}
            <form action="{{ route('admin.backup.database') }}" method="GET">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fa-solid fa-download"></i>
                    Backup Database
                </button>
            </form>

            {{-- Form backup source code --}}
            <form action="{{ route('admin.backup.source-code') }}" method="GET">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition">
                    <i class="fa-solid fa-download"></i>
                    Backup Source Code
                </button>
            </form>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            Proses backup dapat memakan waktu beberapa saat tergantung ukuran database dan source code.
        </p>
    </div>

    {{-- ==================== TABEL RIWAYAT BACKUP ==================== --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Riwayat Backup</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        <th class="px-5 py-3 text-left font-medium">No</th>
                        <th class="px-5 py-3 text-left font-medium">Nama File</th>
                        <th class="px-5 py-3 text-left font-medium">Tipe</th>
                        <th class="px-5 py-3 text-left font-medium">Ukuran</th>
                        <th class="px-5 py-3 text-left font-medium">Tanggal Dibuat</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($files as $file)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-500">{{ $file['no'] }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-file text-red-600"></i>
                                <span class="text-gray-700 font-medium break-all">{{ $file['filename'] }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $file['type_class'] }}">
                                {{ $file['type'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $file['size'] }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $file['created_at'] }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Download --}}
                                <a href="{{ route('admin.backup.download', $file['filename']) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Download">
                                    <i class="fa-solid fa-download"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('admin.backup.delete', $file['filename']) }}" method="POST" class="form-hapus-backup">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            Belum ada riwayat backup.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== SCRIPT: KONFIRMASI HAPUS ==================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tangkap semua form hapus backup dan tambahkan konfirmasi SweetAlert
        document.querySelectorAll('.form-hapus-backup').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus file backup?',
                    text: 'File yang sudah dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#e5e7eb',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        cancelButton: '!text-gray-700',
                        popup: '!rounded-2xl',
                        confirmButton: '!rounded-xl',
                        cancelButton: '!rounded-xl',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@endsection