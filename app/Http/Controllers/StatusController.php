<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Status;
use Yajra\DataTables\DataTables;
use DB;

class StatusController extends Controller
{
    public function index(Request $request, DataTables $dataTables){
        \DB::statement(\DB::raw('set @rownum=0'));

    	$status=Status::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','nama_status');
        
        return $dataTables->eloquent($status)   
            ->addColumn('action',function($row){
                $html="<div class='btn group'>";
                    $html.="<a href='#' class='btn btn-warning btn-sm editstatus' title='Edit' kode='".$row->id."'>
                        <i class='fa fa-edit'></i>
                        </a>";
                    $html.="<a href='#' class='btn btn-danger btn-sm hapusstatus' title='Hapus' kode='".$row->id."'>
                        <i class='fa fa-trash'></i>
                        </a>";
                $html.="</div>";
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);	
    }

    public function store(Request $request){
        $validasi=\Validator::make($request->all(),Status::$rules,Status::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $status=new Status;
            $status->nama_status=$request->input('nama');
            $status->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }
        
        return $data;
    }

    public function show(Request $request,$id){
        $status=Status::find($id);

        return $status;
    }

    public function update(Request $request,$id){
        $validasi=\Validator::make($request->all(),Status::$rules,Status::$pesan);
        
        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $status=Status::find($id);
            $status->nama_status=$request->input('nama');
            $status->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }
        
        return $data;
    }

    public function destroy($id){
        $status=Status::find($id);

        if($status->delete()){
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
