<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRekapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapKaryawan = Karyawan::withCount([
            'absensi as total_hadir' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'hadir');
            },
            'absensi as total_terlambat' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'terlambat');
            },
            'pengerjaan as misi_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->whereIn('status', ['disetujui', 'terlambat']);
            },
            'pengumpulan as tugas_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_upload', $bulan)
                    ->whereYear('tanggal_upload', $tahun)
                    ->whereIn('status', ['disetujui', 'terlambat']);
            },
        ])->get();

        return view('admin.rekap.index', compact('rekapKaryawan', 'bulan', 'tahun'));
    }

    public function downloadPdf(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapKaryawan = Karyawan::withCount([
            'absensi as total_hadir' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'hadir');
            },
            'absensi as total_terlambat' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'terlambat');
            },
            'pengerjaan as misi_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->whereIn('status', ['disetujui', 'terlambat']);
            },
            'pengumpulan as tugas_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_upload', $bulan)
                    ->whereYear('tanggal_upload', $tahun)
                    ->whereIn('status', ['disetujui', 'terlambat']);
            },
        ])->get();

        $namaBulan = \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F');

        $pdf = Pdf::loadView('admin.rekap.pdf', compact('rekapKaryawan', 'bulan', 'tahun'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("rekap-kinerja-{$namaBulan}-{$tahun}.pdf");
    }
}
