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
                    'atasan'
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
                
                $sheet->cell('A8', function($cell) {
                    $cell->setValue('PENILAIAN PRESTASI KERJA')
                    ->setFontSize(14)
                    ->setAlignment('center');
                });

                $sheet->cell('A9', function($cell) {
                    $cell->setValue('PEGAWAI NEGERI SIPIL')
                    ->setFontSize(14)
                    ->setAlignment('center');
                });

                $sheet->cell('A15', function($cell) {
                    $cell->setValue('Jangka Waktu Penilaian')
                    ->setFontSize(14)
                    ->setAlignment('center');
                });

                $sheet->cell('A16', function($cell) use($sasaran){
                    $cell->setValue(date('d F Y',strtotime($sasaran->start_periode))." - ".date('d F Y',strtotime($sasaran->end_periode)))
                    ->setFontSize(14)
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
                    ->setFontSize(12);
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
                    ->setFontSize(12);
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
                    $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat)
                    ->setFontSize(12);
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
                    $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan)
                    ->setFontSize(12);
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
                    ->setFontSize(12);
                });

                $sheet->cell('A39', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi)
                    ->setFontSize(14)
                    ->setAlignment('center');
                });

                $sheet->cell('A40', function($cell){
                    $cell->setValue('TAHUN '.date('Y'))
                    ->setFontSize(14)
                    ->setAlignment('center');
                });

            });

            $excel->sheet('data_skp',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B6:E6');
                $sheet->mergeCells('B12:E12');
                $sheet->mergeCells('B18:E18');

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
                    )
                ));

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('DATA SASARAN KERJA PEGAWAI')
                    ->setFontSize(14)
                    ->setAlignment('center')
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
                    $cell->setValue(':');
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
                    $cell->setValue(':');
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
                    $cell->setValue(':');
                });

                $sheet->cell('E9', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat);
                });

                $sheet->cell('B10', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C10', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D10', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E10', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan);
                });

                $sheet->cell('B11', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C11', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D11', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E11', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });

                /* yang menilai */
                $sheet->cell('A12', function($cell) {
                    $cell->setValue('1.')
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
                    $cell->setValue(':');
                });

                $sheet->cell('E13', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->nama_lengkap);
                });

                $sheet->cell('B14', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->cell('C14', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->cell('D14', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E14', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->nip);
                });

                $sheet->cell('B15', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->cell('C15', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->cell('D15', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E15', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->pangkat[0]->nama_pangkat);
                });

                $sheet->cell('B16', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C16', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D16', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E16', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan);
                });

                $sheet->cell('B17', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C17', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D17', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E17', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end yang menilai */

                /* atasan pejabat penilai */
                $sheet->cell('A18', function($cell) {
                    $cell->setValue('1.')
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
                    $cell->setValue(':');
                });

                $sheet->cell('E19', function($cell) use($nilai){
                    $cell->setValue($nilai->atasan->nama_lengkap);
                });

                $sheet->cell('B20', function($cell) {
                    $cell->setValue('b.');
                });

                $sheet->cell('C20', function($cell) {
                    $cell->setValue('NIP');
                });

                $sheet->cell('D20', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E20', function($cell) use($nilai){
                    $cell->setValue($nilai->atasan->nip);
                });

                $sheet->cell('B21', function($cell) {
                    $cell->setValue('c.');
                });

                $sheet->cell('C21', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang');
                });

                $sheet->cell('D21', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E21', function($cell) use($nilai){
                    $cell->setValue($nilai->atasan->pangkat[0]->nama_pangkat);
                });

                $sheet->cell('B22', function($cell) {
                    $cell->setValue('d.');
                });

                $sheet->cell('C22', function($cell) {
                    $cell->setValue('Jabatan');
                });

                $sheet->cell('D22', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E22', function($cell) use($nilai){
                    $cell->setValue($nilai->atasan->jabatan[0]->nama_jabatan);
                });

                $sheet->cell('B23', function($cell) {
                    $cell->setValue('e.');
                });

                $sheet->cell('C23', function($cell) {
                    $cell->setValue('Unit Kerja');
                });

                $sheet->cell('D23', function($cell) {
                    $cell->setValue(':');
                });

                $sheet->cell('E23', function($cell) use($instansi){
                    $cell->setValue($instansi->nama_instansi);
                });
                /* end atasan pejabat penilai */

            });

            $excel->sheet('form_skp',function($sheet) use($sasaran,$nilai,$instansi){
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('B3:C3');
                $sheet->mergeCells('F3:K3');
                $sheet->mergeCells('A9:A10');
                $sheet->mergeCells('B9:B10');
                $sheet->mergeCells('E9:E10');
                $sheet->mergeCells('F9:K9');
                $sheet->mergeCells('F10:G10');
                $sheet->mergeCells('I10:J10');

                $sheet->cell('A1', function($cell) {
                    $cell->setValue('SASARAN KERJA PEGAWAI')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('A3', function($cell) {
                    $cell->setValue('No.')
                    ->setFontSize(12)
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
                    ->setFontSize(12);
                });
                $sheet->cell('B4', function($cell) {
                    $cell->setValue('Nama')
                    ->setFontSize(12);
                });
                $sheet->cell('C4', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->nama_lengkap)
                    ->setFontSize(12);
                });
                $sheet->cell('B5', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });
                $sheet->cell('C5', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->nip)
                    ->setFontSize(12);
                });
                $sheet->cell('B6', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang')
                    ->setFontSize(12);
                });
                $sheet->cell('C6', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->pangkat[0]->nama_pangkat)
                    ->setFontSize(12);
                });
                $sheet->cell('B7', function($cell) {
                    $cell->setValue('Jabatan')
                    ->setFontSize(12);
                });
                $sheet->cell('C7', function($cell) use($nilai){
                    $cell->setValue($nilai->penilai->jabatan[0]->nama_jabatan)
                    ->setFontSize(12);
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
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('F3', function($cell) {
                    $cell->setValue('II. PEGAWAI NEGERI SIPIL YANG DINILAI')
                    ->setFontSize(12);
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
                    $cell->setValue($nilai->pegawai->nama_lengkap)
                    ->setFontSize(12);
                });
                $sheet->cell('F5', function($cell) {
                    $cell->setValue('NIP')
                    ->setFontSize(12);
                });
                $sheet->cell('G5', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->nip)
                    ->setFontSize(12);
                });
                $sheet->cell('F6', function($cell) {
                    $cell->setValue('Pangkat / Gol. Ruang')
                    ->setFontSize(12);
                });
                $sheet->cell('G6', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->pangkat[0]->nama_pangkat)
                    ->setFontSize(12);
                });
                $sheet->cell('F7', function($cell) {
                    $cell->setValue('Jabatan')
                    ->setFontSize(12);
                });
                $sheet->cell('G7', function($cell) use($nilai){
                    $cell->setValue($nilai->pegawai->jabatan[0]->nama_jabatan)
                    ->setFontSize(12);
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
                    ->setFontSize(12);
                });
                $sheet->cell('B9', function($cell){
                    $cell->setValue('III. KEGIATAN TUGAS JABATAN')
                    ->setFontSize(12);
                });
                $sheet->cell('E9', function($cell){
                    $cell->setValue('AK')
                    ->setFontSize(12);
                });
                $sheet->cell('F9', function($cell){
                    $cell->setValue('TARGET')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });

                $sheet->cell('F10', function($cell){
                    $cell->setValue('KUANT/OUTPUT')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('H10', function($cell){
                    $cell->setValue('KUAL/MUTU')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('I10', function($cell){
                    $cell->setValue('WAKTU')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                $sheet->cell('K10', function($cell){
                    $cell->setValue('BIAYA')
                    ->setFontSize(12)
                    ->setAlignment('center');
                });
                
                $no=11;
                $nos=1;
                foreach($nilai->pegawai->tugas as $row){
                    $sheet->row($no, array(
                        $nos,
                        $row->nama_tugas, 
                        '',
                        '',
                        '',
                        $row->target[0]->kuant,
                        $row->target[0]->output,
                        $row->target[0]->kual,
                        $row->target[0]->waktu,
                        $row->target[0]->periode_waktu,
                        $row->target[0]->biaya,
                    ));

                    $no++;
                    $nos++;
                }

                
            });
        })->export('xlsx');
    }
}
