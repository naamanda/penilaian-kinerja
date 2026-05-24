<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengerjaan;
use App\Models\Karyawan;
use App\Models\Misi;
use Carbon\Carbon;

class ResetHarian extends Command
{
    protected $signature   = 'reset:harian';
    protected $description = 'Auto-reject misi kemarin yang menggantung dan buat record pengerjaan misi baru hari ini';

    public function handle()
    {
        $today     = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        
        $karyawan = Karyawan::where('id_role', 2)->get();
        $misiList = Misi::all();

        // 1. TAMBAHAN: Auto-Reject misi hari kemarin yang tipenya 'menunggu' tapi lupa di-approve admin
        Pengerjaan::where('tanggal', $yesterday)
            ->where('status', 'menunggu') // sesuaikan string status di databasemu
            ->update([
                'status' => 'ditolak', // atau 'expired'
            ]);

        // 2. Buat record baru untuk hari ini
        foreach ($karyawan as $k) {
            foreach ($misiList as $m) {
                $sudahAda = Pengerjaan::where('id_karyawan', $k->id_karyawan)
                    ->where('id_misi', $m->id_misi)
                    ->where('tanggal', $today)
                    ->exists();

                if (!$sudahAda) {
                    Pengerjaan::create([
                        'id_karyawan'  => $k->id_karyawan,
                        'id_misi'      => $m->id_misi,
                        'tanggal'      => $today,
                        'status'       => 'belum_mengerjakan',
                        'poin_didapat' => 0,
                        'foto'         => null,
                        'waktu_upload' => null,
                    ]);
                }
            }
        }

        $this->info('Reset harian selesai: ' . $today);
    }
}