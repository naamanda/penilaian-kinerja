<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';

    protected $primaryKey = 'id_pelanggaran';

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'total_terlambat',
        'total_tidakmengerjakan',
        'total_poinpl',
        'status',
        'file_sp',
        'tanggal_sp'
    ];

    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }
}