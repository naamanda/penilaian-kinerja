<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengerjaan extends Model
{
    protected $table = 'pengerjaan';
    protected $primaryKey = 'id_pengerjaan';
    public $timestamps = false;

    protected $fillable = [
        'id_misi',
        'id_karyawan',
        'tanggal',
        'waktu_upload',
        'foto',
        'poin_didapat',
        'status',
    ];

    public function misi()
    {
        return $this->belongsTo(Misi::class, 'id_misi', 'id_misi');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}