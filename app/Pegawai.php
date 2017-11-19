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
    
    public function atasan(){
        return $this->belongsTO('\App\Pegawai','atasan_langsung');
    }

    public function user(){
        return $this->belongsToMany('\App\User','user_pegawai','pegawai_id','user_id');
    }

    public function pangkat(){
        return $this->belongsToMany('\App\Pangkat')
            ->withPivot(['id','pegawai_id','pangkat_id','tmt','active']);
    }

    public function jabatan(){
        return $this->belongsToMany('\App\Jabatan')
            ->withPivot(['id','pegawai_id','jabatan_id','tmt','active']);
    }

    public function tugas(){
        return $this->hasMany('App\Tugasjabatan','pegawai_id');
    }

    public function nilai(){
        return $this->hasOne('\App\Nilaiskp');
    }
}
