<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Nilaiskp;
use Yajra\DataTables\DataTables;

class NilaiController extends Controller
{
    public function nilai_skp(Request $request){
        return view('dashboard.nilai.skp')
            ->with('home','Dashboard')
            ->with('title','Form SKP');
    }

    public function nilai_skp_store(Request $request){
        $rules=[
            'tanggal'=>'required',
            'pegawai'=>'required',
            'pejabat'=>'required',
            'atasan'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal Harus diisi',
            'pegawai.required'=>'Pegawai Harus diisi',
            'pejabat.required'=>'Pejabat Penilai harus diisi',
            'atasan.required'=>'Atasan Pejabat harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            //cek
            $sasaran = $request->session()->get('sasarankerja');
            $cek=Nilaiskp::where('sasaran_kerja_id',$sasaran)
                ->where('pegawai_id',$request->input('pegawai'))
                ->get();
            
            if(count($cek)>0){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data Pegawai ini sudah ada',
                    'error'=>''
                );
            }else{
                //simpan baru
                $nilai=new Nilaiskp;
                $nilai->sasaran_kerja_id=$sasaran;
                $nilai->pegawai_id=$request->input('pegawai');
                $nilai->tgl_penilaian=date('Y-m-d',strtotime($request->input('tanggal')));
                $nilai->pejabat_penilai=$request->input('pejabat');
                $nilai->atasan_pejabat_penilai=$request->input('atasan');
                $nilai->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data berhasil disimpan',
                    'error'=>''
                );
            }
        }

        return $data;
    }

    public function nilai_skp_data(Request $request,DataTables $dataTables){
        $sasaran = $request->session()->get('sasarankerja');

        \DB::statement(\DB::raw('set @rownum=0'));

        $nilai=Nilaiskp::where('sasaran_kerja_id',$sasaran)
            ->with(
                [
                    'pegawai',
                    'penilai',
                    'atasan'
                ]
            )
            ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),
                'id',
                'tgl_penilaian',
                'pegawai_id',
                'pejabat_penilai',
                'atasan_pejabat_penilai'
            );

        return $dataTables->eloquent($nilai)   
            ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='".\URL::to('home/nilai-skp/'.$row->id.'/report')."' class='btn btn-info btn-sm' title='Edit' kode='".$row->id."'>
                            <i class='icon-stats-bars2'></i>
                            </a>";

                        $html.="<a href='#' class='btn btn-warning btn-sm editnilai' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapusnilai' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
            ->make(true);
    }

    public function nilai_skp_detail($id){
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'penilai',
                    'atasan'
                ]
            )->find($id);

        return $nilai;
    }

    public function nilai_skp_update(Request $request,$id){
        $rules=[
            'tanggal'=>'required',
            'pegawai'=>'required',
            'pejabat'=>'required',
            'atasan'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal Harus diisi',
            'pegawai.required'=>'Pegawai Harus diisi',
            'pejabat.required'=>'Pejabat Penilai harus diisi',
            'atasan.required'=>'Atasan Pejabat harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $nilai=Nilaiskp::find($id);
            $nilai->pegawai_id=$request->input('pegawai');
            $nilai->tgl_penilaian=date('Y-m-d',strtotime($request->input('tanggal')));
            $nilai->pejabat_penilai=$request->input('pejabat');
            $nilai->atasan_pejabat_penilai=$request->input('atasan');

            if($request->has('nilaiskp')){
                $nilai->nilai_pencapaian=$request->input('nilaiskp');
            }

            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }

    public function update_skp_nilai(Request $request,$id){
        $rules=[
            'nilaiskp'=>'required',
        ];

        $pesan=[
            'nilaiskp.required'=>'Nilai SKP Harus diisi',
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $nilai=Nilaiskp::find($id);

            if($request->has('nilaiskp')){
                $nilai->nilai_pencapaian=$request->input('nilaiskp');
            }

            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }

    public function nilai_skp_delete($id){
        $nilai=Nilaiskp::find($id);

        if($nilai->delete()){
            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil dihapus',
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>"Data Gagal dihapus",
                'error'=>''
            );
        }

        return $data;
    }

    public function nilai_skp_report(Request $request,$id){
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'pegawai.pangkat',
                    'pegawai.jabatan',
                    'penilai',
                    'penilai.pangkat',
                    'penilai.jabatan',
                    'atasan'
                ]
            )->find($id);
        
        $instansi=\App\Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();

        return view('dashboard.nilai.report')
                    ->with('home','Dashboard')
                    ->with('title','Report SKP')
                    ->with('nilai',$nilai)
                    ->with('instansi',$instansi);
    }

    public function form_skp(Request $request,$id){
        $sasaran = $request->session()->get('sasarankerja');

        $pegawai=\App\Pegawai::with(
            [
                'tugas'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'tugas.target'=>function($q){
                    $q->where('type','target');
                }
            ]
        )
        ->find($id);

        return $pegawai;
    }

    public function form_skp_realisasi(Request $request,$id){
        $sasaran = $request->session()->get('sasarankerja');

        $pegawai=\App\Pegawai::with(
            [
                'tugas'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'tugas.target'=>function($q){
                    $q->where('type','realisasi');
                },
                'tugas.realisasi'=>function($q){
                    $q->where('type','target');
                },
                'nilai'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'nilai.tambahan'=>function($q){
                    $q->orderBy('type');
                },
                'nilai.prestasi'
            ]
        )
        ->find($id);

        return $pegawai;
    }

    public function tugas_tambahan_store(Request $request){
        $rules=[
            'type'=>'required',
            'nama'=>'required',
            'nilai'=>'required'
        ];

        $pesan=[
            'type.required'=>'Type harus diisi',
            'nama.required'=>'Nama Harus diisi',
            'nilai.required'=>'Nilai harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            if($request->has('kode')){
                $tambahan=\App\Tugastambahan::find($request->input('kode'));
            }else{
                $tambahan=new \App\Tugastambahan;
                $tambahan->nilai_skp_id=$request->input('skp');
            }
            $tambahan->type=$request->input('type');
            $tambahan->nama=$request->input('nama');
            $tambahan->nilai=$request->input('nilai');
            $tambahan->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    public function tugas_tambahan_delete($id){
        $tambahan=\App\Tugastambahan::find($id);

        $hapus=$tambahan->delete();

        if($hapus){
            $data=array(
                'success'=>true,
                'pesan'=>"Data Berhasil dihapus",
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>"Data Gagal dihapus",
                'error'=>''
            );
        }

        return $data;
    }

    public function perilaku_kerja_by_id_skp($id){
        $nilai=\App\Nilaiskp::with('prestasi')->find($id);

        return $nilai;
    }

    public function list_perilaku_by_skp($id,Request $request){
        $type=$request->input('type');

        switch ($type) {
            case 'Add':
                    $nilai = \App\Nilaiprestasi::where('nilai_skp_id',$id)
                        ->pluck('perilaku_kerja_id')
                        ->all();
                    
                    $perilaku = \App\Perilakukerja::whereNotIn('id',$nilai)
                        ->select('id','nama_perilaku as text')
                        ->get();

                    return $perilaku;
                break;
            case 'edit':

                break;
            
            default:
                # code...
                break;
        }
        
    }

    public function list_perilaku_by_skp_store(Request $request){
        $rules=[
            'perilaku'=>'required',
            'nilai'=>'required',
            'skp'=>'required'
        ];

        $pesan=[
            'perilaku.required'=>'Perilaku Harus diisi',
            'nilai.required'=>'Nilai harus diisi',
            'skp.required'=>'SKP Harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            if ($request->has('kode')) {
                $nilai=\App\Nilaiprestasi::find($request->input('kode'));
            }else{
                $nilai=new \App\Nilaiprestasi;
            }
            
            $nilai->nilai_skp_id=$request->input('skp');
            $nilai->perilaku_kerja_id=$request->input('perilaku');
            $nilai->nilai=$request->input('nilai');
            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    public function list_perilaku_by_skp_delete($id){
        $nilai=\App\Nilaiprestasi::find($id);

        if($nilai->delete()){
            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil dihapus',
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>"Data Gagal dihapus",
                'error'=>''
            );
        }

        return $data;
    }

    public function preview_skp($id,Request $request){
        $sasaran = $request->session()->get('sasarankerja');
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'pegawai.tugas'=>function($q) use($sasaran){
                        $q->where('sasaran_kerja_id',$sasaran);
                    },
                    'pegawai.tugas.target'=>function($q){
                        $q->where('type','target');
                    },
                    'pegawai.pangkat',
                    'pegawai.jabatan',
                    'penilai',
                    'penilai.pangkat',
                    'penilai.jabatan',
                    'atasan',
                    'tambahan'
                ]
            )->find($id);
        
        $instansi=\App\Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();

        return view('dashboard.nilai.preview_skp')
                    ->with('home','Dashboard')
                    ->with('title','Report SKP')
                    ->with('nilai',$nilai)
                    ->with('instansi',$instansi);
    }

    public function export_xls($id,Request $request){
        $idsasaran = $request->session()->get('sasarankerja');
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'pegawai.tugas'=>function($q) use($idsasaran){
                        $q->where('sasaran_kerja_id',$idsasaran);
                    },
                    'pegawai.tugas.target'=>function($q){
                        $q->where('type','target');
                    },
                    'pegawai.tugas.realisasi'=>function($q){
                        $q->where('type','realisasi');
                    },
                    'pegawai.pangkat',
                    'pegawai.jabatan',
                    'penilai',
                    'penilai.pangkat',
                    'penilai.jabatan',
                    'atasan',
                    'tambahan',
                    'prestasi'
                ]
            )->find($id);
        
        $instansi=\App\Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();
            
        $sasaran=\App\Sasarankerja::find($idsasaran);

        return \Excel::create('penilaian-skp',function($excel) use($sasaran,$nilai,$instansi){
            $excel->sheet('cover',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A8:J8');
                $sheet->mergeCells('A9:J9');
                $sheet->mergeCells('A15:J15');
                $sheet->mergeCells('A16:J16');
                $sheet->mergeCells('A39:J39');
                $sheet->mergeCells('A40:J40');

                $sheet->setSize(array(
                    'A8' => array(
                        'width'     => 10,
                        'height'    => 25
                    ),
                    'A9' => array(
                        'width'     => 10,
                        'height'    => 25
                    ),
                    'A15' => array(
                        'width'     => 10,
                        'height'    => 25
                    ),
                    'A16' => array(
                        'width'     => 10,
                        'height'    => 25
                    ),
                    'A17' => array(
                        'width'     => 10,
                        'height'    => 40
                    ),
                    'E18' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'E19' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'E20' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'E21' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'E22' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                ));

                $objDrawing = new \PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo.png')); //your image path
                $objDrawing->setCoordinates('E2');
                $objDrawing->setWorksheet($sheet);
                
                $sheet->cell('A8', function($cell) {
                    $cell->setValue('PENILAIAN PRESTASI KERJA')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A9', function($cell) {
                    $cell->setValue('PEGAWAI NEGERI SIPIL')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A15', function($cell) {
                    $cell->setValue('Jangka Waktu Penilaian')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A16', function($cell) use($sasaran){
                    $cell->setValue(date('d F Y',strtotime($sasaran->start_periode))." - ".date('d F Y',strtotime($sasaran->end_periode)))
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setValignment('center')
                    ->setAlignment('center');
                });

                $sheet->cell('B18', function($cell) {
                    $cell->setValue('Nama Pegawai')
                    ->setFontSize(12);
                });

                $sheet->cell('E18', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('F18', function($cell) use($nilai) {
                    $cell->setValue($nilai->pegawai->nama_lengkap)
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });

                $sheet->cell('B19', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });

                $sheet->cell('E19', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('F19', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nip)
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });

                $sheet->cell('B20', function($cell) {
                    $cell->setValue('Pangkat Golongan Ruang')
                    ->setFontSize(12);
                });

                $sheet->cell('E20', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('F20', function($cell) use($nilai){
                    if(count($nilai->pegawai->pangkat)>0){
                        $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat)
                            ->setFont(array(
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                    }else{
                        $cell->setValue('Pangkat Belum Ada')
                            ->setFontSize(12);
                    }
                });

                $sheet->cell('B21', function($cell) {
                    $cell->setValue('Jabatan')
                    ->setFontSize(12);
                });

                $sheet->cell('E21', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('F21', function($cell) use($nilai){
                    if(count($nilai->pegawai->jabatan)>0){
                        $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan)
                            ->setFont(array(
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                    }else{
                        $cell->setValue('Jabatan Belum Ada')
                        ->setFontSize(12);
                    }
                });

                $sheet->cell('B22', function($cell) {
                    $cell->setValue('Unit Kerja')
                    ->setFontSize(12);
                });

                $sheet->cell('E22', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('F22', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });

                $sheet->cell('A39', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A40', function($cell){
                    $cell->setValue('TAHUN '.date('Y'))
                    ->setFont(array(
                        'size'       => '14',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

            });

            $excel->sheet('data_skp',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B12:E12');
                $sheet->mergeCells('B18:E18');
                $sheet->mergeCells('A7:A11');
                $sheet->mergeCells('A13:A17');
                $sheet->mergeCells('A19:A23');

                /* set border */
                $sheet->setBorder('A6', 'thin');
                $sheet->setBorder('A7', 'thin');
                $sheet->setBorder('A12', 'thin');
                $sheet->setBorder('A13', 'thin');
                $sheet->setBorder('A18', 'thin');
                $sheet->setBorder('A19:A23', 'thin');

                $sheet->setBorder('B6', 'thin');
                $sheet->setBorder('B7:E7', 'thin');
                $sheet->setBorder('B8:E8', 'thin');
                $sheet->setBorder('B9:E9', 'thin');
                $sheet->setBorder('B10:E10', 'thin');
                $sheet->setBorder('B11:E11', 'thin');

                $sheet->setBorder('B12', 'thin');
                $sheet->setBorder('B13:E13', 'thin');
                $sheet->setBorder('B14:E14', 'thin');
                $sheet->setBorder('B15:E15', 'thin');
                $sheet->setBorder('B16:E16', 'thin');
                $sheet->setBorder('B17:E17', 'thin');

                $sheet->setBorder('B18', 'thin');
                $sheet->setBorder('B19:E19', 'thin');
                $sheet->setBorder('B20:E20', 'thin');
                $sheet->setBorder('B21:E21', 'thin');
                $sheet->setBorder('B22:E22', 'thin');
                $sheet->setBorder('B23:E23', 'thin');
                /* end set border */

                /* set size */
                $sheet->setHeight(array(
                    1     =>  40,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                    5     =>  20,
                    6     =>  20,
                    7     =>  20,
                    8     =>  20,
                    9     =>  20,
                    10     =>  20,
                    11     =>  20,
                    12     =>  20,
                    13     =>  20,
                    14     =>  20,
                    15     =>  20,
                    16     =>  20,
                    17     =>  20,
                    18     =>  20,
                    19     =>  20,
                    20     =>  20,
                    21     =>  20,
                    22     =>  20,
                    23     =>  20,
                ));

                $sheet->setWidth('A', 3);
                $sheet->setWidth('B', 3);
                $sheet->setWidth('C', 20);
                $sheet->setWidth('D', 3);
                $sheet->setWidth('E', 35);
                /* end set size */
                
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('DATA SASARAN KERJA PEGAWAI')
                    ->setFontSize(14)
                    ->setAlignment('center')
                    ->setValignment('center')
                    ->setFontColor('#ffffff')
                    ->setBackground('#000000');
                });

                $sheet->cell('A3', function($cell) {
                    $cell->setValue('UNIT KERJA')
                    ->setFontSize(12);
                });

                $sheet->cell('D3', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('E3', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFontSize(12);
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue('JANGKA WAKTU PENILAIAN')
                    ->setFontSize(12);
                });

                $sheet->cell('D4', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('E4', function($cell) use($sasaran){
                    $cell->setValue(date('d F Y',strtotime($sasaran->start_periode))." - ".date('d F Y',strtotime($sasaran->end_periode)))
                    ->setFontSize(12);
                });

                $sheet->cell('A6', function($cell) {
                    $cell->setValue('1.')
                    ->setBackground('#eded1a');
                });

                $sheet->cell('B6', function($cell) {
                    $cell->setValue('YANG DINILAI')
                    ->setBackground('#eded1a');
                });

                $sheet->cell('B7', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->cell('C7', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->cell('D7', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E7', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nama_lengkap);
                });

                $sheet->cell('B8', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->cell('C8', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->cell('D8', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E8', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nip);
                });

                $sheet->cell('B9', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->cell('C9', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->cell('D9', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E9', function($cell) use($nilai){
                    if(count($nilai->pegawai->pangkat)>0){
                        $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat);
                    }else{
                        $cell->setValue('Pangkat belum ada');
                    }
                });

                $sheet->cell('B10', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C10', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D10', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E10', function($cell) use($nilai){
                    if(count($nilai->pegawai->jabatan)>0){
                        $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan);
                    }else{
                        $cell->setValue('Jabatan belum ada');
                    }
                });

                $sheet->cell('B11', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C11', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D11', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E11', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });

                /* yang menilai */
                $sheet->cell('A12', function($cell) {
                    $cell->setValue('2.')
                    ->setBackground('#13f222');
                });

                $sheet->cell('B12', function($cell) {
                    $cell->setValue('PEJABAT PENILAI')
                    ->setBackground('#13f222');
                });

                $sheet->cell('B13', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->cell('C13', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->cell('D13', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E13', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nama_lengkap);
                    }else{
                        $cell->setValue('Penilai tidak ada');
                    }
                });

                $sheet->cell('B14', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->cell('C14', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->cell('D14', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E14', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nip);
                    }else{
                        $cell->setValue('Penilai tidak ada');
                    }
                });

                $sheet->cell('B15', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->cell('C15', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->cell('D15', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E15', function($cell) use($nilai){
                    if(count($nilai->penilai->pangkat)>0){
                        $cell->setValue($nilai->penilai->pangkat[0]->nama_pangkat);
                    }else{
                        $cell->setValue('Penilai belum ada');
                    }
                });

                $sheet->cell('B16', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C16', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D16', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E16', function($cell) use($nilai){
                    if(count($nilai->penilai->jabatan)>0){
                        $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan);
                    }else{
                        $cell->setValue('Jabatan belum ada');
                    }
                });

                $sheet->cell('B17', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C17', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D17', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E17', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end yang menilai */

                /* atasan pejabat penilai */
                $sheet->cell('A18', function($cell) {
                    $cell->setValue('3.')
                    ->setBackground('#114ef7');
                });

                $sheet->cell('B18', function($cell) {
                    $cell->setValue('ATASAN PEJABAT PENILAI')
                    ->setBackground('#114ef7');
                });

                $sheet->cell('B19', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->cell('C19', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->cell('D19', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E19', function($cell) use($nilai){
                    if(count($nilai->atasan)>0){
                        $cell->setValue($nilai->atasan->nama_lengkap);
                    }else{
                        $cell->setValue('Atasan tidak ada');
                    }
                });

                $sheet->cell('B20', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->cell('C20', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->cell('D20', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E20', function($cell) use($nilai){
                    if(count($nilai->atasan)>0){
                        $cell->setValue($nilai->atasan->nip);
                    }else{
                        $cell->setValue('Atasan tidak ada');
                    }
                });

                $sheet->cell('B21', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->cell('C21', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->cell('D21', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E21', function($cell) use($nilai){
                    if(count($nilai->atasan->pangkat)>0){
                        $cell->setValue($nilai->atasan->pangkat[0]->nama_pangkat);
                    }else{
                        $cell->setValue('Pangkat atasan tidak ada');
                    }
                });

                $sheet->cell('B22', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C22', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D22', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E22', function($cell) use($nilai){
                    if(count($nilai->atasan->jabatan)>0){
                        $cell->setValue($nilai->atasan->jabatan[0]->nama_jabatan);
                    }else{
                        $cell->setValue('Jabatan atasan tidak ada');
                    }
                });

                $sheet->cell('B23', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C23', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D23', function($cell) {
                    $cell->setValue(':')
                        ->setAlignment('center');
                });

                $sheet->cell('E23', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end atasan pejabat penilai */

            });

            $excel->sheet('form_skp',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('B3:D3');
                $sheet->mergeCells('F3:K3');
                $sheet->mergeCells('A9:A10');
                $sheet->mergeCells('B9:B10');
                $sheet->mergeCells('C9:C10');
                $sheet->mergeCells('D9:D10');

                $sheet->mergeCells('B9:D9');
                $sheet->mergeCells('B10:D10');
                $sheet->mergeCells('E9:E10');
                $sheet->mergeCells('F9:K9');
                $sheet->mergeCells('F10:G10');
                $sheet->mergeCells('I10:J10');

                $sheet->mergeCells('C4:D4');
                $sheet->mergeCells('C5:D5');
                $sheet->mergeCells('C6:D6');
                $sheet->mergeCells('C7:D7');
                $sheet->mergeCells('C8:D8');
                $sheet->mergeCells('G4:K4');
                $sheet->mergeCells('G5:K5');
                $sheet->mergeCells('G6:K6');
                $sheet->mergeCells('G7:K7');
                $sheet->mergeCells('G8:K8');

                $sheet->setBorder('A3', 'thin');
                $sheet->setBorder('B3:D3', 'thin');
                $sheet->setBorder('E3', 'thin');
                $sheet->setBorder('F3:K3', 'thin');

                $sheet->setBorder('A4', 'thin');
                $sheet->setBorder('B4', 'thin');
                $sheet->setBorder('C4', 'thin');

                $sheet->setBorder('E4', 'thin');
                $sheet->setBorder('F4', 'thin');
                $sheet->setBorder('G4', 'thin');
                

                $sheet->setBorder('E5', 'thin');
                $sheet->setBorder('F5', 'thin');
                $sheet->setBorder('G5', 'thin');

                $sheet->setBorder('E6', 'thin');
                $sheet->setBorder('F6', 'thin');
                $sheet->setBorder('G6', 'thin');

                $sheet->setBorder('E7', 'thin');
                $sheet->setBorder('F7', 'thin');
                $sheet->setBorder('G7', 'thin');

                $sheet->setBorder('E8', 'thin');
                $sheet->setBorder('F8', 'thin');
                $sheet->setBorder('G8', 'thin');

                $sheet->setBorder('A5', 'thin');
                $sheet->setBorder('B5', 'thin');
                $sheet->setBorder('C5', 'thin');

                $sheet->setBorder('A6', 'thin');
                $sheet->setBorder('B6', 'thin');
                $sheet->setBorder('C6', 'thin');

                $sheet->setBorder('A7', 'thin');
                $sheet->setBorder('B7', 'thin');
                $sheet->setBorder('C7', 'thin');

                $sheet->setBorder('A8', 'thin');
                $sheet->setBorder('B8', 'thin');
                $sheet->setBorder('C8', 'thin');

                $sheet->setBorder('A9', 'thin');
                $sheet->setBorder('B9', 'thin');
                $sheet->setBorder('C9', 'thin');
                $sheet->setBorder('D9', 'thin');
                $sheet->setBorder('E9', 'thin');
                $sheet->setBorder('F9', 'thin');
                $sheet->setBorder('F10', 'thin');
                $sheet->setBorder('H10', 'thin'); 
                $sheet->setBorder('I10', 'thin');
                $sheet->setBorder('J10', 'thin');                
                $sheet->setBorder('K10', 'thin');                
                


                /* set size */
                $sheet->setHeight(array(
                    1     =>  30,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                    5     =>  20,
                    6     =>  20,
                    7     =>  20,
                    8     =>  20,
                    9     =>  20,
                    10     =>  20,
                    11     =>  20,
                    12     =>  20,
                    13     =>  20,
                    14     =>  20,
                    15     =>  20,
                    16     =>  20,
                    17     =>  20,
                    18     =>  20,
                    19     =>  20,
                    20     =>  20,
                    21     =>  20,
                    22     =>  20,
                    23     =>  20,
                    24     =>  20,
                    25     =>  20,
                ));

                $sheet->setWidth('A', 5);
                $sheet->setWidth('B', 35);
                $sheet->setWidth('C', 47);
                $sheet->setWidth('D', 5);
                $sheet->setWidth('E', 5);
                $sheet->setWidth('F', 25);
                $sheet->setWidth('G', 30);
                $sheet->setWidth('H', 15);

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('SASARAN KERJA PEGAWAI')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A3', function($cell) {
                    $cell->setValue('No.')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                $sheet->cell('A4', function($cell) {
                    $cell->setValue('1.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('A5', function($cell) {
                    $cell->setValue('2.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('A6', function($cell) {
                    $cell->setValue('3.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('A7', function($cell) {
                    $cell->setValue('4.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('A8', function($cell) {
                    $cell->setValue('5.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('B3', function($cell) {
                    $cell->setValue('I. PEJABAT PENILAI')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });

                $sheet->cell('B4', function($cell) {
                    $cell->setValue('Nama')
                    ->setFontSize(12);
                });

                $sheet->cell('C4', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nama_lengkap)
                            ->setFontSize(12);
                    }else{
                        $cell->setValue('Penilai tidak ada')
                            ->setFontSize(12);
                    }
                });
                $sheet->cell('B5', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });
                $sheet->cell('C5', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nip)
                            ->setFontSize(12);
                    }else{
                        $cell->setValue('Penilai tidak ada')
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('B6', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang')
                    ->setFontSize(12);
                });
                $sheet->cell('C6', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->pangkat[0]->nama_pangkat)
                            ->setFontSize(12);
                    }else{
                        $cell->setValue('Pangkat penilai tidak ada')
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('B7', function($cell) {
                    $cell->setValue('Jabatan')
                    ->setFontSize(12);
                });
                $sheet->cell('C7', function($cell) use($nilai){
                    if(count($nilai->penilai->jabatan)>0){
                        $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan)
                            ->setFontSize(12);
                    }else{
                        $cell->setValue('Jabatan penilai tidak ada')
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('B8', function($cell) {
                    $cell->setValue('Unit Kerja')
                    ->setFontSize(12);
                });
                $sheet->cell('C8', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFontSize(12);
                });

                $sheet->cell('E3', function($cell) {
                    $cell->setValue('No.')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('F3', function($cell) {
                    $cell->setValue('II. PEGAWAI NEGERI SIPIL YANG DINILAI')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });

                $sheet->cell('E4', function($cell) {
                    $cell->setValue('1.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('E5', function($cell) {
                    $cell->setValue('2.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('E6', function($cell) {
                    $cell->setValue('3.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('E7', function($cell) {
                    $cell->setValue('4.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('E8', function($cell) {
                    $cell->setValue('5.')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('F4', function($cell) {
                    $cell->setValue('Nama')
                    ->setFontSize(12);
                });
                $sheet->cell('G4', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->nama_lengkap)
                            ->setFontSize(12);
                    }
                });
                $sheet->cell('F5', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });
                $sheet->cell('G5', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->nip)
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('F6', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang')
                    ->setFontSize(12);
                });
                $sheet->cell('G6', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat)
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('F7', function($cell) {
                    $cell->setValue('Jabatan')
                    ->setFontSize(12);
                });
                $sheet->cell('G7', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan)
                        ->setFontSize(12);
                    }
                });
                $sheet->cell('F8', function($cell) {
                    $cell->setValue('Unit Kerja')
                    ->setFontSize(12);
                });
                $sheet->cell('G8', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFontSize(12);
                });

                /*================ kegiatan =========*/
                $sheet->cell('A9', function($cell){
                    $cell->setValue('No.')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });
                $sheet->cell('B9', function($cell){
                    $cell->setValue('III. KEGIATAN TUGAS JABATAN')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                $sheet->cell('E9', function($cell){
                    $cell->setValue('AK')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ));
                });
                $sheet->cell('F9', function($cell){
                    $cell->setValue('TARGET')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('F10', function($cell){
                    $cell->setValue('KUANT/OUTPUT')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                $sheet->cell('H10', function($cell){
                    $cell->setValue('KUAL/MUTU')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                $sheet->cell('I10', function($cell){
                    $cell->setValue('WAKTU')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                $sheet->cell('K10', function($cell){
                    $cell->setValue('BIAYA')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });
                
                $no=11;
                $nos=1;
                $jumtugas=count($nilai->pegawai->tugas);
                if(count($nilai->pegawai->tugas)>0){
                    foreach($nilai->pegawai->tugas as $row){
                        $sheet->mergeCells('B'.$no.':D'.$no);
                        $sheet->setBorder('A'.$no, 'thin');
                        $sheet->setBorder('B'.$no, 'thin');
                        $sheet->setBorder('E'.$no, 'thin');
                        $sheet->setBorder('F'.$no, 'thin');
                        $sheet->setBorder('G'.$no, 'thin');
                        $sheet->setBorder('H'.$no, 'thin');
                        $sheet->setBorder('I'.$no, 'thin');
                        $sheet->setBorder('J'.$no, 'thin');
                        $sheet->setBorder('K'.$no, 'thin');

                        $sheet->cell('A'.$no, function($cell) use($nos){
                            $cell->setValue($nos)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('B'.$no, function($cell) use($row){
                            $cell->setValue($row->nama_tugas)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('E'.$no, function($cell) use($row){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('F'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->kuant)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });

                        $sheet->cell('G'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->output)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });

                        $sheet->cell('H'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->kual)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
                        
                        $sheet->cell('I'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->waktu)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });

                        $sheet->cell('J'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->periode_waktu)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });

                        $sheet->cell('K'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->biaya)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $no++;
                        $nos++;
                    }
                    
                    $sheet->setBorder('A'.$no, 'thin');
                    $sheet->setBorder('E'.$no, 'thin');
                    $sheet->setBorder('F'.$no, 'thin');

                    $sheet->setHeight(array(
                        $no     =>  40,
                    ));

                    $sheet->mergeCells('A'.$no.':D'.$no);
                    $sheet->cell('A'.$no, function($cell){
                        $cell->setValue('Jumlah')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('F'.$no.':K'.$no);
                    $sheet->cell('F'.$no, function($cell){
                        $cell->setValue('')
                        ->setFontSize(12);
                    });
    
                    $nok=$no+1;
                    $sheet->mergeCells('G'.$nok.':K'.$nok);
                    $sheet->cell('G'.$nok, function($cell) use($nilai){
                        $cell->setValue('Tegal, '.date('d F Y',strtotime($nilai->tgl_penilaian)))
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $nol=$nok+1;
                    $sheet->mergeCells('A'.$nol.':C'.$nol);
                    $sheet->cell('A'.$nol, function($cell){
                        $cell->setValue('Pejabat Penilai, ')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('G'.$nol.':K'.$nol);
                    $sheet->cell('G'.$nol, function($cell){
                        $cell->setValue('Pegawai Negeri Sipil Yang Dinilai,')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $nom=$nok+5;
                    $sheet->mergeCells('A'.$nom.':C'.$nom);
                    $sheet->cell('A'.$nom, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('G'.$nom.':K'.$nom);
                    $sheet->cell('G'.$nom, function($cell) use($nilai){
                        $cell->setValue($nilai->pegawai->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $non=$nok+6;
                    $sheet->mergeCells('A'.$non.':C'.$non);
                    $sheet->cell('A'.$non, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('G'.$non.':K'.$non);
                    $sheet->cell('G'.$non, function($cell) use($nilai){
                        $cell->setValue($nilai->pegawai->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
                }
                
            });

            $excel->sheet('pengukuran',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:R1');
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('N6:O6');
                $sheet->mergeCells('Q5:Q6');
                $sheet->mergeCells('R5:R6');
                $sheet->mergeCells('K6:L6');
                $sheet->mergeCells('K5:P5');
                $sheet->mergeCells('J5:J6');
                $sheet->mergeCells('G6:H6');
                $sheet->mergeCells('D6:E6');
                $sheet->mergeCells('D5:I5');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('G7:H7');
                $sheet->mergeCells('D7:E7');
                $sheet->mergeCells('K7:L7');
                $sheet->mergeCells('N7:O7');

                $sheet->setHeight(array(
                    1     =>  30,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                    5     =>  20,
                    6     =>  20,
                    7     =>  20,
                    8     =>  20,
                    9     =>  20,
                    10     =>  20,
                    11     =>  20,
                    12     =>  20,
                    13     =>  20,
                    14     =>  20,
                    15     =>  20,
                    16     =>  20,
                    17     =>  20,
                    18     =>  20,
                    19     =>  20,
                    20     =>  20,
                    21     =>  20,
                    22     =>  20,
                    23     =>  20,
                    24     =>  20,
                    25     =>  20,
                    26     =>  20,
                    27     =>  20,
                    28     =>  20,
                    29     =>  20,
                    30     =>  20,
                    31     =>  20,
                    32     =>  20,
                    33     =>  20,
                    34     =>  20,
                    35     =>  20,
                    36     =>  20,
                    37     =>  20,
                    38     =>  20,
                    39     =>  20,
                    40     =>  20,
                ));

                $sheet->setWidth('A', 5);
                $sheet->setWidth('B', 85);
                $sheet->setWidth('C', 5);
                $sheet->setWidth('D', 10);
                $sheet->setWidth('K', 15);
                $sheet->setWidth('M', 10);
                $sheet->setWidth('N', 10);
                $sheet->setWidth('O', 10);
                $sheet->setWidth('P', 10);
                $sheet->setWidth('Q', 18);
                $sheet->setWidth('R', 20);

                $sheet->setBorder('A5', 'thin');    
                $sheet->setBorder('B5', 'thin');    
                $sheet->setBorder('C5', 'thin');    
                $sheet->setBorder('D5', 'thin');    
                $sheet->setBorder('J5', 'thin');    
                $sheet->setBorder('K5', 'thin');    
                $sheet->setBorder('Q5', 'thin');    
                $sheet->setBorder('R5', 'thin');    

                $sheet->setBorder('D6', 'thin');    
                $sheet->setBorder('F6', 'thin');    
                $sheet->setBorder('G6', 'thin');    
                $sheet->setBorder('H6', 'thin');    
                $sheet->setBorder('I6', 'thin');    

                $sheet->setBorder('K6', 'thin');    
                $sheet->setBorder('M6', 'thin');    
                $sheet->setBorder('N6', 'thin');    
                $sheet->setBorder('O6', 'thin');    
                $sheet->setBorder('P6', 'thin');    

                $sheet->setBorder('A7', 'thin');
                $sheet->setBorder('B7', 'thin');    
                $sheet->setBorder('C7', 'thin');
                $sheet->setBorder('D7', 'thin');
                $sheet->setBorder('E7', 'thin');
                $sheet->setBorder('F7', 'thin');
                $sheet->setBorder('G7', 'thin');
                $sheet->setBorder('H7', 'thin');
                $sheet->setBorder('I7', 'thin');
                $sheet->setBorder('J7', 'thin');
                $sheet->setBorder('K7', 'thin');
                $sheet->setBorder('L7', 'thin');
                $sheet->setBorder('M7', 'thin');
                $sheet->setBorder('N7', 'thin');
                $sheet->setBorder('O7', 'thin');
                $sheet->setBorder('P7', 'thin');
                $sheet->setBorder('Q7', 'thin');
                $sheet->setBorder('R7', 'thin');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('SASARAN KERJA PEGAWAI')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A3', function($cell) use($sasaran){
                    $cell->setValue('Jangka Waktu Penilaian')
                    ->setFontSize(12);
                });

                $sheet->cell('A4', function($cell) use($sasaran){
                    $cell->setValue($sasaran->start_periode." ".$sasaran->end_periode)
                    ->setFontSize(12);
                });

                $sheet->cell('I3', function($cell) use($sasaran){
                    $cell->setValue('Nama')
                    ->setFontSize(12);
                });

                $sheet->cell('I4', function($cell) use($sasaran){
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });

                $sheet->cell('J3', function($cell) use($sasaran){
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('J4', function($cell) use($sasaran){
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('K3', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->nama_lengkap)
                            ->setFontSize(12);
                    }
                });

                $sheet->cell('K4', function($cell) use($nilai){
                    if(count($nilai->pegawai)){
                        $cell->setValue($nilai->pegawai->nip)
                            ->setFontSize(12);
                    }
                });

                /*==============HEADER TABEL ================*/
                $sheet->cell('A5', function($cell) {
                    $cell->setValue('NO.')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('B5', function($cell) {
                    $cell->setValue('I. KEGIATAN TUGAS JABATAN')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('C5', function($cell) {
                    $cell->setValue('AK')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('D5', function($cell) {
                    $cell->setValue('TARGET')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('D6', function($cell) {
                    $cell->setValue('Kuant/ Output')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('F6', function($cell) {
                    $cell->setValue('Kual/Mutu')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('G6', function($cell) {
                    $cell->setValue('Waktu')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('I6', function($cell) {
                    $cell->setValue('Biaya')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('J5', function($cell) {
                    $cell->setValue('AK')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('K5', function($cell) {
                    $cell->setValue('REALISASI')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('K6', function($cell) {
                    $cell->setValue('Kuant/ Output')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('M6', function($cell) {
                    $cell->setValue('Kual/Mutu')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                
                $sheet->cell('N6', function($cell) {
                    $cell->setValue('Waktu')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('P6', function($cell) {
                    $cell->setValue('Biaya')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                
                $sheet->cell('Q5', function($cell) {
                    $cell->setValue('PENGHITUNGAN')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('R5', function($cell) {
                    $cell->setValue('NILAI CAPAIAN SKP')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A7', function($cell) {
                    $cell->setValue('1')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('B7', function($cell) {
                    $cell->setValue('2')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('C7', function($cell) {
                    $cell->setValue('3')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('D7', function($cell) {
                    $cell->setValue('4')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('F7', function($cell) {
                    $cell->setValue('5')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('G7', function($cell) {
                    $cell->setValue('6')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('I7', function($cell) {
                    $cell->setValue('7')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('J7', function($cell) {
                    $cell->setValue('8')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('K7', function($cell) {
                    $cell->setValue('9')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('M7', function($cell) {
                    $cell->setValue('10')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('N7', function($cell) {
                    $cell->setValue('11')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('P7', function($cell) {
                    $cell->setValue('12')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('Q7', function($cell) {
                    $cell->setValue('13')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });

                $sheet->cell('R7', function($cell) {
                    $cell->setValue('14')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center')
                    ->setBackground('#adabab');
                });
                /*==============END HEADER TABEL ============*/

                /*============= KEGIATAN / RELASISASI ======*/
                $no=8;
                $nos=1;
                $jumtugas=count($nilai->pegawai->tugas);
                if(count($nilai->pegawai->tugas)>0){
                    foreach($nilai->pegawai->tugas as $row){
                        $sheet->setBorder('A'.$no, 'thin');
                        $sheet->setBorder('B'.$no, 'thin');    
                        $sheet->setBorder('C'.$no, 'thin');
                        $sheet->setBorder('D'.$no, 'thin');
                        $sheet->setBorder('E'.$no, 'thin');
                        $sheet->setBorder('F'.$no, 'thin');
                        $sheet->setBorder('G'.$no, 'thin');
                        $sheet->setBorder('H'.$no, 'thin');
                        $sheet->setBorder('I'.$no, 'thin');
                        $sheet->setBorder('J'.$no, 'thin');
                        $sheet->setBorder('K'.$no, 'thin');
                        $sheet->setBorder('L'.$no, 'thin');
                        $sheet->setBorder('M'.$no, 'thin');
                        $sheet->setBorder('N'.$no, 'thin');
                        $sheet->setBorder('O'.$no, 'thin');
                        $sheet->setBorder('P'.$no, 'thin');
                        $sheet->setBorder('Q'.$no, 'thin');
                        $sheet->setBorder('R'.$no, 'thin');


                        $sheet->cell('A'.$no, function($cell) use($nos){
                            $cell->setValue($nos)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('B'.$no, function($cell) use($row){
                            $cell->setValue($row->nama_tugas)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('C'.$no, function($cell) use($row){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('D'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->kuant)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('E'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->output)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('F'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->kual)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('G'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->waktu)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('H'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->periode_waktu)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
                        $sheet->cell('I'.$no, function($cell) use($row){
                            $cell->setValue($row->target[0]->biaya)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        });
    
    
                        $sheet->cell('K'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->kuant)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('L'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->output)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('M'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->kual)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('N'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->waktu)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('O'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->periode_waktu)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('P'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->biaya)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('Q'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->perhitungan)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $sheet->cell('R'.$no, function($cell) use($row){
                            if(count($row->realisasi)>0){
                                $cell->setValue($row->realisasi[0]->nilai_pencapaian)
                                ->setFontSize(12)
                                ->setAlignment('center');
                            }
                        });
    
                        $no++;
                        $nos++;
                    }
    
                    $sheet->mergeCells('A'.$no.':B'.$no);
                    $sheet->setBorder('A'.$no, 'thin');
                    $sheet->setBorder('C'.$no, 'thin');
                    $sheet->setBorder('D'.$no, 'thin');
                    $sheet->setBorder('J'.$no, 'thin');
                    $sheet->setBorder('K'.$no, 'thin');
                    $sheet->setBorder('R'.$no, 'thin');

                    $sheet->cell('A'.$no, function($cell){
                        $cell->setValue('Jumlah')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->cell('C'.$no, function($cell){
                        $cell->setValue('0')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('D'.$no.':I'.$no);
                    $sheet->cell('D'.$no, function($cell){
                        $cell->setValue('')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->cell('J'.$no, function($cell){
                        $cell->setValue('0')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->mergeCells('K'.$no.':Q'.$no);
                    $sheet->cell('K'.$no, function($cell){
                        $cell->setValue('')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $sheet->cell('R'.$no, function($cell){
                        $cell->setValue('')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $nok=$no+1;
                    $sheet->setBorder('A'.$nok, 'thin');
                    $sheet->setBorder('B'.$nok, 'thin');
                    $sheet->mergeCells('B'.$nok.':R'.$nok);
                    $sheet->cell('B'.$nok, function($cell){
                        $cell->setValue('II. TUGAS TAMBAHAN DAN KREATIVITAS :')
                        ->setFont(array(
                            'size'       => '12',
                            'bold'       =>  true
                        ));
                    });
    
                    $not=$nok+1;
                    $notam=1;
                    if(count($nilai->tambahan)>0){
                        foreach($nilai->tambahan as $p){
                            $sheet->setBorder('A'.$not, 'thin');
                            $sheet->setBorder('B'.$not, 'thin');
                            $sheet->setBorder('C'.$not, 'thin');
                            $sheet->setBorder('R'.$not, 'thin');

                            $sheet->cell('A'.$not, function($cell) use($notam){
                                $cell->setValue($notam)
                                ->setFontSize(12);
                            });
    
                            $sheet->cell('B'.$not, function($cell) use($p){
                                $cell->setValue($p->nama)
                                ->setFontSize(12);
                            });                        
    
                            $sheet->mergeCells('C'.$not.':Q'.$not);
    
                            $sheet->cell('R'.$not, function($cell) use($p){
                                $cell->setValue($p->nilai)
                                ->setFontSize(12);
                            });
    
                            $notam++;
                            $not++;
                        }
                    }else{
                        $sheet->setBorder('A'.$not, 'thin');
                        $sheet->setBorder('B'.$not, 'thin');
                        $sheet->setBorder('C'.$not, 'thin');
                        $sheet->setBorder('R'.$not, 'thin');
                        
                        $sheet->cell('A'.$not, function($cell) use($notam){
                            $cell->setValue($notam)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('B'.$not, function($cell){
                            $cell->setValue('tugas tambahan')
                            ->setFontSize(12);
                        });                        
    
                        $sheet->mergeCells('C'.$not.':Q'.$not);
    
                        $sheet->cell('R'.$not, function($cell){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $not=$not+1;
                        $sheet->setBorder('A'.$not, 'thin');
                        $sheet->setBorder('B'.$not, 'thin');
                        $sheet->setBorder('C'.$not, 'thin');
                        $sheet->setBorder('R'.$not, 'thin');
                        $sheet->cell('A'.$not, function($cell) use($notam){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('B'.$not, function($cell){
                            $cell->setValue('tugas tambahan')
                            ->setFontSize(12);
                        });                        
    
                        $sheet->mergeCells('C'.$not.':Q'.$not);
    
                        $sheet->cell('R'.$not, function($cell){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $not=$not+1;
                        $sheet->setBorder('A'.$not, 'thin');
                        $sheet->setBorder('B'.$not, 'thin');
                        $sheet->setBorder('C'.$not, 'thin');
                        $sheet->setBorder('R'.$not, 'thin');
                        $sheet->cell('A'.$not, function($cell) use($notam){
                            $cell->setValue('2')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('B'.$not, function($cell){
                            $cell->setValue('kreativitas')
                            ->setFontSize(12);
                        });                        
    
                        $sheet->mergeCells('C'.$not.':Q'.$not);
    
                        $sheet->cell('R'.$not, function($cell){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $not=$not+1;
                        $sheet->setBorder('A'.$not, 'thin');
                        $sheet->setBorder('B'.$not, 'thin');
                        $sheet->setBorder('C'.$not, 'thin');
                        $sheet->setBorder('R'.$not, 'thin');
                        $sheet->cell('A'.$not, function($cell) use($notam){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('B'.$not, function($cell){
                            $cell->setValue('kreativitas')
                            ->setFontSize(12);
                        });                        
    
                        $sheet->mergeCells('C'.$not.':Q'.$not);
    
                        $sheet->cell('R'.$not, function($cell){
                            $cell->setValue('')
                            ->setFontSize(12);
                        });
                    }
    
                    $nok=$not+3;
                    $no1=$not+1;
                    $no2=$not+2;
                    $sheet->mergeCells('A'.$no1.':R'.$no1);
                    $sheet->setBorder('A'.$no1, 'thin');
                    $sheet->mergeCells('A'.$no2.':R'.$no2);
                    $sheet->setBorder('A'.$no2, 'thin');
                    $nom=$nok+1;
    
                    $sheet->setBorder('A'.$nok, 'thin');
                    $sheet->setBorder('R'.$nok, 'thin');

                    $sheet->mergeCells('A'.$nok.':Q'.$nok);
                    //$sheet->mergeCells('A'.$nok.':A'.$nom);
                    
                    $sheet->cell('A'.$nok, function($cell){
                        $cell->setValue('Nilai Capaian SKP')
                        ->setFont(array(
                            'size'       => '12',
                            'bold'       =>  true
                        ))
                        ->setAlignment('center');
                    });                    
    
                    $sheet->cell('R'.$nok, function($cell) use($nilai){
                        $cell->setValue($nilai->nilai_pencapaian)
                        ->setFont(array(
                            'size'       => '12',
                            'bold'       =>  true
                        ));
                    });
                    
                    $sheet->mergeCells('A'.$nom.':Q'.$nom);
                    $sheet->setBorder('A'.$nom, 'thin');
                    $sheet->setBorder('R'.$nom, 'thin');

                    $sheet->cell('R'.$nom, function($cell) use($nilai){
                        $status="";
                        if($nilai->nilai_pencapaian<100 && $nilai->nilai_pencapaian>=90){
                            $status="Sangat Baik";
                        }else if($nilai->nilai_pencapaian<90 && $nilai->nilai_pencapaian>=80){
                            $status="Baik";
                        }else if($nilai->nilai_pencapaian<80 && $nilai->nilai_pencapaian>=70){
                            $status="Cukup Baik";
                        }else if($nilai->nilai_pencapaian<70 && $nilai->nilai_pencapaian>=60){
                            $status="Cukup";
                        }else{
                            $status="Kurang";
                        }
    
                        $cell->setValue($status)
                        ->setFont(array(
                            'size'       => '12',
                            'bold'       =>  true
                        ))
                        ->setAlignment('center');
                    });
    
                    $non=$nom+1;
                    $sheet->mergeCells('M'.$non.':R'.$non);
                    $sheet->cell('M'.$non, function($cell) use($nilai){
                        $cell->setValue('Tegal, '.date('d F Y',strtotime($nilai->tgl_penilaian)))
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $nol=$non+1;
                    $sheet->mergeCells('M'.$nol.':R'.$nol);
                    $sheet->cell('M'.$nol, function($cell) use($nilai){
                        $cell->setValue('Pejabat Penilai, ')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $noo=$nol+4;
                    $sheet->mergeCells('M'.$noo.':R'.$noo);
                    $sheet->cell('M'.$noo, function($cell) use($nilai){
                        if(count($nilai->penilai)>0){
                            $cell->setValue($nilai->penilai->nama_lengkap)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        }
                    });
    
                    $nop=$noo+1;
                    $sheet->mergeCells('M'.$nop.':R'.$nop);
                    $sheet->cell('M'.$nop, function($cell) use($nilai){
                        if(count($nilai->penilai)>0){
                            $cell->setValue($nilai->penilai->nip)
                            ->setFontSize(12)
                            ->setAlignment('center');
                        }
                        
                    });
                }
                
                /*============= END KEGIATAN / REALISASI ======*/
            });

            $excel->sheet('Perilaku Kerja',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('A8:G8');

                /* set size */
                $sheet->setHeight(array(
                    1     =>  30,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                    5     =>  20,
                    6     =>  30,
                    7     =>  20,
                    8     =>  20,
                    9     =>  20,
                    10     =>  20,
                    11     =>  20,
                    12     =>  20,
                    13     =>  20,
                    14     =>  20,
                    15     =>  20,
                    16     =>  20,
                    17     =>  20,
                    18     =>  20,
                    19     =>  20,
                    20     =>  20,
                    21     =>  20,
                    22     =>  20,
                    23     =>  20,
                    24     =>  20,
                    25     =>  20,
                ));

                $sheet->setSize(array(
                    'A3' => array(
                        'width'     => 5,
                        'height'    => 20
                    ),
                    'B3' => array(
                        'width'     => 30,
                        'height'    => 20
                    ),
                    'C3' => array(
                        'width'     => 23,
                        'height'    => 20
                    ),
                    'J3' => array(
                        'width'     => 35,
                        'height'    => 20
                    )
                ));

                $sheet->setWidth('F', 15);
                $sheet->setWidth('G', 35);

                $sheet->setBorder('A6', 'thin');
                $sheet->setBorder('B6', 'thin');
                $sheet->setBorder('C6', 'thin');
                $sheet->setBorder('G6', 'thin');

                $sheet->setBorder('A7', 'thin');
                $sheet->setBorder('B7', 'thin');
                $sheet->setBorder('C7', 'thin');
                $sheet->setBorder('G7', 'thin');

                $sheet->setBorder('A8', 'thin');
                $sheet->setBorder('A9', 'thin');
                $sheet->setBorder('B9', 'thin');
                $sheet->setBorder('C9', 'thin');
                $sheet->setBorder('G9', 'thin');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('BUKU CATATAN PENILAIAN PERILAKU PNS')
                    ->setValignment('center')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A3', function($cell) {
                    $cell->setValue('NAMA')
                    ->setFontSize(12);
                });

                $sheet->cell('A4', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });

                $sheet->cell('C3', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('C4', function($cell) {
                    $cell->setValue(':')
                    ->setFontSize(12);
                });

                $sheet->cell('D3', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nama_lengkap)
                    ->setFontSize(12);
                });

                $sheet->cell('D4', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nip)
                    ->setFontSize(12);
                });

                $sheet->cell('A6', function($cell) {
                    $cell->setValue('No.')
                    ->setValignment('center')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('B6', function($cell) {
                    $cell->setValue('Tanggal')
                    ->setValignment('center')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->mergeCells('C6:F6');
                $sheet->cell('C6', function($cell) {
                    $cell->setValue('Uraian')
                    ->setValignment('center')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('G6', function($cell) {
                    $cell->setValue('Nama/NIP dan Paraf Pejabat Penilai')
                    ->setValignment('center')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('A7', function($cell) {
                    $cell->setValue('1')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('B7', function($cell) {
                    $cell->setValue('2')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->mergeCells('C7:F7');
                $sheet->cell('C7', function($cell) {
                    $cell->setValue('3')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('G7', function($cell) {
                    $cell->setValue('4')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('A9', function($cell) {
                    $cell->setValue('1')
                    ->setFontSize(12)
                    ->setBorder(array(
                        'top'   => array(
                            'style' => 'solid'
                        )
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('B9', function($cell) use($sasaran){
                    $cell->setValue(date('d F',strtotime($sasaran->start_periode))." - ".date('d F Y',strtotime($sasaran->end_periode)))
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->mergeCells('C9:F9');
                $sheet->cell('C9', function($cell) use($sasaran){
                    $cell->setValue('Penilaian SKP sampai dengan akhir '.date('F Y',strtotime($sasaran->end_periode)).' =')
                    ->setFontSize(12);
                });

                $sheet->mergeCells('C10:F10');
                $sheet->setBorder('A10:G10', 'thin');
                $sheet->cell('C10', function($cell) use($nilai){
                    $cell->setValue($nilai->nilai_pencapaian." sedangkan penilaian perilaku kerjanya adalah")
                    ->setFontSize(12);
                });

                $sheet->mergeCells('C11:F11');
                $sheet->setBorder('A11:G11', 'thin');
                $sheet->cell('C11', function($cell){
                    $cell->setValue('sebagai berikut :')
                    ->setFontSize(12);
                });

                $no=12;
                $total=0;

                if(count($nilai->prestasi)>0){
                    foreach($nilai->prestasi as $pr){
                        $sheet->setBorder('A'.$no.':G'.$no, 'thin');
                        $sheet->cell('C'.$no, function($cell) use($pr){
                            $cell->setValue($pr->nama_perilaku)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('D'.$no, function($cell){
                            $cell->setValue('=')
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('E'.$no, function($cell) use($pr){
                            $cell->setValue($pr->pivot->nilai)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('F'.$no, function($cell) use($pr){
                            $status="";
                            if($pr->pivot->nilai<100 && $pr->pivot->nilai>=90){
                                $status="Sangat Baik";
                            }else if($pr->pivot->nilai<90 && $pr->pivot->nilai>=80){
                                $status="Baik";
                            }else if($pr->pivot->nilai<80 && $pr->pivot->nilai>=70){
                                $status="Cukup Baik";
                            }else if($pr->pivot->nilai<70 && $pr->pivot->nilai>=60){
                                $status="Cukup";
                            }else{
                                $status="Kurang";
                            }
    
                            $cell->setValue("( ".$status." )")
                            ->setFontSize(12);
                        });
    
                        $total+=$pr->pivot->nilai;
                        $no++;
                    }
                    
                    $sheet->setBorder('A'.$no.':G'.$no, 'thin');
                    $sheet->cell('C'.$no, function($cell){
                        $cell->setValue('Jumlah')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('D'.$no, function($cell){
                        $cell->setValue('=')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('E'.$no, function($cell) use($total){
                        $cell->setValue($total)
                        ->setFontSize(12);
                    });
    
                    $nos=$no+1;
                    $sheet->setBorder('A'.$nos.':G'.$nos, 'thin');
                    $ratarata=$total/count($nilai->prestasi);
    
                    $sheet->cell('C'.$nos, function($cell){
                        $cell->setValue('Nilai Rata - Rata')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('D'.$nos, function($cell){
                        $cell->setValue('=')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('E'.$nos, function($cell) use($total){
                        $cell->setValue($total)
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('F'.$nos, function($cell) use($ratarata){
                        $status="";
                        if($ratarata<100 && $ratarata>=90){
                            $status="Sangat Baik";
                        }else if($ratarata<90 && $ratarata>=80){
                            $status="Baik";
                        }else if($ratarata<80 && $ratarata>=70){
                            $status="Cukup Baik";
                        }else if($ratarata<70 && $ratarata>=60){
                            $status="Cukup";
                        }else{
                            $status="Kurang";
                        }
    
                        $cell->setValue("( ".$status." )")
                        ->setFontSize(12);
                    });

                    $nol=$nos+1;
                    $sheet->setBorder('A'.$nol.':G'.$nol, 'thin');
                    $sheet->cell('G'.$nol, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });

                    $no1=$nol+1;
                    $sheet->setBorder('A'.$no1.':G'.$no1, 'thin');

                    $no2=$no1+1;
                    $sheet->setBorder('A'.$no2.':G'.$no2, 'thin');

                    $no3=$no2+1;
                    $sheet->setBorder('A'.$no3.':G'.$no3, 'thin');
                    
                    $no4=$nol+4;
                    $sheet->setBorder('A'.$no4.':G'.$no4, 'thin');
                    $sheet->cell('G'.$no4, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
                    
                    $no5=$no4+1;
                    $sheet->setBorder('A'.$no5.':G'.$no5, 'thin');
                    $sheet->cell('G'.$no5, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
                }

            });

            $excel->sheet('Penilaian',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B12:J12');
                $sheet->mergeCells('B18:J18');
                $sheet->mergeCells('B24:J24');
                $sheet->mergeCells('A6:J6');
                $sheet->mergeCells('A7:J7');
                $sheet->mergeCells('E13:J13');
                $sheet->mergeCells('E14:J14');
                $sheet->mergeCells('E15:J15');
                $sheet->mergeCells('E16:J16');
                $sheet->mergeCells('E17:J17');
                $sheet->mergeCells('E19:J19');
                $sheet->mergeCells('E20:J20');
                $sheet->mergeCells('E21:J21');
                $sheet->mergeCells('E22:J22');
                $sheet->mergeCells('E23:J23');
                $sheet->mergeCells('E25:J25');
                $sheet->mergeCells('E26:J26');
                $sheet->mergeCells('E27:J27');
                $sheet->mergeCells('E28:J28');
                $sheet->mergeCells('E29:J29');

                $sheet->setHeight(array(
                    1     =>  30,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                    5     =>  20,
                    6     =>  20,
                    7     =>  20,
                    8     =>  20,
                    9     =>  20,
                    10     =>  20,
                    11     =>  20,
                    12     =>  20,
                    13     =>  20,
                    14     =>  20,
                    15     =>  20,
                    16     =>  20,
                    17     =>  20,
                    18     =>  20,
                    19     =>  20,
                    20     =>  20,
                    21     =>  20,
                    22     =>  20,
                    23     =>  20,
                    24     =>  20,
                    25     =>  20,
                    26     =>  20,
                    27     =>  20,
                    28     =>  20,
                    29     =>  20,
                    30     =>  20,
                    31     =>  20,
                    32     =>  20,
                    33     =>  20,
                    34     =>  20,
                    35     =>  20,
                    36     =>  20,
                    37     =>  20,
                    38     =>  20,
                    39     =>  20,
                    40     =>  20,
                    41     =>  20,
                    42     =>  20,
                    43     =>  20,
                    44     =>  20,
                    45     =>  20,
                    46     =>  20,
                    47     =>  20,
                    48     =>  20,
                    49     =>  20,
                    50     =>  20,
                    51     =>  30,
                    52     =>  20,
                    53     =>  20,
                    54     =>  20,
                    55     =>  20,
                    56     =>  20,
                    57     =>  20,
                    58     =>  20,
                    59     =>  20,
                    60     =>  20,
                    61     =>  20,
                    62     =>  20,
                    63     =>  20,
                    64     =>  20,
                    65     =>  20,
                    66     =>  20,
                    67     =>  20,
                    68     =>  20,
                    69     =>  20,
                    70     =>  20,
                    71     =>  20,
                    72     =>  20,
                    73     =>  20,
                    74     =>  20,
                    75     =>  20,
                    76     =>  20,
                    77     =>  20,
                    78     =>  20,
                    79     =>  20,
                    80     =>  20,
                    81     =>  20,
                    82     =>  20,
                    83     =>  20,
                    84     =>  20,
                    85     =>  20,
                    86     =>  20,
                    87     =>  20,
                    88     =>  20,
                    89     =>  20,
                    90     =>  20,
                    91     =>  20,
                    92     =>  20,
                    93     =>  20,
                    94     =>  20,
                    95     =>  20,
                    96     =>  20,
                    97     =>  20,
                    98     =>  20,
                    99     =>  20,
                    100     =>  20,
                ));

                $sheet->setSize(array(
                    'A1' => array(
                        'width'     => 10,
                        'height'    => 25
                    ),
                    'D3' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'D4' => array(
                        'width'     => 5,
                        'height'    => 25
                    ),
                    'B12' => array(
                        'width'     => 55,
                        'height'    => 25
                    )
                ));

                $sheet->setWidth('A', 3);
                $sheet->setWidth('B', 3);
                $sheet->setWidth('C', 30);
                $sheet->setWidth('D', 3);
                $sheet->setWidth('E', 35);
                $sheet->setWidth('F', 1);
                $sheet->setWidth('G', 3);
                $sheet->setWidth('H', 10);
                $sheet->setWidth('I', 10);

                $objDrawing = new \PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo.png')); //your image path
                $objDrawing->setCoordinates('E2');
                $objDrawing->setWorksheet($sheet);

                $sheet->cell('A6', function($cell) use($instansi){
                    $cell->setValue('PENILAIAN PRESTASI KERJA')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A7', function($cell) use($instansi){
                    $cell->setValue('PEGAWAI NEGERI SIPIL')
                    ->setFont(array(
                        'size'       => '12',
                        'bold'       =>  true
                    ))
                    ->setAlignment('center');
                });

                $sheet->cell('A10', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFontSize(12);
                });

                $sheet->cell('H9', function($cell) {
                    $cell->setValue('JANGKA WAKTU PENILAIAN')
                    ->setFontSize(12);
                });

                $sheet->cell('H10', function($cell) use($sasaran){
                    $cell->setValue(date('d F',strtotime($sasaran->start_periode))." - ".date('d F Y',strtotime($sasaran->end_periode)))
                    ->setFontSize(12);
                });

                $sheet->setBorder('B12', 'thin');
                $sheet->cell('B12', function($cell) {
                    $cell->setValue('YANG DINILAI');
                });

                $sheet->setBorder('A12', 'thin');
                $sheet->cell('A12', function($cell) {
                    $cell->setValue('1.');
                });

                $sheet->setBorder('B13', 'thin');
                $sheet->cell('B13', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->setBorder('C13', 'thin');
                $sheet->cell('C13', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->setBorder('D13', 'thin');
                $sheet->cell('D13', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E13', 'thin');
                $sheet->cell('E13', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->nama_lengkap);
                    }   
                });

                $sheet->setBorder('B14', 'thin');
                $sheet->cell('B14', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->setBorder('C14', 'thin');
                $sheet->cell('C14', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->setBorder('D14', 'thin');
                $sheet->cell('D14', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E14', 'thin');
                $sheet->cell('E14', function($cell) use($nilai){
                    if(count($nilai->pegawai)>0){
                        $cell->setValue($nilai->pegawai->nip);
                    }
                });

                $sheet->setBorder('B15', 'thin');
                $sheet->cell('B15', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->setBorder('C15', 'thin');
                $sheet->cell('C15', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->setBorder('D15', 'thin');
                $sheet->cell('D15', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E15', 'thin');
                $sheet->cell('E15', function($cell) use($nilai){
                    if(count($nilai->pegawai->pangkat)>0){
                        $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat);
                    }
                });

                $sheet->setBorder('B16', 'thin');
                $sheet->cell('B16', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->setBorder('C16', 'thin');
                $sheet->cell('C16', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->setBorder('D16', 'thin');
                $sheet->cell('D16', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E16', 'thin');
                $sheet->cell('E16', function($cell) use($nilai){
                    if(count($nilai->pegawai->jabatan)>0){
                        $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan);
                    }   
                });

                $sheet->setBorder('B17', 'thin');
                $sheet->cell('B17', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->setBorder('C17', 'thin');
                $sheet->cell('C17', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->setBorder('D17', 'thin');
                $sheet->cell('D17', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E17', 'thin');
                $sheet->cell('E17', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });

                /* yang menilai */
                $sheet->setBorder('A18', 'thin');
                $sheet->cell('A18', function($cell) {
                    $cell->setValue('2.');
                });

                $sheet->setBorder('B18', 'thin');
                $sheet->cell('B18', function($cell) {
                    $cell->setValue('PEJABAT PENILAI');
                });

                $sheet->setBorder('B19', 'thin');
                $sheet->cell('B19', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->setBorder('C19', 'thin');
                $sheet->cell('C19', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->setBorder('D19', 'thin');
                $sheet->cell('D19', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E19', 'thin');
                $sheet->cell('E19', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nama_lengkap);
                    }
                });

                $sheet->setBorder('B20', 'thin');
                $sheet->cell('B20', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->setBorder('C20', 'thin');
                $sheet->cell('C20', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->setBorder('D20', 'thin');
                $sheet->cell('D20', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E20', 'thin');
                $sheet->cell('E20', function($cell) use($nilai){
                    if(count($nilai->penilai)>0){
                        $cell->setValue($nilai->penilai->nip);
                    }
                });

                $sheet->setBorder('B21', 'thin');
                $sheet->cell('B21', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->setBorder('C21', 'thin');
                $sheet->cell('C21', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->setBorder('D21', 'thin');
                $sheet->cell('D21', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E21', 'thin');
                $sheet->cell('E21', function($cell) use($nilai){
                    if(count($nilai->penilai->pangkat)>0){
                        $cell->setValue($nilai->penilai->pangkat[0]->nama_pangkat);
                    }
                });

                $sheet->setBorder('B22', 'thin');
                $sheet->cell('B22', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->setBorder('C22', 'thin');
                $sheet->cell('C22', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->setBorder('D22', 'thin');
                $sheet->cell('D22', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E22', 'thin');
                $sheet->cell('E22', function($cell) use($nilai){
                    if(count($nilai->penilai->jabatan)>0){
                        $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan);
                    }
                });

                $sheet->setBorder('B23', 'thin');
                $sheet->cell('B23', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->setBorder('C23', 'thin');
                $sheet->cell('C23', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->setBorder('D23', 'thin');
                $sheet->cell('D23', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E23', 'thin');
                $sheet->cell('E23', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end yang menilai */

                /* atasan pejabat penilai */
                $sheet->setBorder('A24', 'thin');
                $sheet->cell('A24', function($cell) {
                    $cell->setValue('3.');
                });

                $sheet->setBorder('B24', 'thin');
                $sheet->cell('B24', function($cell) {
                    $cell->setValue('ATASAN PEJABAT PENILAI');
                });

                $sheet->setBorder('B25', 'thin');
                $sheet->cell('B25', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->setBorder('C25', 'thin');
                $sheet->cell('C25', function($cell) {
                    $cell->setValue('Nama');
                });

                $sheet->setBorder('D25', 'thin');
                $sheet->cell('D25', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E25', 'thin');
                $sheet->cell('E25', function($cell) use($nilai){
                    if(count($nilai->atasan)>0){
                        $cell->setValue($nilai->atasan->nama_lengkap);
                    }
                });

                $sheet->setBorder('B26', 'thin');
                $sheet->cell('B26', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->setBorder('C26', 'thin');
                $sheet->cell('C26', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->setBorder('D26', 'thin');
                $sheet->cell('D26', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E26', 'thin');
                $sheet->cell('E26', function($cell) use($nilai){
                    if(count($nilai->atasan)>0){
                        $cell->setValue($nilai->atasan->nip);
                    }
                });

                $sheet->setBorder('B27', 'thin');
                $sheet->cell('B27', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->setBorder('C27', 'thin');
                $sheet->cell('C27', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->setBorder('D27', 'thin');
                $sheet->cell('D27', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E27', 'thin');
                $sheet->cell('E27', function($cell) use($nilai){
                    if(count($nilai->atasan->pangkat)>0){
                        $cell->setValue($nilai->atasan->pangkat[0]->nama_pangkat);
                    }
                });

                $sheet->setBorder('B28', 'thin');
                $sheet->cell('B28', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->setBorder('C28', 'thin');
                $sheet->cell('C28', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->setBorder('D28', 'thin');
                $sheet->cell('D28', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E28', 'thin');
                $sheet->cell('E28', function($cell) use($nilai){
                    if(count($nilai->atasan->jabatan)>0){
                        $cell->setValue($nilai->atasan->jabatan[0]->nama_jabatan);
                    }
                });

                $sheet->setBorder('B29', 'thin');
                $sheet->cell('B29', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->setBorder('C29', 'thin');
                $sheet->cell('C29', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->setBorder('D29', 'thin');
                $sheet->cell('D29', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->setBorder('E29', 'thin');
                $sheet->cell('E29', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end atasan pejabat penilai */

                $sheet->setBorder('A30', 'thin');
                $sheet->cell('A30', function($cell) {
                    $cell->setValue('4.');
                });

                $sheet->mergeCells('B30:J30');
                $sheet->setBorder('B30', 'thin');
                $sheet->cell('B30', function($cell) {
                    $cell->setValue('UNSUR YANG DINILAI');
                });

                $sheet->setBorder('J30', 'thin');
                $sheet->cell('J30', function($cell) {
                    $cell->setValue('JUMLAH');
                });

                $sheet->setBorder('B31', 'thin');
                $sheet->cell('B31', function($cell) {
                    $cell->setValue('a.');
                });

                $sheet->mergeCells('C31:G31');
                $sheet->setBorder('C31', 'thin');
                $sheet->cell('C31', function($cell) {
                    $cell->setValue('Sasaran Kerja Pegawai (SKP)');
                });

                $sheet->setBorder('H31', 'thin');
                $sheet->cell('H31', function($cell) use($nilai){
                    $cell->setValue($nilai->nilai_pencapaian);
                });

                $sheet->setBorder('C31:J31', 'thin');
                $sheet->setBorder('I31', 'thin');
                $sheet->cell('I31', function($cell) {
                    $cell->setValue('X 60');
                });

                $sheet->setBorder('J31', 'thin');
                $sheet->cell('J31', function($cell) use($nilai){
                    $total=$nilai->nilai_pencapaian*60/100;
                    $cell->setValue($total);
                });

                $jumlahprestasi=count($nilai->prestasi);
                $rowsekarang=32;
                $rowkebawah=32+((count($nilai->prestasi))+2);

                $sheet->mergeCells('B'.$rowsekarang.':B'.$rowkebawah);
                $sheet->setBorder('B'.$rowsekarang.':B'.$rowkebawah, 'thin');
                $sheet->cell('B'.$rowsekarang, function($cell) {
                    $cell->setValue('b.')
                    ->setAlignment('center');
                });


                $nn=1;
                $totalperilaku=0;
                if(count($nilai->prestasi)>0){
                    foreach($nilai->prestasi as $pr){
                        $sheet->setBorder('C'.$rowsekarang.':J'.$rowsekarang, 'thin');
                        $sheet->mergeCells('D'.$rowsekarang.':G'.$rowsekarang);

                        $sheet->cell('C'.$rowsekarang, function($cell) use($nn){
                            $cell->setValue($nn)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('D'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue($pr->nama_perilaku)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('H'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue($pr->pivot->nilai)
                            ->setFontSize(12);
                        });
    
                        $sheet->cell('I'.$rowsekarang, function($cell) use($pr){
                            $status="";
                            if($pr->pivot->nilai<100 && $pr->pivot->nilai>=90){
                                $status="Sangat Baik";
                            }else if($pr->pivot->nilai<90 && $pr->pivot->nilai>=80){
                                $status="Baik";
                            }else if($pr->pivot->nilai<80 && $pr->pivot->nilai>=70){
                                $status="Cukup Baik";
                            }else if($pr->pivot->nilai<70 && $pr->pivot->nilai>=60){
                                $status="Cukup";
                            }else{
                                $status="Kurang";
                            }
    
                            $cell->setValue("( ".$status." )")
                            ->setFontSize(12);
                            
                        });
    
                        $totalperilaku+=$pr->pivot->nilai;
                        $rowsekarang++;
                        $nn++;
                    }
                    
                    $sheet->setBorder('C'.$rowsekarang.':J'.$rowsekarang, 'thin');
                    $sheet->mergeCells('C'.$rowsekarang.':G'.$rowsekarang);
                    $sheet->mergeCells('C'.$rowsekarang.':G'.$rowsekarang);
                    $sheet->cell('C'.$rowsekarang, function($cell){
                        $cell->setValue('Jumlah')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('H'.$rowsekarang, function($cell) use($totalperilaku){
                        $cell->setValue($totalperilaku)
                        ->setFontSize(12);
                    });
    
                    $rowratarata=$rowsekarang+1;
                    $sheet->setBorder('C'.$rowratarata.':J'.$rowratarata, 'thin');
                    $sheet->mergeCells('C'.$rowratarata.':G'.$rowratarata);
                    $sheet->cell('C'.$rowratarata, function($cell){
                        $cell->setValue('Nilai Rata - Rata')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('H'.$rowratarata, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi);
                        $cell->setValue($ratarata)
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('I'.$rowratarata, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi);
    
                        $status="";
                        if($ratarata<100 && $ratarata>=90){
                            $status="Sangat Baik";
                        }else if($ratarata<90 && $ratarata>=80){
                            $status="Baik";
                        }else if($ratarata<80 && $ratarata>=70){
                            $status="Cukup Baik";
                        }else if($ratarata<70 && $ratarata>=60){
                            $status="Cukup";
                        }else{
                            $status="Kurang";
                        }
    
                        $cell->setValue("( ".$status." )")
                        ->setFontSize(12);
                    });
    
                    $rowperilaku=$rowratarata+1;
                    $sheet->setBorder('C'.$rowperilaku.':J'.$rowperilaku, 'thin');
                    $sheet->mergeCells('C'.$rowperilaku.':G'.$rowperilaku);
                    $sheet->cell('C'.$rowperilaku, function($cell){
                        $cell->setValue('Nilai Perilaku Kerja')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('H'.$rowperilaku, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi);
                        $cell->setValue($ratarata)
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('I'.$rowperilaku, function($cell) use($nilai,$totalperilaku){
                        $cell->setValue('X 40')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('J'.$rowperilaku, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi)*(40/100);
    
                        $cell->setValue($ratarata)
                        ->setFontSize(12);
                    });
    
                    $rowprestasi=$rowperilaku+1;
                    $sheet->setBorder('A'.$rowprestasi.':J'.$rowprestasi, 'thin');
                    $sheet->mergeCells('A'.$rowprestasi.':I'.$rowprestasi);
    
                    $sheet->cell('A'.$rowprestasi, function($cell){
                        $cell->setValue('Nilai Prestasi Kerja')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('J'.$rowprestasi, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi)*(40/100);
                        $total=$nilai->nilai_pencapaian*60/100;
    
                        $hasil=$total+$ratarata;
                        $cell->setValue($hasil)
                        ->setFontSize(12);
                    });
    
                    $rowpr=$rowprestasi+1;
                    $sheet->setBorder('A'.$rowpr.':J'.$rowpr, 'thin');
                    $sheet->mergeCells('A'.$rowpr.':I'.$rowpr);
                    $sheet->cell('J'.$rowpr, function($cell) use($nilai,$totalperilaku){
                        $ratarata=$totalperilaku/count($nilai->prestasi)*(40/100);
                        $total=$nilai->nilai_pencapaian*60/100;
    
                        $hasil=$total+$ratarata;
    
                        $status="";
                        if($hasil<100 && $hasil>=90){
                            $status="Sangat Baik";
                        }else if($hasil<90 && $hasil>=80){
                            $status="Baik";
                        }else if($hasil<80 && $hasil>=70){
                            $status="Cukup Baik";
                        }else if($hasil<70 && $hasil>=60){
                            $status="Cukup";
                        }else{
                            $status="Kurang";
                        }
    
                        $cell->setValue($status)
                        ->setFontSize(12);
                    });
    
                    $rowkeberatan=$rowpr+4;
                    $rowkeberatans=$rowkeberatan+1;
                    $sheet->cell('A'.$rowkeberatan, function($cell){
                        $cell->setValue('5.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowkeberatan, function($cell){
                        $cell->setValue('KEBERATAN DARI PEGAWAI NEGERI SIPIL')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowkeberatans, function($cell){
                        $cell->setValue('YANG DINILAI (APABILA ADA)')
                        ->setFontSize(12);
                    });
    
                    $rowtanggalkeberatan=$rowkeberatan+6;
                    $sheet->cell('I'.$rowtanggalkeberatan, function($cell){
                        $cell->setValue('Tanggal ...........................................')
                        ->setFontSize(12);
                    });
    
                    $rowtanggapan=$rowtanggalkeberatan+2;
                    $sheet->cell('A'.$rowtanggapan, function($cell){
                        $cell->setValue('6.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowtanggapan, function($cell){
                        $cell->setValue('TANGGAPAN PEJABAT PENILAI ATAS KEBERATAN')
                        ->setFontSize(12);
                    });
    
                    $rowtanggaltanggapan=$rowtanggapan+4;
                    $sheet->cell('I'.$rowtanggaltanggapan, function($cell){
                        $cell->setValue('Tanggal ...........................................')
                        ->setFontSize(12);
                    });
    
    
                    $rowkeputusan=$rowtanggaltanggapan+2;
                    $sheet->cell('A'.$rowkeputusan, function($cell){
                        $cell->setValue('7.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowkeputusan, function($cell){
                        $cell->setValue('KEPUTUSAN ATASAN PEJABAT PENILAI ATAS KEBERATAN')
                        ->setFontSize(12);
                    });
    
                    $rowtanggalkeputusan=$rowkeputusan+4;
                    $sheet->cell('I'.$rowtanggalkeputusan, function($cell){
                        $cell->setValue('Tanggal ...........................................')
                        ->setFontSize(12);
                    });
    
                    $rowrekomendasi=$rowtanggalkeputusan+2;
                    $sheet->cell('A'.$rowrekomendasi, function($cell){
                        $cell->setValue('8.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowrekomendasi, function($cell){
                        $cell->setValue('REKOMENDASI')
                        ->setFontSize(12);
                    });
    
    
                    $rowdibuat=$rowrekomendasi+5;
                    $sheet->cell('G'.$rowdibuat, function($cell){
                        $cell->setValue('9.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('H'.$rowdibuat, function($cell){
                        $cell->setValue('DIBUAT TANGGAL,     '.date('F Y'))
                        ->setFontSize(12);
                    });
    
                    $rowafterdibuat=$rowdibuat+1;
                    $sheet->mergeCells('H'.$rowafterdibuat.':J'.$rowafterdibuat);
                    $sheet->cell('H'.$rowafterdibuat, function($cell){
                        $cell->setValue('PEJABAT PENILAI')
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rownamapenilai=$rowafterdibuat+5;
                    $sheet->mergeCells('H'.$rownamapenilai.':J'.$rownamapenilai);
                    $sheet->cell('H'.$rownamapenilai, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rownippenilai=$rownamapenilai+1;
                    $sheet->mergeCells('H'.$rownippenilai.':J'.$rownippenilai);
                    $sheet->cell('H'.$rownippenilai, function($cell) use($nilai){
                        $cell->setValue($nilai->penilai->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rowditerima=$rownippenilai+2;
                    $sheet->cell('A'.$rowditerima, function($cell) use($nilai){
                        $cell->setValue('10.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('B'.$rowditerima, function($cell) use($nilai){
                        $cell->setValue('DITERIMA TANGGAL, '.date('F Y'))
                        ->setFontSize(12);
                    });
    
                    $rowafterditerima=$rowditerima+1;
                    $sheet->cell('B'.$rowafterditerima, function($cell) use($nilai){
                        $cell->setValue('PEGAWAI NEGERI SIPIL YANG DINILAI')
                        ->setFontSize(12);
                    });
    
                    $rowyangdinilai=$rowafterditerima+4;
                    $sheet->mergeCells('B'.$rowyangdinilai.':F'.$rowyangdinilai);
                    $sheet->cell('B'.$rowyangdinilai, function($cell) use($nilai){
                        $cell->setValue($nilai->pegawai->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rownipyangdinilai=$rowyangdinilai+1;
                    $sheet->mergeCells('B'.$rownipyangdinilai.':F'.$rownipyangdinilai);
                    $sheet->cell('B'.$rownipyangdinilai, function($cell) use($nilai){
                        $cell->setValue($nilai->pegawai->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rowditerimaatasan=$rownipyangdinilai+2;
                    $sheet->cell('G'.$rowditerimaatasan, function($cell){
                        $cell->setValue('11.')
                        ->setFontSize(12);
                    });
    
                    $sheet->cell('H'.$rowditerimaatasan, function($cell) use($nilai){
                        $cell->setValue('DITERIMA TANGGAL,  '.date('F Y'))
                        ->setFontSize(12);
                    });
    
                    $rowafterditerimaatasan=$rowditerimaatasan+1;
                    $sheet->cell('H'.$rowafterditerimaatasan, function($cell) use($nilai){
                        $cell->setValue('ATASAN PEJABAT YANG MENILAI')
                        ->setFontSize(12);
                    });
    
                    $rownipatasan=$rowafterditerimaatasan+4;
                    $sheet->mergeCells('H'.$rownipatasan.':J'.$rownipatasan);
                    $sheet->cell('H'.$rownipatasan, function($cell) use($nilai){
                        $cell->setValue($nilai->atasan->nama_lengkap)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
    
                    $rownamaatasan=$rownipatasan+1;
                    $sheet->mergeCells('H'.$rownamaatasan.':J'.$rownamaatasan);
                    $sheet->cell('H'.$rownamaatasan, function($cell) use($nilai){
                        $cell->setValue($nilai->atasan->nip)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });
                }
            });
        })->setActiveSheetIndex(0)->export('xlsx');
    }
}
