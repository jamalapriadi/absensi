<?php

namespace App\Http\Controllers;

use App\Golongan;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;

class GolonganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTables $dataTables){
        \DB::statement(\DB::raw('set @rownum=0'));

    	$golongan=Golongan::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_golongan');
        
        return $dataTables->eloquent($golongan)   
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
                    $html.="<a href='#' class='btn btn-warning btn-sm editgolongan' title='Edit' kode='".$row->id."'>
                        <i class='fa fa-edit'></i>
                        </a>";
                    $html.="<a href='#' class='btn btn-danger btn-sm hapusgolongan' title='Hapus' kode='".$row->id."'>
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
        $validasi=\Validator::make($request->all(),Golongan::$rules,Golongan::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $golongan=new Golongan;
            $golongan->nama_golongan=$request->input('nama');
            $golongan->save();

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
     * @param  \App\Golongan  $golongan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $golongan=Golongan::find($id);
        
        return $golongan;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Golongan  $golongan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validasi=\Validator::make($request->all(),Golongan::$rules,Golongan::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $golongan=Golongan::find($id);
            $golongan->nama_golongan=$request->input('nama');
            $golongan->save();

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
     * @param  \App\Golongan  $golongan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $golongan=Golongan::find($id);
        
        if($golongan->delete()){
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

    public function list_golongan(Request $request){
        $golongan=Golongan::select('id','nama_golongan as text');
        
        if($request->has('q')){
            $golongan=$golongan->where('nama_golongan','like','%'.$request->input('q').'%');
        }

        if($request->has('page_limit')){
            $pagelimit=$request->input('page_limit');
        }else{
            $pagelimit=10;
        }

        return $golongan->paginate($pagelimit);
    }
}
