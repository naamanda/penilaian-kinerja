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
    private $office_lat = -7.6785720;
    private $office_lng = 109.0355009;
    private $radius_km = 0.1; // 100 meter toleransi jarak

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
            'foto'        => 'required' // Menangkap string base64 dari kamera
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

        // Tentukan status kehadiran
        $status = ($jamMenit <= '08:00') ? 'hadir' : 'terlambat';

        // 5. EKSEKUSI DATABASE (TRANSACTIONAL)
        return DB::transaction(function () use ($request, $tanggal, $now, $status) {

            // Proses Konversi Base64 ke File Image
            $img = $request->foto;
            $folderPath = "public/absensi/";
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $request->id_karyawan . '_' . time() . '.png';
            Storage::put($folderPath . $fileName, $image_base64);

            // Simpan ke Tabel Absensi
            Absensi::create([
                'id_karyawan' => $request->id_karyawan,
                'tanggal'     => $tanggal,
                'waktu'       => $now->toTimeString(),
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'foto'        => $fileName,
                'status'      => $status
            ]);

            // Jika status Terlambat, catat poin di tabel Pelanggaran
            if ($status == 'terlambat') {
                $this->updatePoinPelanggaran($request->id_karyawan, 'terlambat');
            }

            return response()->json([
                'message' => 'Absensi berhasil disimpan!',
                'status'  => $status,
                'misi_terbuka' => true // Trigger frontend untuk unlock misi harian
            ]);
        });
    }

    // ==========================================
    // POV ADMIN (Tampilan Desktop)
    // ==========================================

    public function index(Request $request)
    {
        // Ambil keyword dari input search
        $search = $request->input('search');
        $query = Absensi::with('karyawan');

        if ($search) {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })
                ->orWhere('status', 'like', "%{$search}%") // Contoh cari berdasarkan status
                ->orWhere('tanggal', 'like', "%{$search}%");     // Contoh cari berdasarkan tanggal
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Gunakan paginate(5) agar pas dengan layar tanpa scroll
        $data = $query->orderBy('tanggal', 'desc')->paginate(5)->withQueryString();

        return view('admin.absensi.index', compact('data'));
    }

    public function show($id)
    {
        // Menampilkan detail untuk admin (Foto, Lokasi, Waktu)
        $absensi = Absensi::with('karyawan')->findOrFail($id);
        return view('admin.absensi.detail', compact('absensi'));
    }

    public function destroy($id)
    {
        // Gunakan findOrFail untuk memastikan data ada sebelum dihapus
        $absensi = Absensi::where('id_absensi', $id)->firstOrFail();

        // Hapus fotonya jika ada
        if ($absensi->foto) {
            Storage::delete('uploads/absensi/' . $absensi->foto);
        }

        // Eksekusi hapus baris absensi
        $absensi->delete();

        return back()->with('success', 'Catatan absensi berhasil dihapus tanpa menghapus data karyawan.');
    }

    // ==========================================
    // PRIVATE LOGIC (INTERNAL)
    // ==========================================

    /**
     * Menghitung jarak antara 2 titik koordinat bumi.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return ($dist * 60 * 1.1515) * 1.609344;
    }

    /**
     * Update/Create record pelanggaran bulanan.
     */
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
            $p->total_poinpl += 1; // 1 poin terlambat
        } else {
            $p->total_tidakmengerjakan += 1;
            $p->total_poinpl += 2; // 2 poin tidak hadir/mengerjakan
        }

        // Logic SP (Surat Peringatan)
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
