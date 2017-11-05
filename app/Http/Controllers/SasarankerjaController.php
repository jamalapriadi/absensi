<?php

namespace App\Http\Controllers;

use App\Sasarankerja;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SasarankerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTables $dataTables)
    {
        if($request->ajax()){
            \DB::statement(\DB::raw('set @rownum=0'));

            $sasaran=Sasarankerja::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_sasaran','start_periode','end_periode');
            
            return $dataTables->eloquent($sasaran)   
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='#' class='btn btn-warning btn-sm editsasaran' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapussasaran' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.sasarankerja');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validasi=\Validator::make($request->All(),Sasarankerja::$rules,Sasarankerja::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>true,
                'pesan'=>'Validasi Erorr',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            try{
                $sasaran=new Sasarankerja;
                $sasaran->nama_sasaran=$request->input('nama');
                $sasaran->start_periode=date('Y-m-d',strtotime($request->input('start')));
                $sasaran->end_periode=date('Y-m-d',strtotime($request->input('end')));
                $sasaran->save();
    
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data berhasil disimpan',
                    'error'=>''
                );
            }catch(\Exception $e){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data Gagal disimpan',
                    'error'=>$e->getMessage()
                );
            }
        }

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sasarankerja  $sasarankerja
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sasaran=Sasarankerja::find($id);

        return $sasaran;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sasarankerja  $sasarankerja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validasi=\Validator::make($request->All(),Sasarankerja::$rules,Sasarankerja::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>true,
                'pesan'=>'Validasi Erorr',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            try{
                $sasaran=Sasarankerja::find($id);
                $sasaran->nama_sasaran=$request->input('nama');
                $sasaran->start_periode=date('Y-m-d',strtotime($request->input('start')));
                $sasaran->end_periode=date('Y-m-d',strtotime($request->input('end')));
                $sasaran->save();
    
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data berhasil disimpan',
                    'error'=>''
                );
            }catch(\Exception $e){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data Gagal disimpan',
                    'error'=>$e->getMessage()
                );
            }
        }

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sasarankerja  $sasarankerja
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $sasaran=Sasarankerja::find($id);
            $sasaran->delete();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil dihapus',
                'error'=>''
            );
        }catch(\Exception $e){
            $data=array(
                'success'=>false,
                'pesan'=>'Data Gagal dihapus',
                'error'=>$e->getMessage()
            );
        }

        return $data;
    }
}
