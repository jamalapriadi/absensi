<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perilakukerja extends Model
{
    protected $table="perilaku_kerja";

    public static $rules=[
        'nama'=>'required',
        'desc'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama Perilaku Kerja harus diisi',
        'desc.required'=>'Desckripsi perilaku kerja harus diisi'
    ];
}
