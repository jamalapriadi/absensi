<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Yajra\DataTables\DataTables;
use DB;
class UserController extends Controller
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

            $user=User::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','name','email','level','foto');
            
            return $dataTables->eloquent($user)   
                ->addColumn('action',function($row){
                    $disabled="";
                    if(\Auth::user()->level=="admin"){
                        $disabled="";
                    }else{
                        $disabled="disabled";
                    }
                    $html="<div class='btn group'>";
                        $html.="<a href='#' ".$disabled." class='btn btn-warning btn-sm edituser' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' ".$disabled." class='btn btn-danger btn-sm hapususer' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.user.index')
            ->with('home','Dashboard')
            ->with('title','Users');
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
            'nama'=>'required',
            'email'=>'required|unique:users,email',
            'password'=>'required',
            'level'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=new User;
            $user->email=$request->input('email');
            $user->name=$request->input('nama');
            $user->password=bcrypt($request->input('password'));
            $user->level=$request->input('level');

            if($request->hasFile('file')){
                if (!is_dir('uploads/pegawai/')) {
                    mkdir('uploads/pegawai/', 0777, TRUE);
                }

                $file=$request->file('file');
                $filename=str_random(5).'-'.$file->getClientOriginalName();
                $destinationPath='uploads/pegawai/';
                $file->move($destinationPath,$filename);
                $user->foto=$filename;
            }

            $user->save();

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::find($id);

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules=[
            'nama'=>'required',
            'email'=>'required',
            'level'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=User::find($id);
            $user->email=$request->input('email');
            $user->name=$request->input('nama');
            $user->level=$request->input('level');

            if($request->hasFile('file')){
                if (!is_dir('uploads/pegawai/')) {
                    mkdir('uploads/pegawai/', 0777, TRUE);
                }

                $file=$request->file('file');
                $filename=str_random(5).'-'.$file->getClientOriginalName();
                $destinationPath='uploads/pegawai/';
                $file->move($destinationPath,$filename);
                $user->foto=$filename;
            }

            $user->save();

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::find($id);

        if($user->delete()){
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