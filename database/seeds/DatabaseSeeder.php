<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \DB::Table('instansi')
            ->insert(
                [
                    'nama_instansi'=>'Pengadilan Agama Tegal',
                    'kelas'=>'I A',
                    'alamat'=>'Tegal',
                    'kode_pos'=>'43456',
                    'telp'=>'(0283) 234523',
                    'fax'=>'',
                    'website'=>'',
                    'email'=>''
                ]
            );
    }
}
