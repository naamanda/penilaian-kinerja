<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengumpulan;
use App\Models\Karyawan;
use App\Models\Tugas;
use Carbon\Carbon;

class ResetMingguan extends Command
{
    protected $signature   = 'reset:mingguan';
    protected $description = 'Buat record pengumpulan tugas baru tiap awal minggu';

    public function handle()
    {
        $minggu   = Carbon::now()->weekOfMonth;
        $bulan    = Carbon::now()->month;
        $karyawan = Karyawan::where('id_role', 2)->get();

        // Ambil tugas minggu ini
        $tugasList = Tugas::where('minggu', $minggu)
            ->where('bulan', $bulan)
            ->get();

        foreach ($karyawan as $k) {
            foreach ($tugasList as $t) {
                $sudahAda = Pengumpulan::where('id_karyawan', $k->id_karyawan)
                    ->where('id_tugas', $t->id_tugas)
                    ->exists();

                if (!$sudahAda) {
                    Pengumpulan::create([
                        'id_karyawan'   => $k->id_karyawan,
                        'id_tugas'      => $t->id_tugas,
                        'status'        => 'belum_mengerjakan',
                        'poin_didapat'  => 0,
                        'file'          => null,
                        'tanggal_upload'=> null,
                        'waktu_upload'  => null,
                    ]);
                }
            }
        }

        $this->info('Reset mingguan selesai: minggu ' . $minggu . ' bulan ' . $bulan);
    }
}