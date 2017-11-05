<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statuspegawai extends Model
{
    protected $table="jabatan_pegawai";

    public function pangkat(){
        return $this->belongsTo('\App\Pangkat');
    }

    public function kepegawaian(){
        return $this->belongsTo('\App\Status','status_id');
    }
}
