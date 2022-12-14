<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $table = 'kota';

    protected $guarded = [];

    public function provinsi()
    {
    	return $this->belongsTo('App\Provinsi', 'provinsi_id');
    }
}
