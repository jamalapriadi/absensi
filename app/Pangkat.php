<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    //

    public static $rules=[
        'nama'=>'required',
        'golongan'=>'required',
        'ruang'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama Pangkat harus diisi',
        'golongan.required'=>'Golongan harus diisi',
        'ruang.required'=>'Ruang Golongan harus diisi'
    ];

    public function golongan(){
        return $this->belongsTo('App\Golongan','golongan_id');
    }
}
