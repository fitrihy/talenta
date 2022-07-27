<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiwayatJabatanLain extends Model
{
    protected $table = 'riwayat_jabatan_lain';

    protected $fillable = [
        'id_talenta',
        'penugasan',
        'tupoksi',
        'instansi',
        'tanggal_awal',
        'tanggal_akhir',
    ];

    
    public function talenta()
    {
        return $this->belongsTo('App\Talenta', 'id_talenta');
    }
}
