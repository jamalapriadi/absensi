<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nilaiskp extends Model
{
    protected $table="nilai_skp";

    public function pegawai(){
        return $this->belongsTo('\App\Pegawai')
            ->select(['id','nip','nama_lengkap','foto'])
            ->where('active','Y'); 
    }

    public function penilai(){
        return $this->belongsTo('\App\Pegawai','pejabat_penilai')
            ->select(['id','nip','nama_lengkap','foto'])
            ->where('active','Y'); 
    }

    public function atasan(){
        return $this->belongsTo('\App\Pegawai','atasan_pejabat_penilai')
            ->select(['id','nip','nama_lengkap','foto'])
            ->where('active','Y'); 
    }

    public function tambahan(){
        return $this->hasMany('\App\Tugastambahan','nilai_skp_id');
    }

    public function prestasi(){
        return $this->belongsToMany('\App\Perilakukerja','nilai_prestasi_kerja','nilai_skp_id','perilaku_kerja_id')
            ->withPivot(['id','nilai','uraian']);
    }
}
