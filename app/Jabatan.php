<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table="jabatan";

    public static $rules=[
        'nama'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama harus diisi'
    ];

    public function tugas(){
        return $this->hasMany('\App\Tugasjabatan');
    }
}
