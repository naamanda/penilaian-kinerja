<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumpulan extends Model
{
    protected $table = 'pengumpulan';

    protected $primaryKey = 'id_pengumpulan';

    protected $fillable = [
        'id_tugas',
        'id_karyawan',
        'tanggal_upload',
        'waktu_upload',
        'file',
        'poin_didapat',
        'status'
    ];

    public $timestamps = false;

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'id_tugas', 'id_tugas');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}