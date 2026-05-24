<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhir extends Model
{
    protected $table = 'hasil_akhir';

    protected $primaryKey = 'id_hasilakhir';

    protected $fillable = [
        'id_karyawan',
        'bulan',
        'tahun',
        'total_harikerja',
        'nilai_kehadiran',
        'nilai_kedisiplinan',
        'nilai_tugas',
        'nilai_akhir',
        'predikat'
    ];

    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function reward()
    {
        return $this->hasMany(Reward::class, 'id_hasilakhir');
    }
}