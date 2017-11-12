<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
            $sasaran=\App\Sasarankerja::find($value);
            $pegawai=\App\Pegawai::all();
            $skp=\App\Nilaiskp::where('sasaran_kerja_id',$value)->get();
            $pengukuran=\App\Tugasjabatan::where('sasaran_kerja_id',$value)
                ->leftJoin('tugas_jabatan_target as b','b.tugas_jabatan_id','=','tugas_jabatan.id')
                ->where('b.type','realisasi')
                ->get();

            return view('home')
                ->with('home','Dashboard')
                ->with('title','Home')
                ->with('sasaran',$sasaran)
                ->with('pegawai',$pegawai)
                ->with('skp',$skp)
                ->with('pengukuran',$pengukuran);
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

    public function report(Request $request,DataTables $dataTables){
        if($request->ajax()){
            $sasaran = $request->session()->get('sasarankerja');

            \DB::statement(\DB::raw('set @rownum=0'));

            $nilai=\App\Nilaiskp::where('sasaran_kerja_id',$sasaran)
                ->with(
                    [
                        'pegawai',
                        'penilai',
                        'atasan'
                    ]
                )
                ->select(\DB::raw('@rownum  := @rownum  + 1 AS no'),
                    'id',
                    'tgl_penilaian',
                    'pegawai_id',
                    'pejabat_penilai',
                    'atasan_pejabat_penilai'
                );

            return $dataTables->eloquent($nilai)   
                ->addColumn('action',function($row){
                        $html="<div class='btn group'>";
                            $html.="<a href='".\URL::to('home/'.$row->id.'/preview')."' class='btn btn-info btn-sm' title='Edit' kode='".$row->id."' title='Preview'>
                                <i class='icon-search4'></i>
                                </a>";

                            $html.="<a href='#' class='btn btn-warning btn-sm' title='Export Excel' kode='".$row->id."'>
                                <i class='icon-file-excel'></i>
                                </a>";
                            $html.="<a href='#' class='btn btn-danger btn-sm' title='Export PDF' kode='".$row->id."'>
                                <i class='icon-file-pdf'></i>
                                </a>";
                        $html.="</div>";
                        return $html;
                    })
                    ->rawColumns(['action'])
                ->make(true);
        }
        return view('dashboard.report.index')
            ->with('home','Dashboard')
            ->with('title','Report');
    }
}
