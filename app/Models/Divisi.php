<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $primaryKey = 'id_divisi';

    protected $fillable = [
        'nama_divisi',
        'tempat_kerja'
    ];

    public $timestamps = false;

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_divisi');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_divisi');
    }
}