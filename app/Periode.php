<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
	protected $table = 'periode_jabatan';

	protected $fillable = [
        'nama','keterangan', 'warna'
    ];

}
