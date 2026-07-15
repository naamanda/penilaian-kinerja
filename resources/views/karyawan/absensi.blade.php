@extends('layouts.karyawan')

@section('content')

{{-- Header --}}
<div class="bg-[#1e3f7c] px-5 pt-6 pb-8">
    <div class="flex items-center justify-center gap-3 mb-1">
        <img src="{{ asset('assets/logo.png') }}" alt="logo" class="w-10 h-10 object-contain">
        <span class="text-white font-bold text-2xl tracking-wide">LifeSync</span>
    </div>
</div>

<div class="px-4 py-4 pb-24 -mt-4">
    <div class="bg-white rounded-2xl shadow-md p-5">

        <p class="font-bold text-gray-800 text-base">Absensi</p>

        @if($liburHariIni)
        <p class="text-xs text-gray-400 mt-0.5">Hari ini libur, tidak ada absensi.</p>
        <p class="text-xs text-gray-500 mt-1 mb-4">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </p>

        {{-- Tampilan Hari Libur --}}
        <div class="w-full h-40 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50
            flex flex-col items-center justify-center mb-2">
            <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="flex items-center gap-2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l.7 2.154a1 1 0 00.95.69h2.264c.969 0 1.371 1.24.588 1.81l-1.832 1.331a1 1 0 00-.364 1.118l.7 2.154c.3.921-.755 1.688-1.54 1.118l-1.832-1.331a1 1 0 00-1.176 0l-1.832 1.331c-.784.57-1.838-.197-1.539-1.118l.7-2.154a1 1 0 00-.364-1.118L5.597 7.581c-.783-.57-.38-1.81.588-1.81H8.45a1 1 0 00.95-.69l.7-2.154z" />
                </svg>
                <p class="text-sm font-semibold">Selamat menikmati hari libur</p>
            </div>
        </div>

        @else

        <p class="text-xs text-gray-400 mt-0.5">Absensi Hari Ini</p>
        <p class="text-xs text-gray-500 mt-1 mb-4">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </p>

        <div class="inline-flex items-center gap-1.5 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Batas Waktu: 18:00
        </div>

        @if(!$sudahAbsen)

        @if($sudahTutup)
        {{-- Absensi sudah ditutup --}}
        <div class="w-full h-40 rounded-2xl border-2 border-dashed border-red-200 bg-red-50
            flex flex-col items-center justify-center mb-4">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-red-400">Absensi sudah ditutup</p>
            <p class="text-xs text-red-300 mt-1">Batas waktu absensi 18:00</p>
        </div>

        @else
        {{-- Area Kamera --}}
        <div id="kameraArea" class="w-full h-[480px] rounded-2xl border-2 border-dashed border-blue-300 bg-blue-50 flex flex-col items-center justify-center cursor-pointer mb-4 overflow-hidden relative" onclick="bukaKamera()">
            <canvas id="fotoCanvas" class="absolute inset-0 w-full h-[480px] object-cover rounded-2xl hidden"></canvas>
            <div id="placeholder" class="flex flex-col items-center gap-2 z-10">
                <div class="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-blue-500">Klik Untuk Ambil Foto</p>
            </div>
        </div>

        {{-- Video live kamera --}}
        <video id="video" class="hidden w-full h-[480px] rounded-2xl mb-3 object-cover" autoplay playsinline></video>

        {{-- Tombol ambil foto --}}
        <button id="btnCapture" onclick="ambilFoto()"
            class="hidden w-full py-3 rounded-xl text-sm font-bold bg-blue-500 text-white mb-3">
            Ambil Foto
        </button>

        {{-- Info lokasi --}}
        <div id="infoLokasi" class="text-xs text-center mb-3 hidden">
            <span id="lokasiText" class="text-gray-400">Mengambil lokasi...</span>
        </div>

        {{-- Tombol absen --}}
        <button id="btnAbsen"
            class="w-full py-3.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-400 cursor-not-allowed transition-all"
            disabled onclick="kirimAbsensi()">
            Absen Sekarang
        </button>

        {{-- Tombol ulangi --}}
        <button id="btnUlang" onclick="ulangi()"
            class="hidden w-full py-3 rounded-xl text-sm font-bold border border-gray-300 text-gray-500 mt-2">
            Ulangi Foto
        </button>
        @endif

        @else

        {{-- Halaman Setelah Absensi Berhasil --}}
        <div class="w-full h-[480px] rounded-2xl overflow-hidden mb-4 bg-gray-100 shadow-inner">
            @if($fotoAbsensi)
            <img src="{{ url('uploads/absensi/' . $fotoAbsensi) }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-sm gap-2">
                @if($statusAbsen == 'izin')
                <p class="font-semibold text-gray-600 mt-2">Surat Izin Telah Diunggah</p>
                @else
                <p>Tidak ada foto</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Pengecekan Status Badge --}}
        @if($statusAbsen == 'hadir')
        <div class="w-full py-3.5 rounded-xl text-sm font-bold text-center bg-emerald-100 text-emerald-600">
            Hadir — {{ $waktuAbsen }} WIB
        </div>
        @elseif($statusAbsen == 'izin')
        {{-- Mengambil data izin terbaru untuk hari ini --}}
        @php
        $dataIzinHariIni = \App\Models\Izin::where('id_karyawan', session('id_karyawan'))
            ->whereDate('tanggal_izin', \Carbon\Carbon::today())
            ->first();

        $statusPersetujuan = '';
        if ($dataIzinHariIni) {
            if ($dataIzinHariIni->status == 'disetujui') {
                $statusPersetujuan = ' (Disetujui)';
            } elseif ($dataIzinHariIni->status == 'ditolak') {
                $statusPersetujuan = ' (Ditolak)';
            } else {
                $statusPersetujuan = ' (Menunggu Persetujuan)';
            }
        }
        @endphp

        <div class="w-full py-3.5 rounded-xl text-sm font-bold text-center bg-blue-100 text-blue-600">
            Izin — {{ str_replace(' WIB', '', $waktuAbsen) }} WIB{{ $statusPersetujuan }}
        </div>
        @elseif($statusAbsen == 'tidak_hadir')
        <div class="w-full py-3.5 rounded-xl text-sm font-bold text-center bg-red-100 text-red-600">
            Tidak Hadir
        </div>
        @else
        <div class="w-full py-3.5 rounded-xl text-sm font-bold text-center bg-orange-100 text-orange-600">
            Terlambat — {{ $waktuAbsen }} WIB
        </div>
        @endif

        @endif
        @endif

    </div>
</div>

{{-- Pop-up Modal Custom untuk Gagal Radius --}}
<div id="popupRadius" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-xl transform scale-95 transition-transform duration-300">
        <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
            📍
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Absensi Gagal</h3>
        <p class="text-sm text-gray-500 mb-6">
            Posisi kamu di luar radius kantor. Silahkan mendekat ke area kantor untuk melakukan absensi.
        </p>
        <button onclick="tutupPopup()" class="w-full py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl shadow-md transition-all active:scale-98">
            OK
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let fotoBase64 = null;
    let stream = null;
    let userLat = null;
    let userLng = null;
    let lokasiValid = false;

    const OFFICE_LAT = -7.678603;
    const OFFICE_LNG = 109.035448;
    const RADIUS_KM = 0.1;

    function hitungJarak(lat1, lon1, lat2, lon2) {
        const theta = lon1 - lon2;
        let dist = Math.sin(deg2rad(lat1)) * Math.sin(deg2rad(lat2)) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.cos(deg2rad(theta));
        dist = Math.acos(Math.min(1, dist));
        dist = dist * (180 / Math.PI);
        return (dist * 60 * 1.1515) * 1.609344;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    window.addEventListener('load', () => {
        const infoLokasi = document.getElementById('infoLokasi');
        const lokasiText = document.getElementById('lokasiText');

        if (!infoLokasi) return;

        infoLokasi.classList.remove('hidden');
        lokasiText.textContent = 'Mengambil lokasi...';
        lokasiText.className = 'text-gray-400';

        if (!navigator.geolocation) {
            lokasiText.textContent = 'Browser tidak mendukung lokasi';
            lokasiText.className = 'text-yellow-500';
            return;
        }

        const cekLokasi = (pos) => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;

            const jarak = hitungJarak(userLat, userLng, OFFICE_LAT, OFFICE_LNG);
            const jarakM = Math.round(jarak * 1000);

            if (jarak <= RADIUS_KM) {
                lokasiValid = true;
                lokasiText.textContent = `Lokasi sesuai — ${jarakM}m dari kantor`;
                lokasiText.className = 'text-green-600 font-semibold';
            } else {
                lokasiValid = false;
                lokasiText.textContent = `Di luar radius kantor — ${jarakM}m dari kantor (maks. ${RADIUS_KM * 1000}m)`;
                lokasiText.className = 'text-red-500 font-semibold';
            }
        };

        const gagalLokasi = (error) => {
            console.log(error);
            alert(
                "Code: " + error.code +
                "\nMessage: " + error.message
            );
            lokasiText.textContent = "⚠️ " + error.message;
            lokasiText.className = "text-red-500";
        };

        navigator.geolocation.getCurrentPosition(cekLokasi, gagalLokasi, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    });

    async function bukaKamera() {
        if (fotoBase64) return;

        const video = document.getElementById('video');
        const kameraArea = document.getElementById('kameraArea');
        const btnCapture = document.getElementById('btnCapture');

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                }
            });
            video.srcObject = stream;
            video.className = "w-full h-[480px] rounded-2xl mb-3 object-cover";
            video.classList.remove('hidden');
            kameraArea.classList.add('hidden');
            btnCapture.classList.remove('hidden');
        } catch (err) {
            alert('Tidak bisa akses kamera: ' + err.message);
        }
    }

    function ambilFoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('fotoCanvas');
        const btnCapture = document.getElementById('btnCapture');
        const kameraArea = document.getElementById('kameraArea');
        const btnAbsen = document.getElementById('btnAbsen');
        const btnUlang = document.getElementById('btnUlang');
        const placeholder = document.getElementById('placeholder');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        fotoBase64 = canvas.toDataURL('image/png');

        if (stream) stream.getTracks().forEach(t => t.stop());

        video.classList.add('hidden');
        btnCapture.classList.add('hidden');
        kameraArea.classList.remove('hidden');
        canvas.classList.remove('hidden');
        placeholder.classList.add('hidden');

        btnAbsen.disabled = false;
        btnAbsen.className = 'w-full py-3.5 rounded-xl text-sm font-bold bg-[#1e3f7c] text-white transition-all active:scale-95';
        btnUlang.classList.remove('hidden');
    }

    function ulangi() {
        const canvas = document.getElementById('fotoCanvas');
        const kameraArea = document.getElementById('kameraArea');
        const placeholder = document.getElementById('placeholder');
        const btnAbsen = document.getElementById('btnAbsen');
        const btnUlang = document.getElementById('btnUlang');

        fotoBase64 = null;
        if (stream) stream.getTracks().forEach(t => t.stop());

        canvas.classList.add('hidden');
        placeholder.classList.remove('hidden');
        kameraArea.classList.remove('hidden');
        btnUlang.classList.add('hidden');

        btnAbsen.disabled = true;
        btnAbsen.className = 'w-full py-3.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-400 cursor-not-allowed transition-all';
    }

    function bukaPopup() {
        const modal = document.getElementById('popupRadius');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.firstElementChild.classList.remove('scale-95');
        }, 20);
    }

    function tutupPopup() {
        const modal = document.getElementById('popupRadius');
        modal.classList.add('opacity-0');
        modal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    async function kirimAbsensi() {
        if (!fotoBase64) {
            alert('Silahkan ambil foto terlebih dahulu.');
            return;
        }

        if (!lokasiValid) {
            bukaPopup();
            return;
        }

        const btn = document.getElementById('btnAbsen');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        try {
            const res = await fetch('/absensi-karyawan/simpan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    foto: fotoBase64,
                    latitude: userLat,
                    longitude: userLng,
                })
            });

            const data = await res.json();

            if (res.ok) {
                window.location.reload();
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.textContent = 'Absen Sekarang';
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi. Coba lagi.');
            btn.disabled = false;
            btn.textContent = 'Absen Sekarang';
        }
    }
</script>
@endpush