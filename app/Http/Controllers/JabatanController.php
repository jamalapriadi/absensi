<?php

namespace App\Http\Controllers;

use App\Jabatan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTables $dataTables){
        if($request->ajax()){
            \DB::statement(\DB::raw('set @rownum=0'));

            $jabatan=Jabatan::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_jabatan');
            
            return $dataTables->eloquent($jabatan)   
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='".\URL::to('home/jabatan/'.$row->id)."' class='btn btn-info btn-sm' title='Tugas Jabatan' kode='".$row->id."'>
                            <i class='fa fa-history'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-warning btn-sm editjabatan' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapusjabatan' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);	
        }

        return view('dashboard.jabatan.index');
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
    public function store(Request $request){
        $validasi=\Validator::make($request->all(),Jabatan::$rules,Jabatan::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $jabatan=new Jabatan;
            $jabatan->nama_jabatan=$request->input('nama');
            $jabatan->save();

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
     * @param  \App\Jabatan  $jabatan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jabatan=Jabatan::find($id);

        return view('dashboard.jabatan.detail')
            ->with('jabatan',$jabatan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Jabatan  $jabatan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jabatan=Jabatan::find($id);

        return $jabatan;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jabatan  $jabatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id){
        $validasi=\Validator::make($request->all(),Jabatan::$rules,Jabatan::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $jabatan=Jabatan::find($id);
            $jabatan->nama_jabatan=$request->input('nama');
            $jabatan->save();

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
     * @param  \App\Jabatan  $jabatan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $jabatan=Jabatan::find($id);

        if($jabatan->delete()){
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

    public function tugas($id,DataTables $dataTables){
        \DB::statement(\DB::raw('set @rownum=0'));

        $jabatan=\App\Tugasjabatan::where('jabatan_id',$id)
            ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','sasaran_kerja_id','jabatan_id','nama_tugas');
        
        return $dataTables->eloquent($jabatan)   
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
                    $html.="<a href='".\URL::to('home/tugas-parameter/'.$row->id)."' class='btn btn-info btn-sm' title='Tugas Jabatan' kode='".$row->id."'>
                        <i class='fa fa-history'></i>
                        </a>";
                    $html.="<a href='#' class='btn btn-warning btn-sm edittugas' title='Edit' kode='".$row->id."'>
                        <i class='fa fa-edit'></i>
                        </a>";
                    $html.="<a href='#' class='btn btn-danger btn-sm hapustugas' title='Hapus' kode='".$row->id."'>
                        <i class='fa fa-trash'></i>
                        </a>";
                $html.="</div>";
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);	
    }

    public function add_tugas(Request $request,$id){
        $rules=['tugas'=>'required'];
        $pesan=['tugas.required'=>'Tugas harus diisi'];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $tugas=new \App\Tugasjabatan;
            $tugas->jabatan_id=$id;
            $tugas->nama_tugas=$request->input('tugas');
            $tugas->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }
}
