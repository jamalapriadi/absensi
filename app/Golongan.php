<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    //

    public static $rules=[
        'nama'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama Golongan harus diisi'
    ];
}
