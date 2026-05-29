<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Pelanggaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Tentukan koordinat kantor secara hardcode.
     * Silahkan sesuaikan lat & lng dengan lokasi kantor asli.
     */
    private $office_lat =  -7.678603;
    private $office_lng = 109.035448;
    private $radius_km = 0.1; // 100 meter toleransi jarak

    // ==========================================
    // POV ADMIN (Tampilan Desktop)
    // ==========================================

    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab    = $request->input('tab', 'semua'); // tab aktif: semua | hari_ini | tidak_hadir
        $today  = Carbon::today()->toDateString();

        // ── Tab: Tidak Hadir Hari Ini ──────────────────────────────────────
        if ($tab === 'tidak_hadir') {
            $sudahAbsenIds = Absensi::whereDate('tanggal', $today)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->pluck('id_karyawan');

            $karyawanTidakHadir = \App\Models\Karyawan::whereNotIn('id_karyawan', $sudahAbsenIds)
                ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
                ->paginate(5)
                ->withQueryString();

            return view('admin.absensi.index', [
                'data'               => Absensi::query()->paginate(5), // ← pakai Eloquent, bukan collect()
                'karyawanTidakHadir' => $karyawanTidakHadir,
                'tab'                => $tab,
                'today'              => $today,
            ]);
        }

        // ── Tab: Hadir & Terlambat Hari Ini ───────────────────────────────
        $query = Absensi::with('karyawan');

        if ($tab === 'hari_ini') {
            $query->whereDate('tanggal', $today)
                ->whereIn('status', ['hadir', 'terlambat']);
        } else {
            // Tab: Semua — logika filter asli
            if ($search) {
                $query->whereHas('karyawan', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%");
            }
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        $data = $query->orderBy('tanggal', 'desc')->paginate(5)->withQueryString();

        return view('admin.absensi.index', [
            'data'               => $data,
            'karyawanTidakHadir' => collect(),
            'tab'                => $tab,
            'today'              => $today,
        ]);
    }

    public function show($id)
    {
        // Menampilkan detail untuk admin (Foto, Lokasi, Waktu)
        $absensi = Absensi::with('karyawan')->findOrFail($id);
        return view('admin.absensi.detail', compact('absensi'));
    }

    public function destroy($id)
    {
        $absensi = Absensi::where('id_absensi', $id)->firstOrFail();

        // --- FIXED: HAPUS FOTO DI PUBLIC PATH ---
        if ($absensi->foto && file_exists(public_path('uploads/absensi/' . $absensi->foto))) {
            unlink(public_path('uploads/absensi/' . $absensi->foto));
        }

        $absensi->delete();

        return back()->with('success', 'Catatan absensi berhasil dihapus tanpa menghapus data karyawan.');
    }

    // ==========================================
    // POV KARYAWAN (Tampilan Mobile Web)
    // ==========================================

    public function store(Request $request)
    {
        // 1. VALIDASI INPUT
        $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'foto'        => 'required'
        ]);

        $now = Carbon::now();
        $tanggal = $now->toDateString();
        $jamMenit = $now->format('H:i');

        // 2. CEK APAKAH SUDAH ABSEN HARI INI?
        $sudahAbsen = Absensi::where('id_karyawan', $request->id_karyawan)
            ->where('tanggal', $tanggal)
            ->exists();
        if ($sudahAbsen) {
            return response()->json(['message' => 'Anda sudah melakukan absensi hari ini!'], 403);
        }

        // 3. VALIDASI LOKASI (GEOFENCING)
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $this->office_lat, $this->office_lng);
        if ($distance > $this->radius_km) {
            return response()->json(['message' => 'Gagal! Posisi Anda di luar radius kantor.'], 403);
        }

        // 4. VALIDASI WAKTU
        if ($jamMenit < '07:30') {
            return response()->json(['message' => 'Absensi belum dibuka. Silahkan tunggu jam 07:30.'], 403);
        }

        $status = ($jamMenit <= '08:00') ? 'hadir' : 'terlambat';

        // 5. EKSEKUSI DATABASE
        return DB::transaction(function () use ($request, $tanggal, $now, $status) {

            // --- FIXED: PENYIMPANAN FOTO LANGSUNG KE PUBLIC PATH ---
            $img = $request->foto;
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $request->id_karyawan . '_' . time() . '.png';

            $destinationPath = public_path('uploads/absensi/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            file_put_contents($destinationPath . $fileName, $image_base64);

            Absensi::create([
                'id_karyawan' => $request->id_karyawan,
                'tanggal'     => $tanggal,
                'waktu'       => $now->toTimeString(),
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'foto'        => $fileName,
                'status'      => $status
            ]);

            if ($status == 'terlambat') {
                $this->updatePoinPelanggaran($request->id_karyawan, 'terlambat');
            }

            return response()->json([
                'message' => 'Absensi berhasil disimpan!',
                'status'  => $status,
                'misi_terbuka' => true
            ]);
        });
    }

    // ==========================================
    // PRIVATE LOGIC (INTERNAL)
    // ==========================================

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return ($dist * 60 * 1.1515) * 1.609344;
    }

    private function updatePoinPelanggaran($id_karyawan, $jenis)
    {
        $now = Carbon::now();

        $p = Pelanggaran::firstOrCreate(
            [
                'id_karyawan' => $id_karyawan,
                'bulan' => $now->month,
                'tahun' => $now->year
            ]
        );

        if ($jenis == 'terlambat') {
            $p->total_terlambat += 1;
            $p->total_poinpl += 1;
        } else {
            $p->total_tidakmengerjakan += 1;
            $p->total_poinpl += 2;
        }

        if ($p->total_poinpl >= 9) {
            $p->status = 'SP2';
        } elseif ($p->total_poinpl >= 5) {
            $p->status = 'SP1';
        } else {
            $p->status = 'aman';
        }

        $p->save();
    }
}
