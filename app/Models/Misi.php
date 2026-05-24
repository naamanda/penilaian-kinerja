<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Misi extends Model
{
    protected $table = 'misi';
    protected $primaryKey = 'id_misi';
    public $timestamps = false;

    protected $fillable = [
        'nama_misi',
        'deskripsi',
        'poin',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'id_misi', 'id_misi');
    }
}