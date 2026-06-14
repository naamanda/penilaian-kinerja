<?php

namespace App\Helpers;

use Carbon\Carbon;
use Grei\TanggalMerah;
use Illuminate\Support\Facades\Cache;

class HariLiburHelper
{
    protected static function getInstance(): TanggalMerah
    {
        $jsonPath = storage_path('app/calendar.json');
        return new TanggalMerah($jsonPath);
    }

    public static function isLibur(Carbon $tanggal): bool
    {
        $t = self::getInstance();
        $t->set_date($tanggal->format('Ymd'));
        return $t->is_holiday();
    }

    public static function isHariKerja(Carbon $tanggal): bool
    {
        return $tanggal->isWeekday() && !self::isLibur($tanggal);
    }

    /**
     * Hitung total hari kerja dalam 1 bulan penuh (Senin-Jumat, dikurangi tanggal merah)
     */
    public static function getTotalHariKerjaBulan(int $bulan, int $tahun): int
    {
        return Cache::remember(
            "total_hari_kerja_{$bulan}_{$tahun}",
            now()->addDay(),
            function () use ($bulan, $tahun) {
                $total       = 0;
                $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $tgl = Carbon::create($tahun, $bulan, $d);
                    if (self::isHariKerja($tgl)) {
                        $total++;
                    }
                }

                return $total;
            }
        );
    }
}