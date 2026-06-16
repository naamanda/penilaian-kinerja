<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengerjaan;
use Carbon\Carbon;

class ApproveMisiController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->input('tab', 'antrean');
        $search = $request->input('search');
        $bulan  = (int) $request->get('bulan', date('n'));
        $tahun  = (int) $request->get('tahun', date('Y'));

        // Cek apakah hari ini Libur Nasional atau Weekend
        $hariIniLibur = \App\Helpers\HariLiburHelper::isLibur(Carbon::today()) || Carbon::today()->isWeekend();

        $query = Pengerjaan::with(['misi', 'karyawan'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($tab == 'antrean') {
            $query->whereIn('status', ['menunggu', 'ditolak'])
                ->orderByRaw("FIELD(status, 'menunggu', 'ditolak') ASC");
        } elseif ($tab == 'belum_mengerjakan') {
            if ($hariIniLibur) {
                // Hari libur: kosongkan query pengerjaan belum_mengerjakan
                $query->whereRaw('1 = 0');
            } else {
                $query->where('status', 'belum_mengerjakan')
                    ->whereDate('tanggal', Carbon::today())
                    ->whereHas('misi', fn($m) => $m->where(
                        'waktu_selesai',
                        '>',
                        Carbon::now()->subMinutes(10)->format('H:i:s')
                    ));
            }
        } elseif ($tab == 'selesai') {
            $query->whereIn('status', ['disetujui', 'terlambat'])
                ->whereDate('tanggal', Carbon::today());
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('misi', function ($q2) use ($search) {
                    $q2->where('nama_misi', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('karyawan', function ($q2) use ($search) {
                        $q2->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        $data = $query->orderBy('tanggal', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Mengatur statistik counter berdasarkan kondisi hari libur harian
        $stat = [
            'belum'     => $hariIniLibur ? 0 : Pengerjaan::where('status', 'belum_mengerjakan')->whereDate('tanggal', Carbon::today())->count(),
            'menunggu'  => Pengerjaan::where('status', 'menunggu')->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->count(), // Antrean bulanan tetap dihitung
            'terlambat' => $hariIniLibur ? 0 : Pengerjaan::where('status', 'terlambat')->whereDate('tanggal', Carbon::today())->count(),
            'disetujui' => $hariIniLibur ? 0 : Pengerjaan::where('status', 'disetujui')->whereDate('tanggal', Carbon::today())->count(),
        ];

        return view('admin.misi.approve.index', compact('data', 'stat', 'tab', 'bulan', 'tahun', 'hariIniLibur'));
    }

    public function show($id)
    {
        $misi = Pengerjaan::with(['misi', 'karyawan'])->findOrFail($id);
        return view('admin.misi.approve.detail', compact('misi'));
    }

    public function approve($id)
    {
        $pengerjaan = Pengerjaan::with('misi')->findOrFail($id);
        $misi       = $pengerjaan->misi;

        $waktuUpload       = Carbon::parse($pengerjaan->waktu_upload);
        $deadline          = Carbon::parse($misi->waktu_selesai);
        $deadlineToleransi = $deadline->copy()->addMinutes(10);

        if ($waktuUpload->lte($deadline)) {
            $pengerjaan->status       = 'disetujui';
            $pengerjaan->poin_didapat = $misi->poin ?? 0;
        } else {
            // Pasti dalam toleransi 10 menit, karena kalau lebih sudah dicegah di karyawan
            $pengerjaan->status       = 'terlambat';
            $pengerjaan->poin_didapat = intval(($misi->poin ?? 0) / 2);
        }

        $pengerjaan->save();
        return redirect('/approve-misi?tab=antrean')->with('success', 'Misi berhasil disetujui.');
    }

    public function reject($id)
    {
        $pengerjaan = Pengerjaan::findOrFail($id);
        $pengerjaan->update([
            'status' => 'ditolak',
            'poin_didapat' => 0
        ]);

        return redirect('/approve-misi?tab=antrean')->with('success', 'Misi ditolak. Menunggu karyawan upload ulang.');
    }
}
