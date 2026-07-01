<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Kinerja Karyawan - Periode {{ $bulan }}/{{ $tahun }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e3f7c;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            color: #1e3f7c;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }

        .meta-info {
            margin-bottom: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #f5f7fa;
            color: #1e3f7c;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #fbfbfb;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-warning {
            background-color: #fff8e1;
            color: #f57f17;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>LifeSync - Rekap Kinerja Karyawan</h2>
        <p>Laporan Akumulasi Aktivitas, Kehadiran, dan Kedisplinan Karyawan</p>
    </div>

    <div class="meta-info">
        Periode Laporan: {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ $tahun }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th class="text-center">Total Hadir</th>
                <th class="text-center">Misi Selesai</th>
                <th class="text-center">Tugas Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapKaryawan as $item)
            <tr>
                <td style="font-weight: bold;">{{ $item->nama }}</td>
                <td class="text-center">
                    <span class="badge badge-success">{{ $item->total_hadir }} Hari</span>
                </td>
                <td class="text-center" style="color: #2563eb; font-weight: bold;">{{ $item->misi_count }} Misi</td>
                <td class="text-center" style="color: #7c3aed; font-weight: bold;">{{ $item->tugas_count }} Tugas</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #999; padding: 20px;">
                    Tidak ada data aktivitas kuantitas pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>