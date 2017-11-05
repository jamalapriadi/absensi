<?php

namespace App\Http\Controllers;

use App\Pangkat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTables $dataTables){
        \DB::statement(\DB::raw('set @rownum=0'));

        $pangkat=Pangkat::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_pangkat','golongan_id','ruang')
            ->with('golongan');
        
        return $dataTables->eloquent($pangkat)   
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
                    $html.="<a href='#' class='btn btn-warning btn-sm editpangkat' title='Edit' kode='".$row->id."'>
                        <i class='fa fa-edit'></i>
                        </a>";
                    $html.="<a href='#' class='btn btn-danger btn-sm hapuspangkat' title='Hapus' kode='".$row->id."'>
                        <i class='fa fa-trash'></i>
                        </a>";
                $html.="</div>";
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);	
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validasi=\Validator::make($request->all(),Pangkat::$rules,Pangkat::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $pangkat=new Pangkat;
            $pangkat->nama_pangkat=$request->input('nama');
            $pangkat->golongan_id=$request->input('golongan');
            $pangkat->ruang=$request->input('ruang');
            $pangkat->save();

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
     * @param  \App\Pangkat  $pangkat
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pangkat=Pangkat::with('golongan')->find($id);
        
        return $pangkat;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pangkat  $pangkat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validasi=\Validator::make($request->all(),Pangkat::$rules,Pangkat::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $pangkat=Pangkat::find($id);
            $pangkat->nama_pangkat=$request->input('nama');
            $pangkat->golongan_id=$request->input('golongan');
            $pangkat->ruang=$request->input('ruang');
            $pangkat->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil diupdate',
                'error'=>''
            );
        }
        
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pangkat  $pangkat
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pangkat=Pangkat::find($id);
        
        if($pangkat->delete()){
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
