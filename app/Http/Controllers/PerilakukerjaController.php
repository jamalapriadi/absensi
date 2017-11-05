<?php

namespace App\Http\Controllers;

use App\Perilakukerja;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PerilakukerjaController extends Controller
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

            $perilaku=Perilakukerja::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_perilaku','deskripsi');
            
            return $dataTables->eloquent($perilaku)   
                ->addColumn('action',function($row){
                    $html="<div class='btn group'>";
                        $html.="<a href='#' class='btn btn-warning btn-sm editperilaku' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapusperilaku' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);	
        }

        return view('dashboard.perilaku');
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
        $validasi=\Validator::make($request->all(),Perilakukerja::$rules,Perilakukerja::$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $perilaku=new Perilakukerja;
            $perilaku->nama_perilaku=$request->input('nama');
            $perilaku->deskripsi=$request->input('desc');
            $perilaku->save();

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
     * @param  \App\Perilakukerja  $perilakukerja
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $perilaku=Perilakukerja::find($id);

        return $perilaku;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Perilakukerja  $perilakukerja
     * @return \Illuminate\Http\Response
     */
    public function edit(Perilakukerja $perilakukerja)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Perilakukerja  $perilakukerja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validasi=\Validator::make($request->all(),Perilakukerja::$rules,Perilakukerja::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $perilaku=Perilakukerja::find($id);
            $perilaku->nama_perilaku=$request->input('nama');
            $perilaku->deskripsi=$request->input('desc');
            $perilaku->save();

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
     * @param  \App\Perilakukerja  $perilakukerja
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $perilaku=Perilakukerja::find($id);
        
        if($perilaku->delete()){
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
