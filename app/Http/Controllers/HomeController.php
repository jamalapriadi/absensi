<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->session()->has('sasarankerja')) {
            $value = $request->session()->get('sasarankerja');
            return view('home')
                ->with('home','Dashboard')
                ->with('title','Home');
        }else{
            $sasaran=\App\Sasarankerja::all();
            return view('sasaran')
                ->with('sasaran',$sasaran);
        }
    }

    public function setting(){
        return view('setting')
            ->with('home','Dashboard')
            ->with('title','Setting');
    }

    public function users(){
        return view('user.index')
            ->with('home','Dashboard')
            ->with('title','User');
    }

    public function master(){
        return view('dashboard.master')
            ->with('home','Dashboard')
            ->with('title','Master Data');
    }

    public function save_session_sasaran(Request $request){
        $rules=['sasaran'=>'required'];
        $pesan=['sasaran.required'=>'Sasaran harus diisi'];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $request->session()->put('sasarankerja', $request->input('sasaran'));
            $data=array(
                'success'=>true,
                'pesan'=>'Data Valid',
                'error'=>''
            );
        }

        return $data;
    }
}
