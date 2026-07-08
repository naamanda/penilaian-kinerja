<?php

namespace App\Services;

use App\Models\Pengerjaan;
use App\Models\Pengumpulan;
use App\Models\Karyawan;
use App\Models\Misi;
use App\Models\Tugas;
use Carbon\Carbon;

class AutoResetService
{
    public static function jalankan()
    {
        $today     = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $minggu    = Carbon::now()->weekOfMonth;
        $bulan     = Carbon::now()->month;

        $karyawanList = Karyawan::where('id_role', 2)->get();

        $tugasList = Tugas::where('minggu', $minggu)
            ->where('bulan', $bulan)
            ->get();

        // 1. LOGIKA RESET HARIAN (MISI)
        $sudahResetHariIni = Pengerjaan::where('tanggal', $today)->exists();

        if (!$sudahResetHariIni) {
            Pengerjaan::where('tanggal', $yesterday)
                ->where('status', 'menunggu')
                ->update(['status' => 'ditolak']);

            $misiList = Misi::all();
            foreach ($karyawanList as $k) {
                foreach ($misiList as $m) {
                    Pengerjaan::create([
                        'id_karyawan'  => $k->id_karyawan,
                        'id_misi'      => $m->id_misi,
                        'tanggal'      => $today,
                        'status'       => 'belum_mengerjakan',
                        'poin_didapat' => 0,
                    ]);
                }
            }
        }

        // 2. LOGIKA RESET MINGGUAN (TUGAS MINGGUAN)
        foreach ($karyawanList as $k) {
            foreach ($tugasList as $t) {
                if ($k->id_divisi != $t->id_divisi) continue;

                Pengumpulan::firstOrCreate(
                    ['id_karyawan' => $k->id_karyawan, 'id_tugas' => $t->id_tugas],
                    ['status' => 'belum_mengerjakan', 'poin_didapat' => 0]
                );
            }
        }
    }
}
