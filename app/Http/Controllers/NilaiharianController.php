<?php

namespace App\Http\Controllers;

use App\Nilaiharian;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NilaiharianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTables $dataTables)
    {
        if($request->ajax()){
            $user=\App\User::with('pegawai')->find(\Auth::user()->id);
            \DB::statement(\DB::raw('set @rownum=0'));

            $nilai=Nilaiharian::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','type_kegiatan',
                'pegawai_id','type_kegiatan','tanggal','dari_jam','sampai_jam','kegiatan',
                'hasil','keterangan')
                ->with('pegawai');
            
            if(\Auth::user()->level=="pegawai"){
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }
            
            return $dataTables->eloquent($nilai)   
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='#' class='btn btn-warning btn-sm editharian' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapusharian' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action','keterangan'])
                ->make(true);	
        }

        return view('dashboard.kegiatan_harian')
            ->with('home','Dashboard')
            ->with('title','Kegiatan Harian');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=[
            'tanggal'=>'required',
            'type'=>'required',
            'darijam'=>'required',
            'sampaijam'=>'required',
            'kegiatan'=>'required',
            'hasil'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal harus diisi',
            'darijam.required'=>'Dari jam harus diisi',
            'sampaijam.required'=>'Sampai jam harus diisi',
            'kegiatan.required'=>'Kegiatan harus diisi',
            'hasil.required'=>'Hasil Kegiatan harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=\App\User::with('pegawai')->find(\Auth::user()->id);

            $nilai=new Nilaiharian;
            $nilai->type_kegiatan=$request->input('type');
            $nilai->pegawai_id=$user->pegawai[0]->id;
            $nilai->tanggal=date('Y-m-d',strtotime($request->input('tanggal')));
            $nilai->dari_jam=date('H:i:s',strtotime($request->input('darijam')));
            $nilai->sampai_jam=date('H:i:s',strtotime($request->input('sampaijam')));
            $nilai->kegiatan=$request->input('kegiatan');
            $nilai->hasil=$request->input('hasil');
            $nilai->keterangan=$request->input('keterangan');
            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Nilaiharian  $nilaiharian
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $nilai=Nilaiharian::find($id);

        return $nilai;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Nilaiharian  $nilaiharian
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Nilaiharian  $nilaiharian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules=[
            'tanggal'=>'required',
            'darijam'=>'required',
            'sampaijam'=>'required',
            'kegiatan'=>'required',
            'hasil'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal harus diisi',
            'darijam.required'=>'Dari jam harus diisi',
            'sampaijam.required'=>'Sampai jam harus diisi',
            'kegiatan.required'=>'Kegiatan harus diisi',
            'hasil.required'=>'Hasil Kegiatan harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=\App\User::with('pegawai')->find(\Auth::user()->id);

            $nilai=Nilaiharian::find($id);
            $nilai->type_kegiatan=$request->input('type');
            $nilai->pegawai_id=$user->pegawai[0]->id;
            $nilai->tanggal=date('Y-m-d',strtotime($request->input('tanggal')));
            $nilai->dari_jam=date('H:i:s',strtotime($request->input('darijam')));
            $nilai->sampai_jam=date('H:i:s',strtotime($request->input('sampaijam')));
            $nilai->kegiatan=$request->input('kegiatan');
            $nilai->hasil=$request->input('hasil');
            $nilai->keterangan=$request->input('keterangan');
            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nilaiharian  $nilaiharian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nilai=\App\Nilaiharian::find($id);

        $hapus=$nilai->delete();

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

    public function report_kegiatan_harian(Request $request){
        $user=\App\User::with('pegawai')->find(\Auth::user()->id);
        $pegawai=\App\Pegawai::select('id','nama_lengkap')->get();

        $bawahan=\App\Pegawai::where('atasan_langsung',$user->pegawai[0]->id)
                ->with(
                    [
                        'harian'=>function($q){
                            $q->whereNull('approved');
                        }
                    ]
                )->get();

        return view('dashboard.nilai.report_kegiatan_harian')
            ->with('title','Report Kegiatan Harian')
            ->with('pegawai',$pegawai)
            ->with('bawahan',$bawahan)
            ->with('home','Dashboard');
    }

    public function preview_kegiatan_harian(Request $request){
        $rules=[
            'dari'=>'required'
        ];

        $pesan=[
            'dari.required'=>'Dari harus diisi',
            'sampai.required'=>'Sampai harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>"Validasi error",
                'error'=>$validasi->errors()->all(),
                'nilai'=>array()
            );
        }else{
            $start=date('Y-m-d',strtotime($request->input('dari')));
            $end=date('Y-m-d',strtotime($request->input('sampai')));

            $nilai=\App\Nilaiharian::with('pegawai')
                ->whereBetween('tanggal',[$start,$end]);

            $p="";
            if($request->has('pegawai') && $request->input('pegawai')!=""){
                $nilai=$nilai->where('pegawai_id',$request->input('pegawai'));
                $p=$request->input('pegawai');
            }else{
                $user=\App\User::with('pegawai')->find(\Auth::user()->id);
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
                $p=$user->pegawai[0]->id;
            }

            $nilai=$nilai->get();

            $data=array(
                'success'=>true,
                'pesan'=>"Data Berhasil diload",
                'error'=>'',
                'nilai'=>$nilai,
                'dari'=>$start,
                'sampai'=>$end,
                'pegawai'=>$p
            );
        }

        return $data;
    }

    public function export_kegiatan_harian(Request $request){
        $start=date('Y-m-d',strtotime($request->input('dari')));
        $end=date('Y-m-d',strtotime($request->input('sampai')));
        $p=$request->input('pegawai');

        $nilai=\App\Nilaiharian::with('pegawai')
            ->whereBetween('tanggal',[$start,$end]);

        if($p!=""){
            $userpegawai=\DB::table('user_pegawai')
                ->where('pegawai_id',$p)->first();

            $user=\App\User::with('pegawai','pegawai.atasan','pegawai.jabatan')->find($userpegawai->user_id);
            $nilai=$nilai->where('pegawai_id',$p);
        }else{
            if(\Auth::user()->level=="pegawai"){
                $user=\App\User::with('pegawai','pegawai.atasan','pegawai.jabatan')->find(\Auth::user()->id);
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }
        }

        $nilai=$nilai->get();

        $instansi=\App\Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();
        
            return \Excel::create('kegiatan harian',function($excel) use($instansi,$nilai,$user){
                $excel->sheet('cover',function($sheet) use($nilai,$instansi,$user){
                    $sheet->mergeCells('A1:F1');
                    $sheet->mergeCells('A2:F2');

                    $sheet->cell('A1', function($cell) {
                        $cell->setValue('MAHKAMAH AGUNG RI')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('A2', function($cell) use($instansi){
                        $cell->setValue($instansi->nama_instansi)
                        ->setFontSize(12)
                        ->setAlignment('center');
                    });

                    $sheet->cell('A4', function($cell){
                        $cell->setValue('Nama')
                        ->setFontSize(14);
                    });

                    $sheet->cell('B4', function($cell){
                        $cell->setValue(':')
                        ->setFontSize(14);
                    });

                    $sheet->cell('C4', function($cell) use($user){
                        $cell->setValue($user->pegawai[0]->nama_lengkap)
                        ->setFontSize(14);
                    });

                    $sheet->cell('A5', function($cell){
                        $cell->setValue('Jabatan')
                        ->setFontSize(14);
                    });

                    $sheet->cell('B5', function($cell){
                        $cell->setValue(':')
                        ->setFontSize(14);
                    });

                    $sheet->cell('C5', function($cell) use($user){
                        $cell->setValue($user->pegawai[0]->jabatan[0]->nama_jabatan)
                        ->setFontSize(14);
                    });

                    /*=== HEADING TABEL ====== */
                    $sheet->cell('A7', function($cell){
                        $cell->setValue('No')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('B7', function($cell){
                        $cell->setValue('Tanggal')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('C7', function($cell){
                        $cell->setValue('Jam')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('D7', function($cell){
                        $cell->setValue('Kegiatan')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('E7', function($cell){
                        $cell->setValue('Hasil / Volume')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $sheet->cell('F7', function($cell){
                        $cell->setValue('Keterangan')
                        ->setFontSize(14)
                        ->setAlignment('center');
                    });

                    $rowsekarang=8;
                    $no=1;
                    foreach($nilai as $pr){
                        $sheet->cell('A'.$rowsekarang, function($cell) use($no){
                            $cell->setValue($no)
                            ->setFontSize(14);
                        });

                        $sheet->cell('B'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue($pr->tanggal)
                            ->setFontSize(14);
                        });

                        $sheet->cell('C'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue(date('H:i:s',strtotime($pr->dari_jam))." s/d ".date('H:i:s',strtotime($pr->sampai_jam)))
                            ->setFontSize(14);
                        });

                        $sheet->cell('D'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue($pr->kegiatan)
                            ->setFontSize(14);
                        });

                        $sheet->cell('E'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue($pr->hasil)
                            ->setFontSize(14);
                        });

                        $sheet->cell('F'.$rowsekarang, function($cell) use($pr){
                            $cell->setValue(strip_tags($pr->keterangan))
                            ->setFontSize(14);
                        });

                        $rowsekarang++;
                        $no++;
                    }
                    /*==== END HEADING TABEL =====*/

                    $sheet->cell('A'.$rowsekarang, function($cell){
                        $cell->setValue('Mengetahui :')
                        ->setFontSize(14);
                    });

                    $rowatasan=$rowsekarang+2;
                    $sheet->cell('A'.$rowatasan, function($cell){
                        $cell->setValue('Atasan Langsung')
                        ->setFontSize(14);
                    });

                    $sheet->cell('F'.$rowatasan, function($cell){
                        $cell->setValue('Yang Melaksanakan, ')
                        ->setFontSize(14);
                    });


                    $rownamaatasan=$rowatasan+4;
                    $sheet->cell('A'.$rownamaatasan, function($cell) use($user){
                        if(count($user->pegawai[0]->atasan)>0){
                            $cell->setValue($user->pegawai[0]->atasan->nama_lengkap)
                                ->setFontSize(14);
                        }else{
                            $cell->setValue("")
                                ->setFontSize(14);
                        }
                        
                    });

                    $sheet->cell('F'.$rownamaatasan, function($cell) use($user){
                        $cell->setValue($user->pegawai[0]->nama_lengkap)
                        ->setFontSize(14);
                    });

                    $rownipatasan=$rownamaatasan+1;
                    $sheet->cell('A'.$rownipatasan, function($cell) use($user){
                        if(count($user->pegawai[0]->atasan)>0){
                            $cell->setValue($user->pegawai[0]->atasan->nip)
                                ->setFontSize(14);
                        }else{
                            $cell->setValue("")
                                ->setFontSize(14);
                        }
                    });

                    $sheet->cell('F'.$rownipatasan, function($cell) use($user){
                        $cell->setValue($user->pegawai[0]->nip)
                        ->setFontSize(14);
                    });

                });
            })->export('xlsx');
    }


    public function report_nilai_skp(Request $request){
        $sasaran=\App\Sasarankerja::all();
        $pegawai=\App\Pegawai::select('id','nama_lengkap')->get();

        return view('dashboard.nilai.report_nilai_skp')
            ->with('title','Report Nilai SKP')
            ->with('home','Dashboard')
            ->with('pegawai',$pegawai)
            ->with('sasaran',$sasaran);
    }

    public function report_skp_preview(Request $request){
        $rules=[
            'sasaran'=>'required'
        ];

        $pesan=[
            'sasaran.required'=>'Sasaran harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'error'=>$validasi->errors()->all(),
                'nilai'=>array()
            );
        }else{
            $sasaran=$request->input('sasaran');

            $nilai=\App\Nilaiskp::where('sasaran_kerja_id',$sasaran)
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
            
            if(\Auth::user()->level=="pegawai"){
                $user=\App\User::with('pegawai','pegawai.atasan','pegawai.jabatan')->find(\Auth::user()->id);
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }

            if($request->has('pegawai')){
                $nilai=$nilai->where('pegawai_id',$request->input('pegawai'));
            }

            $nilai=$nilai->get();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diload',
                'error'=>'',
                'nilai'=>$nilai
            );
        }

        return $data;
    }
}
