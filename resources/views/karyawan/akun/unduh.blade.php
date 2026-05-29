@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-5">
    
    {{-- Tombol Kembali --}}
    <a href="{{ route('karyawan.akun') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-[#1e3f7c] transition">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Akun
    </a>

    {{-- Filter Bulan --}}
    <form action="{{ route('karyawan.akun.unduh') }}" method="GET" id="filterForm" class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Periode Evaluasi</label>
        <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm">
            @foreach($daftarBulan as $key => $namaBulan)
                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                    {{ $namaBulan }} {{ $tahun }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="tahun" value="{{ $tahun }}">
    </form>

    @if($hasilAkhir)
    {{-- TAMPILAN INTERAKTIF DI LAYAR APLIKASI HP --}}
    <div class="space-y-4">
        <div class="bg-gradient-to-br from-[#1e3f7c] to-blue-800 rounded-3xl p-5 text-white shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-10 text-9xl font-black">
                {{ $hasilAkhir->predikat }}
            </div>
            <p class="text-xs font-medium text-blue-200 uppercase tracking-widest">Nilai Akhir Akumulasi</p>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-4xl font-black tracking-tight">{{ number_format($hasilAkhir->nilai_akhir, 2) }}</span>
                <span class="text-sm text-blue-200">/ 100</span>
            </div>
            <div class="mt-4 inline-flex items-center space-x-2 bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10">
                <i class="fas fa-award text-yellow-300 text-xs"></i>
                <span class="text-xs font-bold tracking-wide">Predikat:  {{ $hasilAkhir->predikat }}</span>
            </div>
        </div>

        <div class="space-y-2.5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider px-1">Rincian Performa Pilar</p>
            
            <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-50 text-blue-600 p-3 rounded-xl">
                        <i class="fas fa-calendar-check text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Kehadiran</h4>
                        <p class="text-[11px] text-gray-400">Rekapitulasi Absensi Bulanan</p>
                    </div>
                </div>
                <span class="text-sm font-extrabold text-gray-800 bg-gray-50 px-2.5 py-1.5 rounded-lg">{{ number_format($hasilAkhir->nilai_kehadiran, 2) }}</span>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="bg-orange-50 text-orange-600 p-3 rounded-xl">
                        <i class="fas fa-bolt text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Kedisiplinan</h4>
                        <p class="text-[11px] text-gray-400">Pengerjaan Misi Harian</p>
                    </div>
                </div>
                <span class="text-sm font-extrabold text-gray-800 bg-gray-50 px-2.5 py-1.5 rounded-lg">{{ number_format($hasilAkhir->nilai_kedisiplinan, 2) }}</span>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="bg-green-50 text-green-600 p-3 rounded-xl">
                        <i class="fas fa-tasks text-base"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Tugas</h4>
                        <p class="text-[11px] text-gray-400">Pengumpulan Tugas Mingguan</p>
                    </div>
                </div>
                <span class="text-sm font-extrabold text-gray-800 bg-gray-50 px-2.5 py-1.5 rounded-lg">{{ number_format($hasilAkhir->nilai_tugas, 2) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="downloadPDF()" class="flex items-center justify-center space-x-2 py-3.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition text-xs font-bold shadow-sm cursor-pointer border-none">
                <i class="fas fa-file-pdf text-sm"></i>
                <span>Unduh PDF</span>
            </button>
            <button type="button" onclick="downloadExcel()" class="flex items-center justify-center space-x-2 py-3.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-xl transition text-xs font-bold shadow-sm cursor-pointer border-none">
                <i class="fas fa-file-excel text-sm"></i>
                <span>Unduh Excel</span>
            </button>
        </div>
    </div>


    {{-- TAMPILAN KHUSUS STRUKTUR PDF (Sengaja Dihidden dari layar HP agar tidak merusak visual app) --}}
    <div class="hidden">
        <div id="areaCetak" style="font-family: Arial, sans-serif; color: #333333; padding: 40px; background: #ffffff;">
            <div style="border-b: 2px solid #1e3f7c; padding-bottom: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: bold; color: #1e3f7c; margin: 0; letter-spacing: 0.5px;">LAPORAN HASIL KINERJA KARYAWAN</h1>
                    <p style="font-size: 13px; color: #666666; margin: 5px 0 0 0;">Sistem Penilaian Kinerja Karyawan — LifeSync</p>
                </div>
            </div>

            <table style="width: 100%; font-size: 13px; margin-bottom: 30px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px 0; color: #666666; width: 120px;">Periode Penilaian</td>
                    <td style="padding: 4px 0; font-weight: bold; color: #1e3f7c;">: {{ $daftarBulan[$bulan] }} {{ $tahun }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666666;">Waktu Cetak</td>
                    <td style="padding: 4px 0; font-weight: bold;">: {{ date('d-m-Y H:i') }} WIB</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 13px;">
                <thead>
                    <tr style="background-color: #1e3f7c; color: #ffffff; text-align: left;">
                        <th style="padding: 12px; font-weight: bold; border: 1px solid #1e3f7c;">PARAMETER EVALUASI UTAMA</th>
                        <th style="padding: 12px; font-weight: bold; border: 1px solid #1e3f7c; text-align: right; width: 120px;">BOBOT NILAI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #fcfcfc;">
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">Nilai Kehadiran (Rekapitulasi Absensi Kerja)</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: right; font-weight: bold;">{{ number_format($hasilAkhir->nilai_kehadiran, 2) }}</td>
                    </tr>
                    <tr style="background-color: #ffffff;">
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">Nilai Kedisiplinan (Akumulasi Misi Harian)</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: right; font-weight: bold;">{{ number_format($hasilAkhir->nilai_kedisiplinan, 2) }}</td>
                    </tr>
                    <tr style="background-color: #fcfcfc;">
                        <td style="padding: 12px; border: 1px solid #e5e7eb;">Nilai Tugas (Pengumpulan Modul Mingguan)</td>
                        <td style="padding: 12px; border: 1px solid #e5e7eb; text-align: right; font-weight: bold;">{{ number_format($hasilAkhir->nilai_tugas, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="border-top: 2px dashed #dddddd; margin-bottom: 20px;"></div>

            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr style="background-color: #f0f4fa;">
                    <td style="padding: 14px; font-weight: bold; color: #333333; border: 1px solid #d0dff2;">NILAI AKHIR RATA-RATA</td>
                    <td style="padding: 14px; font-weight: 900; color: #1e3f7c; text-align: right; font-size: 18px; border: 1px solid #d0dff2; width: 120px;">
                        {{ number_format($hasilAkhir->nilai_akhir, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 14px; font-weight: bold; color: #333333; border: 1px solid #e5e7eb;">PREDIKAT KOMPETENSI</td>
                    <td style="padding: 14px; font-weight: bold; color: #ffffff; background-color: #1e3f7c; text-align: center; border: 1px solid #1e3f7c;">
                        {{ $hasilAkhir->predikat }}
                    </td>
                </tr>
            </table>

            <div style="margin-top: 60px; text-align: right; font-size: 11px; color: #999999; border-top: 1px solid #eeeeee; padding-top: 10px;">
                * Dokumen elektronik ini diterbitkan secara sah oleh sistem manajemen performa LifeSync.
            </div>
        </div>
    </div>

    {{-- Data Element Excel Shadow --}}
    <table id="tabelExcelSembunyi" class="hidden">
        <thead>
            <tr><th colspan="2" style="font-size: 16px; font-weight: bold;">LAPORAN HASIL KINERJA KARYAWAN</th></tr>
            <tr><td>Periode Penilaian:</td><td>{{ $daftarBulan[$bulan] }} {{ $tahun }}</td></tr>
            <tr><td>Waktu Unduh:</td><td>{{ date('d-m-Y H:i') }}</td></tr>
        </thead>
        <tbody>
            <tr><td>Nilai Kehadiran (Absensi)</td><td>{{ $hasilAkhir->nilai_kehadiran }}</td></tr>
            <tr><td>Nilai Kedisiplinan (Misi Harian)</td><td>{{ $hasilAkhir->nilai_kedisiplinan }}</td></tr>
            <tr><td>Nilai Tugas (Tugas Mingguan)</td><td>{{ $hasilAkhir->nilai_tugas }}</td></tr>
            <tr style="font-weight: bold; background-color: #f3f4f6;"><td>Nilai Akhir</td><td>{{ $hasilAkhir->nilai_akhir }}</td></tr>
            <tr style="font-weight: bold;"><td>Predikat</td><td>{{ $hasilAkhir->predikat }}</td></tr>
        </tbody>
    </table>

    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-start space-x-3">
        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
        <p class="text-xs text-yellow-800 font-medium">Data performa dan laporan untuk bulan ini belum tersedia.</p>
    </div>
    @endif
</div>

{{-- Script Compiler html2pdf --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPDF() {
        const element = document.getElementById('areaCetak');
        const opsiKonfigurasi = {
            margin:       [15, 15, 15, 15],
            filename:     'Laporan_Kinerja_{{ $daftarBulan[$bulan] }}_{{ $tahun }}.pdf',
            image:        { type: 'jpeg', quality: 1.0 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                logging: false,
                letterRendering: true
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Mengambil container cetak tersembunyi untuk diexport menjadi berkas formal
        html2pdf().set(opsiKonfigurasi).from(element).save();
    }

    function downloadExcel() {
        var table = document.getElementById("tabelExcelSembunyi");
        var html = table.outerHTML;
        var blob = new Blob([html], { type: "application/vnd.ms-excel" });
        var url = URL.createObjectURL(blob);
        
        var a = document.createElement("a");
        a.href = url;
        a.download = "Laporan_Kinerja_{{ $daftarBulan[$bulan] }}_{{ $tahun }}.xls";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>
@endsection