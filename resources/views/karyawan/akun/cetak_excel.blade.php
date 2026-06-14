<table border="1">
    <tr>
        <td colspan="3" style="font-weight: bold; text-align: center; font-size: 14px; background-color: #1e3f7c; color: white;">LAPORAN HASIL KINERJA KARYAWAN</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center; font-size: 11px;">Periode Penilaian: {{ $namaBulan }} {{ $tahun }}</td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
        <td><b>Nama Karyawan:</b></td>
        <td colspan="2">{{ $hasilAkhir->karyawan->nama ?? $hasilAkhir->karyawan->name }}</td>
    </tr>
    <tr>
        <td><b>Jabatan / Divisi:</b></td>
        <td colspan="2">{{ $hasilAkhir->karyawan->divisi->nama_divisi ?? $hasilAkhir->karyawan->jabatan ?? 'Karyawan' }}</td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr style="background-color: #f1f5f9;">
        <td colspan="3"><b>Rincian Nilai Komponen</b></td>
    </tr>
    <tr>
        <td>Nilai Kehadiran</td>
        <td colspan="2" style="text-align: right;">{{ $hasilAkhir->nilai_kehadiran }}</td>
    </tr>
    <tr>
        <td>Nilai Kedisiplinan</td>
        <td colspan="2" style="text-align: right;">{{ $hasilAkhir->nilai_kedisiplinan }}</td>
    </tr>
    <tr>
        <td>Nilai Tugas</td>
        <td colspan="2" style="text-align: right;">{{ $hasilAkhir->nilai_tugas }}</td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr style="background-color: #fef7e0;">
        <td><b>Akumulasi Nilai Akhir</b></td>
        <td colspan="2" style="text-align: right; font-weight: bold;">{{ $hasilAkhir->nilai_akhir }}</td>
    </tr>
    <tr style="background-color: #fef7e0;">
        <td><b>Predikat Kinerja</b></td>
        <td colspan="2" style="text-align: right; font-weight: bold; color: #b45309;">{{ $hasilAkhir->predikat }}</td>
    </tr>
</table>