<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sasarankerja extends Model
{
    protected $table="sasaran_kerja";

    public static $rules=[
        'nama'=>'required',
        'start'=>'required',
        'end'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama sasaran kerja harus diisi',
        'start.required'=>'Start Periode harus diisi',
        'end.required'=>'End Periode harus diisi'
    ];
}
