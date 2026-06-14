<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kinerja PDF</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .title { text-align: center; color: #1e3f7c; font-size: 18px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .subtitle { text-align: center; margin-top: 0; color: #64748b; font-size: 12px; margin-bottom: 25px; }
        .line-divider { border-top: 2px solid #1e3f7c; margin-bottom: 25px; }
        
        .info-table { width: 100%; margin-bottom: 30px; font-size: 14px; }
        .info-table td { padding: 4px 0; }
        
        .section-title { font-weight: bold; color: #1e3f7c; text-transform: uppercase; font-size: 12px; margin-bottom: 15px; letter-spacing: 0.5px; }
        
        .component-table { width: 100%; text-align: center; margin-bottom: 35px; border-collapse: collapse; }
        .component-card { background-color: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; width: 31%; }
        .card-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .card-value { font-size: 20px; font-weight: bold; color: #1e3f7c; margin-top: 5px; }
        
        .predikat-box { border: 2px dashed #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; background-color: #fffdfa; margin-bottom: 50px; }
        .predikat-label { font-size: 11px; font-weight: bold; color: #b45309; text-transform: uppercase; letter-spacing: 1px; }
        .predikat-value { font-size: 42px; font-weight: bold; color: #b45309; margin: 10px 0; }
        .total-value { font-size: 12px; color: #475569; }

        .footer-sign { float: right; text-align: center; width: 250px; margin-top: 40px; font-size: 13px; }
        .footer-date { margin-bottom: 60px; color: #333; }
    </style>
</head>
<body>

    <div class="title">Laporan Hasil Kinerja Bulanan Karyawan</div>
    <div class="subtitle">Periode Penilaian: {{ $namaBulan }} {{ $tahun }}</div>

    <div class="line-divider"></div>

    <table class="info-table">
        <tr>
            <td style="width: 22%;"><strong>Nama Karyawan</strong></td>
            <td style="width: 3%;">:</td>
            <td style="width: 75%;">{{ $hasilAkhir->karyawan->nama ?? $hasilAkhir->karyawan->name }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan / Divisi</strong></td>
            <td>:</td>
            <td>{{ $hasilAkhir->karyawan->divisi->nama_divisi ?? $hasilAkhir->karyawan->jabatan ?? 'IT' }}</td>
        </tr>
    </table>

    <div class="section-title">Rincian Nilai Komponen</div>
    <table class="component-table">
        <tr>
            <td class="component-card">
                <div class="card-label">Nilai Kehadiran</div>
                <div class="card-value">{{ number_format($hasilAkhir->nilai_kehadiran, 2) }}</div>
            </td>
            <td style="width: 3%;"></td>
            <td class="component-card">
                <div class="card-label">Nilai Kedisiplinan</div>
                <div class="card-value">{{ number_format($hasilAkhir->nilai_kedisiplinan, 2) }}</div>
            </td>
            <td style="width: 3%;"></td>
            <td class="component-card">
                <div class="card-label">Nilai Tugas</div>
                <div class="card-value">{{ number_format($hasilAkhir->nilai_tugas, 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="predikat-box">
        <div class="predikat-label">Hasil Evaluasi Predikat Kinerja</div>
        <div class="predikat-value">{{ $hasilAkhir->predikat }}</div>
        <div class="total-value">Akumulasi Nilai Akhir: <strong>{{ number_format($hasilAkhir->nilai_akhir, 2) }}</strong></div>
    </div>

    <div class="footer-sign">
        <div class="footer-date">Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $hasilAkhir->karyawan->nama ?? $hasilAkhir->karyawan->name }}</div>
        <div style="color: #64748b; font-size: 11px;">Karyawan Bersangkutan</div>
    </div>

</body>
</html>