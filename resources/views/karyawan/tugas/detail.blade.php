@extends('layouts.karyawan')

@section('content')

{{-- Toast Notifikasi --}}
<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] hidden max-w-xs w-full px-4 py-3 rounded-2xl shadow-xl text-white text-sm font-semibold flex items-center gap-3 transition-all duration-300">
    <span id="toast-icon" class="text-lg shrink-0"></span>
    <span id="toast-message"></span>
</div>

<div class="bg-[#1e3f7c] px-5 pt-10 pb-6 flex items-center gap-3">
    <a href="/tugas-mingguan" class="text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <span class="text-white font-bold text-lg">Detail Tugas</span>
</div>

<div class="px-4 py-4 pb-24">

    {{-- Info Tugas --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-4">
        <p class="font-bold text-gray-800 text-base">{{ $pengumpulan->tugas->nama_tugas }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $pengumpulan->tugas->deskripsi }}</p>

        <div class="mt-3 text-xs text-gray-500">
            📅 Deadline: {{ \Carbon\Carbon::parse($pengumpulan->tugas->deadline)->locale('id')->translatedFormat('l, d F Y H:i') }}
        </div>

        <div class="mt-1 text-xs text-yellow-500 font-semibold">
            🏅 {{ $pengumpulan->tugas->poin }} poin
        </div>

        <div class="mt-3">
            @if($pengumpulan->status == 'menunggu')
                <span class="text-xs bg-orange-100 text-orange-600 px-3 py-1 rounded-full">Menunggu Persetujuan</span>
            @elseif($pengumpulan->status == 'disetujui')
                <span class="text-xs bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full">✓ Disetujui — +{{ $pengumpulan->poin_didapat }} poin</span>
            @elseif($pengumpulan->status == 'terlambat')
                <span class="text-xs bg-purple-100 text-purple-600 px-3 py-1 rounded-full">Terlambat — +{{ $pengumpulan->poin_didapat }} poin</span>
            @elseif($pengumpulan->status == 'ditolak')
                <span class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full">Ditolak — Upload ulang bukti</span>
            @else
                <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Belum Dikerjakan</span>
            @endif
        </div>
    </div>

    {{-- Bukti yang sudah diupload --}}
    @if($pengumpulan->file)
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
        <p class="text-xs font-semibold text-gray-500 mb-2">Bukti yang diupload:</p>
        <a href="/tugas-mingguan/{{ $pengumpulan->id_pengumpulan }}/file"
            target="_blank"
            class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.414V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-700">{{ $pengumpulan->file }}</p>
                <p class="text-xs text-blue-500 mt-0.5">Klik untuk buka file</p>
            </div>
        </a>
        <p class="text-xs text-gray-400 mt-2">
            Diupload: {{ \Carbon\Carbon::parse($pengumpulan->tanggal_upload)->locale('id')->translatedFormat('d F Y') }}
            pukul {{ \Carbon\Carbon::parse($pengumpulan->waktu_upload)->format('H:i') }}
        </p>
    </div>
    @endif

    {{-- Form Upload --}}
    @if($bisaUpload)
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <p class="font-semibold text-gray-700 text-sm mb-1">Upload Bukti</p>
        <p class="text-xs text-gray-400 mb-4">
            Deadline: {{ $deadline->locale('id')->translatedFormat('l, d F Y H:i') }}
        </p>

        <div id="previewArea"
            class="w-full aspect-square rounded-2xl border-2 border-dashed border-blue-300 bg-blue-50
                    flex flex-col items-center justify-center cursor-pointer mb-4 overflow-hidden relative"
            onclick="document.getElementById('inputFile').click()">
            <div id="placeholder" class="flex flex-col items-center gap-2 z-10">
                <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-blue-500">Klik Untuk Pilih File</p>
                <p class="text-xs text-gray-400">PDF, maks. 10 MB</p>
            </div>
        </div>

        <input type="file" id="inputFile" class="hidden" accept="application/pdf" onchange="previewFile(event)">

        <button id="btnUpload"
            class="w-full py-3.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-400 cursor-not-allowed"
            disabled onclick="kirimUpload()">
            Upload Bukti
        </button>

        <button id="btnUlang" onclick="ulangi()"
            class="hidden w-full py-3 rounded-xl text-sm font-bold border border-gray-300 text-gray-500 mt-2">
            🔄 Ganti File
        </button>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // ── Toast ──────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon  = document.getElementById('toast-icon');
        const msg   = document.getElementById('toast-message');

        toast.className = `fixed top-6 left-1/2 -translate-x-1/2 z-[9999] max-w-xs w-full px-4 py-3 rounded-2xl shadow-xl text-white text-sm font-semibold flex items-center gap-3 transition-all duration-300
            ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;

        icon.textContent = type === 'success' ? '✅' : '❌';
        msg.textContent  = message;

        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.style.opacity = '1';
            }, 400);
        }, 2800);
    }

    // ── File Handling ──────────────────────────────────────
    let fotoBase64 = null;

    function previewFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            showToast('Hanya file PDF yang diperbolehkan.', 'error');
            event.target.value = '';
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            showToast('Ukuran file tidak boleh lebih dari 10 MB.', 'error');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            fotoBase64 = e.target.result;

            document.getElementById('placeholder').innerHTML = `
            <div class="flex flex-col items-center gap-2 px-4">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.414V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-green-600 text-center break-all">${file.name}</p>
                <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            </div>`;

            const btnUpload = document.getElementById('btnUpload');
            btnUpload.disabled  = false;
            btnUpload.className = 'w-full py-3.5 rounded-xl text-sm font-bold bg-[#1e3f7c] text-white transition-all active:scale-95';
            document.getElementById('btnUlang').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function ulangi() {
        fotoBase64 = null;
        document.getElementById('inputFile').value = '';
        document.getElementById('placeholder').innerHTML = `
        <div class="flex flex-col items-center gap-2 z-10">
            <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-blue-500">Klik Untuk Pilih File</p>
            <p class="text-xs text-gray-400">PDF, maks. 10 MB</p>
        </div>`;

        const btnUpload = document.getElementById('btnUpload');
        btnUpload.disabled  = true;
        btnUpload.className = 'w-full py-3.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-400 cursor-not-allowed';
        document.getElementById('btnUlang').classList.add('hidden');
    }

    async function kirimUpload() {
        if (!fotoBase64) return;

        const btn = document.getElementById('btnUpload');
        btn.disabled    = true;
        btn.textContent = 'Mengupload...';

        try {
            const res  = await fetch('/tugas-mingguan/{{ $pengumpulan->id_pengumpulan }}/upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ file: fotoBase64 })
            });

            const data = await res.json();

            if (res.ok) {
                showToast(data.message || 'Tugas berhasil diupload!', 'success');
                setTimeout(() => window.location.href = '/tugas-mingguan', 2500);
            } else {
                showToast(data.message || 'Gagal mengupload tugas.', 'error');
                btn.disabled    = false;
                btn.textContent = 'Upload Bukti';
            }
        } catch (e) {
            showToast('Terjadi kesalahan. Coba lagi.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Upload Bukti';
        }
    }
</script>
@endpush