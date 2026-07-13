@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">

    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <h3 class="text-md font-bold text-gray-800 mb-1">Laporan Hasil Kinerja</h3>
        <p class="text-xs text-gray-400 mb-4">Pilih periode untuk melihat detail aktivitas Anda</p>

        <form action="{{ route('karyawan.akun.unduh') }}" method="GET" class="flex items-center space-x-2 mb-4">
            <input type="hidden" name="section" id="input-section" value="{{ request('section', '') }}">
            <select name="bulan" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none w-full">
                @foreach($daftarBulan as $num => $nama)
                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <select name="tahun" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 focus:outline-none w-28">
                @for($y = date('Y'); $y >= date('Y')-2; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-[#1e3f7c] text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-sm hover:bg-opacity-90">Cari</button>
        </form>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <a href="{{ route('karyawan.akun.cetak_pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="bg-red-500 text-white text-center rounded-xl py-2.5 text-xs font-bold shadow-sm hover:bg-red-600 transition">Cetak PDF</a>
            <a href="{{ route('karyawan.akun.cetak_excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="bg-emerald-600 text-white text-center rounded-xl py-2.5 text-xs font-bold shadow-sm hover:bg-emerald-700 transition">Ekspor Excel</a>
        </div>
    </div>

    {{-- SECTION ABSENSI --}}
    <div id="section-absensi" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 space-y-3">
        <div class="flex items-center space-x-2 text-gray-800 font-bold text-sm border-b border-gray-50 pb-2">
            <h4>Log & Riwayat Absensi</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-2 font-semibold">Tanggal</th>
                        <th class="pb-2 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($detailAbsensi as $abs)
                    @php
                        $absStatus = $abs->status;
                        $absLabel = match(strtolower($absStatus)) {
                            'hadir'                      => 'Hadir',
                            'tepat waktu'                => 'Tepat Waktu',
                            'terlambat'                  => 'Terlambat',
                            'tidak hadir', 'tidak_hadir' => 'Tidak Hadir',
                            default                      => ucfirst(str_replace('_', ' ', $absStatus)),
                        };
                        $absClass = match(strtolower($absStatus)) {
                            'hadir', 'tepat waktu' => 'bg-green-50 text-green-600',
                            'terlambat'            => 'bg-blue-50 text-blue-600',
                            default                => 'bg-red-50 text-red-600',
                        };
                    @endphp
                    <tr>
                        <td class="py-3">{{ date('d F Y', strtotime($abs->tanggal)) }}</td>
                        <td class="py-3 text-right">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $absClass }}">
                                {{ $absLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-400">Tidak ada log absensi bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION MISI --}}
    <div id="section-misi" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 space-y-3">
        <div class="flex items-center space-x-2 text-gray-800 font-bold text-sm border-b border-gray-50 pb-2">
            <h4>Log Misi Harian</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-2 font-semibold">Tanggal</th>
                        <th class="pb-2 font-semibold">Nama Misi</th>
                        <th class="pb-2 font-semibold text-center">Poin</th>
                        <th class="pb-2 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($detailMisi as $dm)
                    @php
                        $misiStatus = $dm->status;
                        $misiLabel = match($misiStatus) {
                            'disetujui'         => 'Disetujui',
                            'terlambat'         => 'Terlambat',
                            'tidak_mengerjakan' => 'Tidak Mengerjakan',
                            'belum_mengerjakan' => 'Belum Mengerjakan',
                            default             => ucfirst(str_replace('_', ' ', $misiStatus)),
                        };
                        $misiClass = match($misiStatus) {
                            'disetujui'         => 'bg-green-50 text-green-600',
                            'terlambat'         => 'bg-blue-50 text-blue-600',
                            'tidak_mengerjakan' => 'bg-red-50 text-red-600',
                            default             => 'bg-amber-50 text-amber-600',
                        };
                        $poinTampil = in_array($misiStatus, ['disetujui', 'terlambat'])
                            ? $dm->poin_didapat
                            : 0;
                        $poinMisi = $dm->misi->poin ?? '-';
                    @endphp
                    <tr>
                        <td class="py-3 pr-2 text-gray-500 whitespace-nowrap">{{ date('d M Y', strtotime($dm->tanggal)) }}</td>
                        <td class="py-3 pr-2 max-w-[120px] truncate">{{ $dm->misi->nama_misi ?? '-' }}</td>
                        <td class="py-3 text-center font-medium">
                            {{ $poinTampil }}<span class="text-gray-400">/{{ $poinMisi }}</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $misiClass }}">
                                {{ $misiLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-400">Tidak ada log misi harian bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION TUGAS --}}
    <div id="section-tugas" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 space-y-3">
        <div class="flex items-center space-x-2 text-gray-800 font-bold text-sm border-b border-gray-50 pb-2">
            <h4>Log Pengumpulan Tugas</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-2 font-semibold">Minggu</th>
                        <th class="pb-2 font-semibold">Nama Tugas</th>
                        <th class="pb-2 font-semibold text-center">Pengumpulan</th>
                        <th class="pb-2 font-semibold text-center">Poin</th>
                        <th class="pb-2 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($detailTugas as $dt)
                    @php
                        $tugasStatus = $dt->status;
                        $tugasLabel = match($tugasStatus) {
                            'disetujui'         => 'Disetujui',
                            'terlambat'         => 'Terlambat',
                            'tidak_mengerjakan' => 'Tidak Mengerjakan',
                            'belum_mengerjakan' => 'Belum Mengerjakan',
                            'selesai'           => 'Selesai',
                            default             => ucfirst(str_replace('_', ' ', $tugasStatus)),
                        };
                        $tugasClass = match($tugasStatus) {
                            'disetujui', 'selesai' => 'bg-green-50 text-green-600',
                            'terlambat'            => 'bg-blue-50 text-blue-600',
                            'tidak_mengerjakan'    => 'bg-red-50 text-red-600',
                            default                => 'bg-amber-50 text-amber-600',
                        };
                        $poinTampil = in_array($tugasStatus, ['disetujui', 'terlambat', 'selesai'])
                            ? $dt->poin_didapat
                            : 0;
                        $poinTugas = $dt->tugas->poin ?? '-';
                        $tglKumpul = $dt->tanggal_upload ? date('d M Y', strtotime($dt->tanggal_upload)) : '-';
                    @endphp
                    <tr>
                        <td class="py-3 pr-2 whitespace-nowrap text-gray-500">Minggu {{ $dt->tugas->minggu ?? '-' }}</td>
                        <td class="py-3 pr-2 max-w-[120px] truncate">{{ $dt->tugas->nama_tugas ?? '-' }}</td>
                        <td class="py-3 text-center text-gray-500 whitespace-nowrap">{{ $tglKumpul }}</td>
                        <td class="py-3 text-center font-medium">
                            {{ $poinTampil }}<span class="text-gray-400">/{{ $poinTugas }}</span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $tugasClass }}">
                                {{ $tugasLabel }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-400">Tidak ada log pengumpulan tugas bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <a href="{{ route('karyawan.akun.index') }}" class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-3 rounded-xl transition">Kembali ke Menu Utama</a>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section') || '';
        const hash = window.location.hash || (sectionParam ? '#' + sectionParam : '');

        const secAbsensi = document.getElementById('section-absensi');
        const secMisi    = document.getElementById('section-misi');
        const secTugas   = document.getElementById('section-tugas');

        if (hash === '#absensi') {
            if (secMisi)    secMisi.remove();
            if (secTugas)   secTugas.remove();
        } else if (hash === '#misi') {
            if (secAbsensi) secAbsensi.remove();
            if (secTugas)   secTugas.remove();
        } else if (hash === '#tugas') {
            if (secAbsensi) secAbsensi.remove();
            if (secMisi)    secMisi.remove();
        }

        // Simpan hash ke hidden input sebelum form submit
        const inputSection = document.getElementById('input-section');
        const form = document.querySelector('form');
        if (form && inputSection) {
            form.addEventListener('submit', function() {
                const currentHash = window.location.hash.replace('#', '');
                if (currentHash) inputSection.value = currentHash;
            });
        }

        // Restore hash di URL setelah page load dari query param
        if (sectionParam && !window.location.hash) {
            history.replaceState(null, '', window.location.pathname + window.location.search + '#' + sectionParam);
        }
    });
</script>
@endsection