<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table="instansi";

    public static $rules=[
        'nama'=>'required',
        'kelas'=>'required',
        'alamat'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama Instansi harus diisi',
        'kelas.required'=>'Kelas Instansi harus diisi',
        'alamat.required'=>'Alamat harus diisi'
    ];
}
