@extends('layouts.karyawan')

@section('content')
{{-- Header --}}
<div class="bg-[#1e3f7c] px-5 pt-10 pb-6 flex items-center justify-between">
    <a href="/aktivitas-misi" class="text-white flex items-center gap-1 text-sm font-semibold">
        <span class="inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </span>
    </a>
    <span class="text-white font-bold text-lg">Detail Misi</span>
    <div class="w-14"></div>
</div>

{{-- Toast Notifikasi --}}
<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] hidden max-w-xs w-full px-4 py-3 rounded-2xl shadow-xl text-white text-sm font-semibold flex items-center gap-3 transition-all duration-300">
    <span id="toast-icon" class="text-lg shrink-0"></span>
    <span id="toast-message"></span>
</div>

<div class="px-4 py-4 pb-24">

    @php
    $waktuUploadFull = \Carbon\Carbon::parse(
    \Carbon\Carbon::parse($pengerjaan->tanggal)->format('Y-m-d') . ' ' . $pengerjaan->waktu_upload
    );
    $isTerlambat = $waktuUploadFull->gt(\Carbon\Carbon::parse($toleransi));
    @endphp

    {{-- Card Informasi Misi --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-yellow-500 bg-yellow-50 px-2.5 py-1 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8l-2 4H6l3 2-1 4 4-2 4 2-1-4 3-2h-4l-2-4z" />
                </svg>
                {{ $pengerjaan->misi->poin }} Poin
            </span>

            <div>
                @if($pengerjaan->status == 'belum_mengerjakan')
                @if($bisaUpload)
                <span class="text-xs bg-blue-100 text-blue-600 px-2.5 py-1 rounded-full font-medium">Bisa Dikerjakan</span>
                @elseif(\Carbon\Carbon::now()->gt($toleransi))
                <span class="text-xs bg-gray-100 text-gray-400 px-2.5 py-1 rounded-full font-medium">Terlewat</span>
                @else
                <span class="text-xs bg-yellow-100 text-yellow-600 px-2.5 py-1 rounded-full font-medium">Belum Mulai</span>
                @endif
                @elseif($pengerjaan->status == 'menunggu')
                @if($isTerlambat)
                <span class="text-xs bg-amber-100 text-amber-600 px-2.5 py-1 rounded-full font-medium">Menunggu Persetujuan (Terlambat)</span>
                @else
                <span class="text-xs bg-orange-100 text-orange-600 px-2.5 py-1 rounded-full font-medium">Menunggu Persetujuan</span>
                @endif
                @elseif($pengerjaan->status == 'disetujui')
                @if($isTerlambat)
                <span class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-medium">Disetujui (Terlambat)</span>
                @else
                <span class="text-xs bg-emerald-100 text-emerald-600 px-2.5 py-1 rounded-full font-medium">Disetujui</span>
                @endif
                @elseif($pengerjaan->status == 'ditolak')
                <span class="text-xs bg-red-100 text-red-600 px-2.5 py-1 rounded-full font-medium">Ditolak (Bisa Upload Ulang)</span>
                @endif
            </div>
        </div>

        <h1 class="font-bold text-gray-800 text-lg mb-1">{{ $pengerjaan->misi->nama_misi }}</h1>
        <p class="text-sm text-gray-500 mb-4">{{ $pengerjaan->misi->deskripsi }}</p>

        <div class="border-t border-gray-100 pt-3 flex flex-col gap-1.5 text-xs text-gray-400">
            <p>Waktu Misi: <strong>{{ \Carbon\Carbon::parse($pengerjaan->misi->waktu_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($pengerjaan->misi->waktu_selesai)->format('H:i') }} WIB</strong></p>
            <p>Batas Toleransi: <strong class="text-red-400">{{ \Carbon\Carbon::parse($toleransi)->format('H:i') }} WIB</strong></p>
        </div>
    </div>

    {{-- Area Interaktif --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <h2 class="font-bold text-gray-800 text-sm mb-4">Bukti Pengerjaan Misi</h2>

        @if($bisaUpload)
        <div id="camera-container" class="relative w-full h-[480px] bg-black rounded-xl overflow-hidden mb-4 flex flex-col items-center justify-center shadow-inner">
            <button type="button" id="btn-start-init" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md z-10">
                Aktifkan Kamera
            </button>
            <video id="video" class="hidden w-full h-full object-cover absolute inset-0" autoplay playsinline></video>
            <canvas id="canvas" class="hidden"></canvas>
            <img id="preview" class="hidden w-full h-full object-cover absolute inset-0" alt="Preview foto">
            <button type="button" id="btn-switch" class="hidden absolute top-3 right-3 bg-black/50 text-white p-2.5 rounded-full hover:bg-black/70 transition backdrop-blur-sm z-10">
                Putar Kamera
            </button>
        </div>

        <div class="flex flex-col gap-3">
            <button type="button" id="btn-capture" class="hidden w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                Ambil Foto Bukti
            </button>
            <button type="button" id="btn-retake" class="hidden w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                Foto Ulang
            </button>
            <button type="button" id="btn-submit" class="hidden w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                Kirim Bukti Misi
            </button>
        </div>
        @else
        <div class="flex flex-col gap-4">
            @if($pengerjaan->foto)
            <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-3.5 rounded-xl">
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center shrink-0">
                    <span class="text-xl">📷</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span class="text-xs font-semibold text-gray-700 truncate">{{ $pengerjaan->foto }}</span>
                    <a href="{{ asset('uploads/misi/' . $pengerjaan->foto) }}" target="_blank" class="text-xs text-blue-500 font-medium hover:underline mt-0.5">
                        Klik untuk buka foto
                    </a>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-100 pt-3 flex flex-col gap-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Poin Diperoleh:</span>
                    @if($pengerjaan->status == 'disetujui')
                    <span class="font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">+{{ $pengerjaan->misi->poin }} Poin</span>
                    @elseif($pengerjaan->status == 'terlambat')
                    <span class="font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">+{{ $pengerjaan->poin_didapat }} Poin</span>
                    @elseif($pengerjaan->status == 'ditolak')
                    <span class="font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-md">0 Poin</span>
                    @elseif($pengerjaan->status == 'menunggu')
                    <span class="font-medium text-gray-500 bg-gray-50 px-2 py-0.5 rounded-md">Menunggu Persetujuan</span>
                    @endif
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Status Misi:</span>
                    <div>
                        @if($pengerjaan->status == 'menunggu')
                        <span class="font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md">Menunggu Persetujuan</span>
                        @elseif($pengerjaan->status == 'disetujui')
                        <span class="font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Disetujui</span>
                        @elseif($pengerjaan->status == 'terlambat')
                        <span class="font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Disetujui (Terlambat)</span>
                        @elseif($pengerjaan->status == 'ditolak')
                        <span class="font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-md">Ditolak</span>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Waktu Upload:</span>
                    <span class="text-gray-600 font-medium">
                        {{ \Carbon\Carbon::parse($pengerjaan->tanggal)->format('d M Y') }}
                        pukul {{ substr($pengerjaan->waktu_upload, 0, 5) }} WIB
                    </span>
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-400">Waktu pengerjaan belum mulai/telah habis.</p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

@if($bisaUpload)
<script>
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toast-icon');
        const msg = document.getElementById('toast-message');

        toast.className = `fixed top-6 left-1/2 -translate-x-1/2 z-[9999] max-w-xs w-full px-4 py-3 rounded-2xl shadow-xl text-white text-sm font-semibold flex items-center gap-3 transition-all duration-300
            ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;

        icon.textContent = type === 'success' ? '✅' : '❌';
        msg.textContent = message;

        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.style.opacity = '1';
            }, 400);
        }, 2800);
    }

    // Kamera
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const btnStartInit = document.getElementById('btn-start-init');
    const btnCapture = document.getElementById('btn-capture');
    const btnRetake = document.getElementById('btn-retake');
    const btnSubmit = document.getElementById('btn-submit');
    const btnSwitch = document.getElementById('btn-switch');

    let currentStream = null;
    let base64Image = null;
    let currentFacingMode = "environment";

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
    }

    function startCamera(facingMode) {
        stopCamera();
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode
                },
                audio: false
            })
            .then(stream => {
                currentStream = stream;
                video.srcObject = stream;
                video.classList.remove('hidden');
                btnSwitch.classList.remove('hidden');
                btnCapture.classList.remove('hidden');
            })
            .catch(() => {
                navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    })
                    .then(stream => {
                        currentStream = stream;
                        video.srcObject = stream;
                        video.classList.remove('hidden');
                        btnSwitch.classList.remove('hidden');
                        btnCapture.classList.remove('hidden');
                    })
                    .catch(() => {
                        showToast('Gagal mengakses kamera.', 'error');
                        btnStartInit.classList.remove('hidden');
                    });
            });
    }

    btnStartInit.addEventListener('click', () => {
        btnStartInit.classList.add('hidden');
        startCamera(currentFacingMode);
    });

    btnSwitch.addEventListener('click', () => {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        startCamera(currentFacingMode);
    });

    btnCapture.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        base64Image = canvas.toDataURL('image/png');

        preview.src = base64Image;
        video.classList.add('hidden');
        btnSwitch.classList.add('hidden');
        stopCamera();

        preview.classList.remove('hidden');
        btnCapture.classList.add('hidden');
        btnRetake.classList.remove('hidden');
        btnSubmit.classList.remove('hidden');
    });

    btnRetake.addEventListener('click', () => {
        base64Image = null;
        preview.classList.add('hidden');
        btnRetake.classList.add('hidden');
        btnSubmit.classList.add('hidden');
        startCamera(currentFacingMode);
    });

    btnSubmit.addEventListener('click', () => {
        if (!base64Image) {
            showToast('Silakan ambil foto bukti terlebih dahulu.', 'error');
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerText = "Mengirim...";

        fetch('/aktivitas-misi/{{ $pengerjaan->id_pengerjaan }}/upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    foto: base64Image
                })
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;
                if (!response.ok) {
                    throw new Error((data && data.message) ? data.message : 'Terjadi kesalahan pada server.');
                }
                return data;
            })
            .then(data => {
                showToast(data.message || 'Bukti misi berhasil dikirim!', 'success');
                setTimeout(() => window.location.href = '/aktivitas-misi', 2500);
            })
            .catch(err => {
                showToast('Gagal: ' + err.message, 'error');
                btnSubmit.disabled = false;
                btnSubmit.innerText = "📤 Kirim Bukti Misi";
            });
    });
</script>
@endif
@endsection