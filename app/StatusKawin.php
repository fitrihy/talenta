<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusKawin extends Model
{
    protected $table = 'status_kawin';

    protected $fillable = [
        'nama', 'keluarga_flag'
    ];
}
