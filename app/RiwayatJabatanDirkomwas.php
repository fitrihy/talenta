<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiwayatJabatanDirkomwas extends Model
{
    protected $table = 'riwayat_jabatan_dirkomwas';

    protected $fillable = [
        'id_talenta',
        'jabatan',
        'tupoksi',
        'nama_perusahaan',
        'tanggal_awal',
        'tanggal_akhir',
        'masih_bekerja',
        'achievement',
    ];

    
    public function talenta()
    {
        return $this->belongsTo('App\Talenta', 'id_talenta');
    }
}
