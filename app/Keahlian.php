<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keahlian extends Model
{
    protected $table = 'keahlian';

    protected $fillable = [
        'jenis_keahlian', 'deskripsi'
    ];
}
