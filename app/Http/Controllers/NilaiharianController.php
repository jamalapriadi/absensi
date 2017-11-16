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
                ->rawColumns(['action'])
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
        return view('dashboard.nilai.report_kegiatan_harian')
            ->with('title','Report Kegiatan Harian')
            ->with('home','Dashboard');
    }

    public function report_kegiatan_harian_preview(Request $request){
        $rules=[
            'tanggal'=>'required'
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
                'error'=>$validasi->errors()->all()
            );
        }else{
            $start=date('Y-m-d',strtotime($request->input('dari')));
            $end=$request->input('sampai');

            $nilai=\App\Nilaiharian::whereBetween('tanggal',[$start,$end]);

            if(\Auth::user()->level=="pegawai"){
                $user=\App\User::with('pegawai')->find(\Auth::user()->id);
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }

            $nilai=$nilai->get();

            $data=array(
                'success'=>true,
                'pesan'=>"Data Berhasil diload",
                'error'=>''
            );
        }

        return $data;
    }

    public function report_nilai_skp(Request $request){
        $sasaran=\App\Sasarankerja::all();

        return view('dashboard.nilai.report_nilai_skp')
            ->with('title','Report Nilai SKP')
            ->with('home','Dashboard')
            ->with('sasaran',$sasaran);
    }
}
