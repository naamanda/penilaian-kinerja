<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    protected $table = 'izin';
    protected $primaryKey = 'id_izin';
    public $timestamps = false;

    protected $fillable = [
        'id_karyawan',
        'id_absensi',
        'tanggal_izin',
        'file_izin',
        'keterangan',
        'status',
        'tanggal_pengajuan',
    ];

    protected $casts = [
        'tanggal_izin'      => 'date',
        'tanggal_pengajuan' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id_absensi');
    }
}