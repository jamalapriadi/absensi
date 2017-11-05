<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table="status";

    public static $rules=[
        'nama'=>'required'
    ];

    public static $pesan=[
        'nama.required'=>'Nama harus diisi'
    ];
}
