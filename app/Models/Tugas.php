<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $primaryKey = 'id_tugas';

    protected $fillable = [
        'id_divisi',
        'nama_tugas',
        'deskripsi',
        'minggu',
        'bulan',
        'deadline',
        'poin'
    ];

    public $timestamps = false;

    // Relasi ke tabel pengumpulan
    public function pengumpulan()
    {
        return $this->hasMany(Pengumpulan::class, 'id_tugas', 'id_tugas');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }
}