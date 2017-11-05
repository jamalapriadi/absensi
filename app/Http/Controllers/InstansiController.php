<?php

namespace App\Http\Controllers;

use App\Instansi;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $instansi=Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();
            
            if(count($instansi)>0){
                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil diload',
                    'instansi'=>$instansi
                );
            }else{
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data Gagal diload',
                    'instansi'=>array()
                );
            }

            return $data;
        }
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
        if($request->ajax()){
            $validasi=\Validator::make($request->all(),Instansi::$rules,Instansi::$pesan);

            if($validasi->fails()){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Validasi Gagal',
                    'errors'=>$validasi->errors()->all()
                );
            }else{
                if($request->has('kode')){
                    $instansi=Instansi::find($request->input('kode'));
                }else{
                    $instansi=new Instansi;
                }
                $instansi->nama_instansi=$request->input('nama');
                $instansi->kelas=$request->input('kelas');
                $instansi->alamat=$request->input('alamat');
                $instansi->kode_pos=$request->input('kodepos');
                $instansi->telp=$request->input('telp');
                $instansi->fax=$request->input('fax');
                $instansi->website=$request->input('website');
                $instansi->email=$request->input('email');
                $instansi->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil diupdate',
                    'errors'=>''
                );
            }

            return $data;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Instansi  $instansi
     * @return \Illuminate\Http\Response
     */
    public function show(Instansi $instansi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Instansi  $instansi
     * @return \Illuminate\Http\Response
     */
    public function edit(Instansi $instansi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Instansi  $instansi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $validasi=\Validator::make($request->all(),Instansi::$rules,Instansi::$pesan);

            if($validasi->fails()){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Validasi Gagal',
                    'errors'=>$validasi->errors()->all()
                );
            }else{
                $instansi=Instansi::find($id);
                $instansi->nama_instansi=$request->input('nama');
                $instansi->kelas=$request->input('kelas');
                $instansi->alamat=$request->input('alamat');
                $instansi->kode_pos=$request->input('kodepos');
                $instansi->telp=$request->input('telp');
                $instansi->fax=$request->input('fax');
                $instansi->website=$request->input('website');
                $instansi->email=$request->input('email');
                $instansi->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil diupdate',
                    'errors'=>''
                );
            }

            return $data;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Instansi  $instansi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Instansi $instansi)
    {
        //
    }
}
