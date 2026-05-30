<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $reward->nama_reward }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            gap: 20px;
        }
        .download-btn {
            background: #1e3f7c;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-family: sans-serif;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .download-btn:hover { background: #163260; }
        #sertifikat {
            background: #fff;
            width: 277mm;
            height: 190mm;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .border-outer {
            position: absolute;
            inset: 8mm;
            border: 3px solid #1e3f7c;
        }
        .border-inner {
            position: absolute;
            inset: 11mm;
            border: 1px solid #c9a84c;
        }
        .corner {
            position: absolute;
            width: 14mm;
            height: 14mm;
            border-color: #c9a84c;
            border-style: solid;
        }
        .corner-tl { top: 13mm; left: 13mm; border-width: 2px 0 0 2px; }
        .corner-tr { top: 13mm; right: 13mm; border-width: 2px 2px 0 0; }
        .corner-bl { bottom: 13mm; left: 13mm; border-width: 0 0 2px 2px; }
        .corner-br { bottom: 13mm; right: 13mm; border-width: 0 2px 2px 0; }
        .content {
            text-align: center;
            padding: 0 30mm;
            position: relative;
            z-index: 10;
            width: 100%;
        }
        .logo-text {
            font-family: sans-serif;
            font-size: 13pt;
            font-weight: bold;
            color: #1e3f7c;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }
        .title {
            font-size: 8.5pt;
            color: #888;
            letter-spacing: 4px;
            text-transform: uppercase;
            font-family: sans-serif;
            margin-bottom: 5mm;
        }
        .divider-gold {
            width: 40mm;
            height: 1px;
            background: linear-gradient(to right, transparent, #c9a84c, transparent);
            margin: 0 auto 5mm;
        }
        .reward-name {
            font-size: 21pt;
            font-weight: bold;
            color: #1e3f7c;
            margin-bottom: 6mm;
            line-height: 1.25;
        }
        .subtitle {
            font-size: 9.5pt;
            color: #777;
            font-family: sans-serif;
            margin-bottom: 2mm;
            font-style: italic;
        }
        .recipient-name {
            font-size: 24pt;
            color: #c9a84c;
            font-style: italic;
            margin-bottom: 1mm;
            line-height: 1.2;
        }
        .recipient-divisi {
            font-size: 9pt;
            color: #888;
            font-family: sans-serif;
            margin-bottom: 5mm;
        }
        .divider-main {
            width: 70mm;
            height: 1px;
            background: linear-gradient(to right, transparent, #c9a84c, transparent);
            margin: 0 auto 5mm;
        }
        .info-row {
            display: flex;
            justify-content: center;
            gap: 12mm;
        }
        .info-item { font-family: sans-serif; text-align: center; }
        .info-label {
            font-size: 7.5pt;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1mm;
        }
        .info-val {
            font-size: 9.5pt;
            font-weight: bold;
            color: #444;
        }
        @media print { .download-btn { display: none; } }
    </style>
</head>
<body>

    <button class="download-btn" onclick="unduhPDF()">
        ⬇ Unduh Sertifikat PDF
    </button>

    <div id="sertifikat">
        <div class="border-outer"></div>
        <div class="border-inner"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="content">
            <div class="logo-text">LifeSync</div>
            <div class="title">Sertifikat Penghargaan</div>
            <div class="divider-gold"></div>
            <div class="reward-name">{{ $reward->nama_reward }}</div>
            <div class="subtitle">Diberikan kepada</div>
            <div class="recipient-name">{{ $reward->hasilakhir->karyawan->nama }}</div>
            <div class="recipient-divisi">Divisi {{ $reward->hasilakhir->karyawan->divisi->nama_divisi ?? '-' }}</div>
            <div class="divider-main"></div>
            <div class="info-row">
                <div class="info-item">
                    <div class="info-label">Periode Penilaian</div>
                    <div class="info-val">{{ $namaBulan }} {{ $tahun }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nilai Akhir</div>
                    <div class="info-val">{{ $reward->hasilakhir->nilai_akhir }} / 100</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Predikat</div>
                    <div class="info-val">{{ $reward->hasilakhir->predikat }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function unduhPDF() {
            const element = document.getElementById('sertifikat');
            const namaFile = 'Sertifikat_{{ Str::slug($reward->nama_reward) }}_{{ $namaBulan }}_{{ $tahun }}.pdf';
            const opt = {
                margin: 0,
                filename: namaFile,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>