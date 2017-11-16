<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tugasjabatan extends Model
{
    protected $table="tugas_jabatan";

    public function target(){
        return $this->hasMany('App\Targetjabatan','tugas_jabatan_id');
    }
}
