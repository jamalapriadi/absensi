<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table="pegawai";

    public static $rules=[
    	'nama'=>'required'
    ];

    public static $pesan=[
    	'nama.required'=>'Nama Pegawai harus diisi'
    ];

    public function status(){
        return $this->hasOne('\App\Statuspegawai');
    }
}
