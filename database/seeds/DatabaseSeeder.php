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
        // \App\User::where('level','pegawai')
        // ->update(
        //     [
        //         'password'=>bcrypt('welcome')
        //     ]
        //     );
        
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
        
        \DB::Table('sasaran_kerja')
            ->insert(
                [
                    'nama_sasaran'=>'Sasaran Kerja Tahun 2018',
                    'start_periode'=>'2018-01-01',
                    'end_periode'=>'2018-12-31',
                    'created_at'=>Date('Y-m-d H:i:s'),
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]
                );
        
        \DB::Table('jabatan')
                ->insert(
                    [
                        [
                            'nama_jabatan'=>'Ketua',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Wakil Ketua',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Hakim',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Panitera',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Panitera Mudan Permohonan',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Panitera Muda Hukum',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Sekretaris',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Panitera Pengganti',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Kepala Sub Bagian Kepegawaian, Organisasi dan Tata Laksana',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Kepala Sub Bagian Umum dan Keuangan',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Juru Sita Pengganti',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ],
                        [
                            'nama_jabatan'=>'Kepala Sub Bagian Perencanaan, Teknologi Informasi dan Pelaporan',
                            'created_at'=>Date('Y-m-d H:i:s'),
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]
                    ]
            );
        
        \DB::table('golongans')
            ->insert(
                [
                    [
                        'id'=>1,
                        'nama_golongan'=>'IV'
                    ],
                    [
                        'id'=>2,
                        'nama_golongan'=>'III'
                    ],
                    [
                        'id'=>3,
                        'nama_golongan'=>'II'
                    ]
                ]
            );
        
        \DB::table('pangkats')
            ->insert(
                [
                    [
                        'nama_pangkat'=>'Pembina',
                        'golongan_id'=>1,
                        'ruang'=>'a'
                    ],
                    [
                        'nama_pangkat'=>'Penata Tingkat 1',
                        'golongan_id'=>2,
                        'ruang'=>'d'
                    ],
                    [
                        'nama_pangkat'=>'Penata',
                        'golongan_id'=>2,
                        'ruang'=>'c'
                    ],
                    [
                        'nama_pangkat'=>'Pengatur Muda',
                        'golongan_id'=>3,
                        'ruang'=>'a'
                    ]
                ]
            );
        
        \DB::Table('status')
            ->insert(
                [
                    'nama_status'=>'Pegawai Negeri Sipil'
                ]
            );
        
        \DB::table('users')
            ->insert(
                [
                    'name'=>'Jamal Apriadi',
                    'email'=>'jamal.apriadi@gmail.com',
                    'password'=>bcrypt('welcome')
                ]
            );
        
        \DB::table('perilaku_kerja')
            ->insert(
                [
                    [
                        'nama_perilaku'=>'Orientasi Pelayanan',
                        'deskripsi'=>'-'
                    ],
                    [
                        'nama_perilaku'=>'Integritas',
                        'deskripsi'=>'-'
                    ],
                    [
                        'nama_perilaku'=>'Komitmen',
                        'deskripsi'=>'-'
                    ],
                    [
                        'nama_perilaku'=>'Disiplin',
                        'deskripsi'=>'-'
                    ],
                    [
                        'nama_perilaku'=>'Kerjasama',
                        'deskripsi'=>'-'
                    ],
                    [
                        'nama_perilaku'=>'Kepemimpinan',
                        'deskripsi'=>'-'
                    ]
                ]
            );
    }
}
