<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nilaiharian extends Model
{
    protected $table="nilai_kegiatan_harian";

    public function pegawai(){
        return $this->belongsTo('App\Pegawai');
    }
}
