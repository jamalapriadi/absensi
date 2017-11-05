<?php

namespace App\Http\Controllers;

use App\Pegawai;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,DataTables $dataTables)
    {
        if($request->ajax()){
            \DB::statement(\DB::raw('set @rownum=0'));
            $pegawai=Pegawai::with(
                [
                    'status'=>function($q){
                        $q->where('active','Y');
                    },
                    'status.pangkat',
                    'status.kepegawaian'
                ]
            )
                ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nip','tmk','nama_lengkap',
                'tempat_lahir','tanggal_lahir','agama','alamat','active','foto');

            return $dataTables->eloquent($pegawai)   
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='".\URL::to('home/pegawai/'.$row->id)."' class='btn btn-info btn-sm' title='Histroy'><i class='icon-history'></i></a>";
                        $html.="<a href='".\URL::to('home/pegawai/'.$row->id.'/edit')."' class='btn btn-warning btn-sm' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapuspegawai' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->addColumn('gambar',function($q){
                    $html=\Html::image('uploads/pegawai/'.$q->foto,'',array('class'=>'img-responsive','style'=>'width:80px;'));

                    return $html;
                })
                ->rawColumns(['action','gambar'])
                ->make(true);   
        }

        return view('dashboard.pegawai.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $status=\App\Status::select('id','nama_status')->get();
        $pangkat=\App\Pangkat::select('id','nama_pangkat','ruang')->get();
        $jabatan=\App\Jabatan::select('id','nama_jabatan')->get();

        return view('dashboard.pegawai.create')
            ->with('status',$status)
            ->with('pangkat',$pangkat)
            ->with('jabatan',$jabatan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validasi=\Validator::make($request->all(),Pegawai::$rules,Pegawai::$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $pegawai=new Pegawai;
            $pegawai->nip=$request->input('nip');
            $pegawai->tmk=$request->input('tmk');
            $pegawai->nama_lengkap=$request->input('nama');
            $pegawai->tanggal_lahir=date('Y-m-d',strtotime($request->input('tanggal')));
            $pegawai->tempat_lahir=$request->input('tempat');
            $pegawai->agama=$request->input('agama');
            $pegawai->alamat=$request->input('alamat');
            $pegawai->active='Y';

            if($request->hasFile('file')){
                if (!is_dir('uploads/pegawai/')) {
                    mkdir('uploads/pegawai/', 0777, TRUE);
                }

                $file=$request->file('file');
                $filename=str_random(5).'-'.$file->getClientOriginalName();
                $destinationPath='uploads/pegawai/';
                $file->move($destinationPath,$filename);
                $pegawai->foto=$filename;
            }

            $simpan=$pegawai->save();

            if($simpan){
                $idpegawai=$pegawai->id;
                $status=new \App\Statuspegawai;
                $status->pegawai_id=$idpegawai;
                $status->status_id=$request->input('status');
                $status->pangkat_id=$request->input('pangkat');
                $status->jabatan_id=$request->input('jabatan');
                $status->tgl_masuk=date('Y-m-d',strtotime($request->input('tanggalmasuk')));
                $status->digaji_menurut=$request->input('gajimenurut');
                $status->gaji_pokok=$request->input('gaji');
                $status->active='Y';
                $status->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil disimpan',
                    'error'=>''
                );
            }else{
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil disimpan',
                    'error'=>''
                );
            }
        }

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pegawai=Pegawai::with(
                [
                    'status'=>function($q){
                        $q->where('active','Y');
                    },
                    'status.pangkat',
                    'status.kepegawaian'
                ]
            )->find($id);

        return view('dashboard.pegawai.detail')
            ->with('pegawai',$pegawai);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pegawai=Pegawai::find($id);

        return view('dashboard.pegawai.edit')
            ->with('pegawai',$pegawai);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validasi=\Validator::make($request->all(),Pegawai::$rules,Pegawai::$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $pegawai=Pegawai::find($id);
            $pegawai->nip=$request->input('nip');
            $pegawai->tmk=$request->input('tmk');
            $pegawai->nama_lengkap=$request->input('nama');
            $pegawai->tanggal_lahir=date('Y-m-d',strtotime($request->input('tanggal')));
            $pegawai->tempat_lahir=$request->input('tempat');
            $pegawai->agama=$request->input('agama');
            $pegawai->alamat=$request->input('alamat');
            $pegawai->active='Y';

            if($request->hasFile('file')){
                if (!is_dir('uploads/pegawai/')) {
                    mkdir('uploads/pegawai/', 0777, TRUE);
                }

                $file=$request->file('file');
                $filename=str_random(5).'-'.$file->getClientOriginalName();
                $destinationPath='uploads/pegawai/';
                $file->move($destinationPath,$filename);
                $pegawai->foto=$filename;
            }

            $simpan=$pegawai->save();

            if($simpan){
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil disimpan',
                    'error'=>''
                );
            }else{
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil disimpan',
                    'error'=>''
                );
            }
        }

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pegawai  $pegawai
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pegawai=Pegawai::find($id);
        
        if($pegawai->delete()){
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
}
