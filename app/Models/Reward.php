<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $table = 'reward';

    protected $primaryKey = 'id_reward';

    protected $fillable = [
        'id_hasilakhir',
        'nama_reward',
        'jenis',
        'nominal'
    ];

    public $timestamps = false;

    public function hasilakhir()
    {
        return $this->belongsTo(HasilAkhir::class, 'id_hasilakhir');
    }
}