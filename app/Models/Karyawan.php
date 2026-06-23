<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'id_role',
        'id_divisi',
        'tanggal_bergabung',
    ];

    protected $hidden = [
        'password'
    ];

    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan');
    }

    public function misi()
    {
        return $this->hasMany(Misi::class, 'id_karyawan');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_karyawan');
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'id_karyawan');
    }

    public function hasilakhir()
    {
        return $this->hasMany(HasilAkhir::class, 'id_karyawan');
    }

    public function reward()
    {
        return $this->hasMany(Reward::class, 'id_karyawan');
    }

    public function pengerjaan()
    {
        return $this->hasMany(Pengerjaan::class, 'id_karyawan', 'id_karyawan');
    }

    public function pengumpulan()
    {
        return $this->hasMany(Pengumpulan::class, 'id_karyawan', 'id_karyawan');
    }
}
