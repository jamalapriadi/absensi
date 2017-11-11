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

        return view('dashboard.jabatan.index')
            ->with('home','Dashboard')
            ->with('title','Jabatan');;
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
            ->with('jabatan',$jabatan)
            ->with('home','Dashboard')
            ->with('title','Detail Jabatan');
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

    public function tugas($id,DataTables $dataTables,Request $request){
        $sasaran = $request->session()->get('sasarankerja');
        \DB::statement(\DB::raw('set @rownum=0'));

        $jabatan=\App\Tugasjabatan::where('pegawai_id',$id)
            ->where('sasaran_kerja_id',$sasaran)
            ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','sasaran_kerja_id','pegawai_id','nama_tugas');
            
        
        return $dataTables->eloquent($jabatan)   
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
                    $html.="<a href='".\URL::to('home/jabatan/'.$row->id.'/tugas')."' class='btn btn-info btn-sm' title='Tugas Jabatan' kode='".$row->id."'>
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
            $tugas->pegawai_id=$id;
            $tugas->sasaran_kerja_id=$request->session()->get('sasarankerja');
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

    public function tugas_jabatan($id){
        $tugas=\App\Tugasjabatan::find($id);

        return view('dashboard.jabatan.tugas_jabatan')
            ->with('home','Dashboard')
            ->with('title','Target Jabatan')
            ->with('tugas',$tugas);
    }

    public function target($id,DataTables $dataTables){
        \DB::statement(\DB::raw('set @rownum=0'));

        $target=\App\Targetjabatan::where('tugas_jabatan_id',$id)
            ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','tugas_jabatan_id','kuant','output',
            'kual','waktu','periode_waktu','biaya');
        
        return $dataTables->eloquent($target)   
            ->addColumn('kualitas',function($q){
                return $q->kuant." ".$q->output;
            })
            ->addColumn('mutu',function($q){
                return $q->kual;
            })
            ->addColumn('periode',function($q){
                return $q->waktu." ".$q->periode_waktu;
            })
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
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

    public function tugas_by_id($id){
        $tugas=\App\Tugasjabatan::find($id);

        return $tugas;
    }

    public function update_tugas($id,Request $request){
        $rules=['nama'=>'required'];
        $pesan=['nama.required'=>'Nama harus diisi'];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $tugas=\App\Tugasjabatan::find($id);
            $tugas->nama_tugas=$request->input('nama');
            $tugas->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }

    public function delete_tugas($id){
        $tugas=\App\Tugasjabatan::find($id);

        $hapus=$tugas->delete();

        if($hapus){
            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil dihapus',
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>''
            );
        }

        return $data;
    }

    public function target_store(Request $request){
        $rules=[
            'kuantitas'=>'required',
            'output'=>'required',
            'mutu'=>'required',
            'waktu'=>'required',
            'tugas'=>'required'
        ];

        $pesan=[
            'kuantitas.required'=>'Kuantitas harus diisi',
            'ouput.required'=>'Output harus diisi',
            'mutu.required'=>'Mutu harus diisi',
            'waktu.required'=>'Waktu harus diisi',
            'tugas.required'=>'Tugas harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $target=new \App\Targetjabatan;
            $target->tugas_jabatan_id=$request->input('tugas');
            $target->kuant=$request->input('kuantitas');
            $target->output=$request->input('output');
            $target->kual=$request->input('mutu');
            $target->waktu=$request->input('waktu');
            $target->periode_waktu=$request->input('periode');
            $target->biaya=$request->input('biaya');

            if($request->has('type')){
                $target->type=$request->input('type');
            }else{
                $target->type="target";
            }

            if($request->has('penghitungan')){
                $target->perhitungan=$request->input('penghitungan');
            }

            if($request->has('nilai')){
                $target->nilai_pencapaian=$request->input('nilai');
            }
            
            $target->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    public function target_by_id($id){
        $target=\App\Targetjabatan::find($id);

        return $target;
    }

    public function cek_target($id){
        $target=\App\Targetjabatan::where('tugas_jabatan_id',$id)
            ->get();

        return $target;
    }

    public function hapus_target($id){
        $target=\App\Targetjabatan::find($id);

        $hapus=$target->delete();

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

    public function update_target(Request $request,$id){
        $rules=[
            'kuantitas'=>'required',
            'output'=>'required',
            'mutu'=>'required',
            'waktu'=>'required'
        ];

        $pesan=[
            'kuantitas.required'=>'Kuantitas harus diisi',
            'ouput.required'=>'Output harus diisi',
            'mutu.required'=>'Mutu harus diisi',
            'waktu.required'=>'Waktu harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $target=\App\Targetjabatan::find($id);
            $target->kuant=$request->input('kuantitas');
            $target->output=$request->input('output');
            $target->kual=$request->input('mutu');
            $target->waktu=$request->input('waktu');
            $target->periode_waktu=$request->input('periode');
            $target->biaya=$request->input('biaya');
            
            if($request->has('type')){
                $target->type=$request->input('type');
            }else{
                $target->type="target";
            }

            if($request->has('penghitungan')){
                $target->perhitungan=$request->input('penghitungan');
            }

            if($request->has('nilai')){
                $target->nilai_pencapaian=$request->input('nilai');
            }

            $target->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }
}
