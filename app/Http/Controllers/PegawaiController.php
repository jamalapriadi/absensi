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
                    'pangkat'=>function($q){
                        $q->where('active','Y');
                    },
                    'jabatan'=>function($q){
                        $q->where('active','Y');
                    },
                    'atasan'
                ]
            )
            ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nip','tmk','nama_lengkap',
                'tempat_lahir','tanggal_lahir','agama','alamat','active','foto','atasan_langsung');
            
            if($request->has('status')){
                $pegawai=$pegawai->where('status_id',$request->input('status'));
            }

            if($request->has('pangkat')){
                $pangkat=$request->input('pangkat');

                $pegawai=$pegawai->whereHas('pangkat',function($q) use($pangkat){
                    $q->where('pangkat_id',$pangkat);
                });
            }

            if($request->has('jabatan')){
                $jabatan=$request->input('jabatan');

                $pegawai=$pegawai->whereHas('jabatan',function($q) use($jabatan){
                    $q->where('jabatan_id',$jabatan);
                });
            }

            return $dataTables->eloquent($pegawai)   
                ->addColumn('pangkats',function($q){
                    $html="";
                    foreach($q->pangkat as $row){
                        $html.=$row->nama_pangkat;
                    }

                    return $html;
                })
                ->addColumn('jabatans',function($q){
                    $html="";
                    foreach($q->jabatan as $row){
                        $html.=$row->nama_jabatan;
                    }

                    return $html;
                })
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='".\URL::to('home/tugas-pegawai/'.$row->id)."' class='btn btn-info btn-sm' title='Tugas Jabatan' kode='".$row->id."'>
                            <i class='fa fa-history'></i>
                            </a>";
                            
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

        $status=\App\Status::select('id','nama_status')->get();
        $pangkat=\App\Pangkat::select('id','nama_pangkat','ruang')->get();
        $jabatan=\App\Jabatan::select('id','nama_jabatan')->get();

        return view('dashboard.pegawai.index')
            ->with('status',$status)
            ->with('pangkat',$pangkat)
            ->with('jabatan',$jabatan)
            ->with('home','Dashboard')
            ->with('title','Pegawai');
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
        $pegawai=\App\Pegawai::select('id','nama_lengkap')->get();

        return view('dashboard.pegawai.create')
            ->with('status',$status)
            ->with('pangkat',$pangkat)
            ->with('jabatan',$jabatan)
            ->with('atasan',$pegawai)
            ->with('home','Dashboard')
            ->with('title','Add New Pegawai');
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
            $filename="";

            $pegawai=new Pegawai;
            $pegawai->nip=$request->input('nip');
            $pegawai->tmk=$request->input('tmk');
            $pegawai->nama_lengkap=$request->input('nama');
            $pegawai->tanggal_lahir=date('Y-m-d',strtotime($request->input('tanggal')));
            $pegawai->tempat_lahir=$request->input('tempat');
            $pegawai->agama=$request->input('agama');
            $pegawai->alamat=$request->input('alamat');
            $pegawai->status_id=$request->input('status');
            $pegawai->active='Y';

            if($request->has('atasan')){
                $pegawai->atasan_langsung=$request->input('atasan');
            }

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
                $user=new \App\User;
                $user->name=$request->input('nama');
                $user->email=$request->input('email');
                $user->password=bcrypt($request->input('password'));
                $user->level=$request->input('level');
                $user->foto=$filename;
                $simpanuser=$user->save();

                if($simpanuser){
                    \DB::table('user_pegawai')
                        ->insert(
                            [
                                'user_id'=>$user->id,
                                'pegawai_id'=>$pegawai->id
                            ]
                        );
                }

                if($request->has('pangkat')){
                    $pangkat=new \App\Pangkatpegawai;
                    $pangkat->pegawai_id=$pegawai->id;
                    $pangkat->pangkat_id=$request->input('pangkat');
                    $pangkat->tmt=date('Y-m-d',strtotime($request->input('tmtpangkat')));
                    $pangkat->active='Y';
                    $pangkat->save();
                }

                if($request->has('jabatan')){
                    $jabatan=new \App\Jabatanpegawai;
                    $jabatan->pegawai_id=$pegawai->id;
                    $jabatan->jabatan_id=$request->input('jabatan');
                    $jabatan->tmt=date('Y-m-d',strtotime($request->input('tmtjabatan')));
                    $jabatan->active='Y';
                    $jabatan->save();
                }

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
                    'status.kepegawaian',
                    'atasan'
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
        $pegawai=Pegawai::with(
                [
                    'pangkat'=>function($q){
                        $q->where('active','Y');
                    },
                    'jabatan'=>function($q){
                        $q->where('active','Y');
                    }
                ]
            )->find($id);
        $status=\App\Status::select('id','nama_status')->get();
        $pangkat=\App\Pangkat::select('id','nama_pangkat','ruang')->get();
        $jabatan=\App\Jabatan::select('id','nama_jabatan')->get();
        $atasan=\App\Pegawai::select('id','nama_lengkap')->get();

        return view('dashboard.pegawai.edit')
            ->with('pegawai',$pegawai)
            ->with('status',$status)
            ->with('pangkat',$pangkat)
            ->with('jabatan',$jabatan)
            ->with('atasan',$atasan)
            ->with('home','Edit Pegawai')
            ->with('title',$pegawai->nama_lengkap);
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
            $pegawai->status_id=$request->input('status');
            $pegawai->active='Y';

            if($request->has('atasan')){
                $pegawai->atasan_langsung=$request->input('atasan');
            }

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
                if($request->has('pangkat')){
                    $pangkat=\App\Pangkatpegawai::where('pegawai_id',$id)
                        ->where('active','Y')
                        ->update(
                            [
                                'pangkat_id'=>$request->input('pangkat'),
                                'tmt'=>date('Y-m-d',strtotime($request->input('tmtpangkat')))
                            ]
                        );
                }

                if($request->has('jabatan')){
                    $jabatan=\App\Jabatanpegawai::where('pegawai_id',$id)
                        ->where('active','Y')
                        ->update(
                            [
                                'jabatan_id'=>$request->input('jabatan'),
                                'tmt'=>date('Y-m-d',strtotime($request->input('tmtjabatan')))
                            ]
                        );
                }

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
        $pegawai=Pegawai::with('user')->find($id);
        
        if($pegawai->delete()){
            \DB::table('user_pegawai')
                ->where(
                    [
                        'pegawai_id'=>$pegawai->id
                    ]
                )->delete();

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

    public function list_pegawai(Request $request){
        $pegawai=Pegawai::select('id','nama_lengkap as text')
            ->where('active',1);

        
        if($request->has('q')){
            $pegawai=$pegawai->where('nama_lengkap','like','%'.$request->input('q').'%');
        }

        if($request->has('page_limit')){
            $pegawai=$pegawai->paginate($request->input('page_limit'));
        }else{
            $pegawai=$pegawai->paginate(50);
        }

        return $pegawai;
    }

    public function tugas($id){
        $pegawai=Pegawai::find($id);

        return view('dashboard.pegawai.detail')
            ->with('pegawai',$pegawai)
            ->with('home','Dashboard')
            ->with('title','Tugas Pegawai');
    }
}
