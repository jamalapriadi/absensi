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
            $user=\App\User::with('pegawai')->find(\Auth::user()->id);

            $value = $request->session()->get('sasarankerja');
            $sasaran=\App\Sasarankerja::find($value);
            $pegawai=\App\Pegawai::all();
            $skp=\App\Nilaiskp::where('sasaran_kerja_id',$value)->get();
            
            $pengukuran=\App\Tugasjabatan::where('sasaran_kerja_id',$value)
                ->leftJoin('tugas_jabatan_target as b','b.tugas_jabatan_id','=','tugas_jabatan.id')
                ->where('b.type','realisasi')
                ->get();
            
            $nilai=\App\Nilaiharian::select(\DB::raw('@rownum  := @rownum  + 1 AS no'),'id','type_kegiatan',
                'pegawai_id','type_kegiatan','tanggal','dari_jam','sampai_jam','kegiatan',
                'hasil','keterangan')
                ->with('pegawai');

            if(\Auth::user()->level=="pegawai"){
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }

            $bawahan=\App\Pegawai::where('atasan_langsung',$user->pegawai[0]->id)
                ->with(
                    [
                        'harian'=>function($q){
                            $q->whereNull('approved');
                        }
                    ]
                )->get();

            $nilai=$nilai->paginate(20);

            return view('home')
                ->with('home','Dashboard')
                ->with('title','Home')
                ->with('sasaran',$sasaran)
                ->with('pegawai',$pegawai)
                ->with('skp',$skp)
                ->with('pengukuran',$pengukuran)
                ->with('nilai',$nilai)
                ->with('bawahan',$bawahan);
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
            
            if(\Auth::user()->level=="pegawai"){
                $user=\App\User::with('pegawai','pegawai.atasan','pegawai.jabatan')->find(\Auth::user()->id);
                $nilai=$nilai->where('pegawai_id',$user->pegawai[0]->id);
            }

            return $dataTables->eloquent($nilai)   
                ->addColumn('action',function($row){
                        $html="<div class='btn group'>";
                            $html.="<a href='".\URL::to('home/'.$row->id.'/preview')."' class='btn btn-info btn-sm' title='Edit' kode='".$row->id."' title='Preview'>
                                <i class='icon-search4'></i>
                                </a>";

                            $html.="<a href='".\URL::to('home/'.$row->id.'/export-xls')."' class='btn btn-warning btn-sm' title='Export Excel' kode='".$row->id."'>
                                <i class='icon-file-excel'></i>
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

    public function change_password(){
        return view('dashboard.user.change_password')
            ->with('home','Dashboard')
            ->with('title','Change Password');
    }

    public function profile(){
        $user=\App\User::with(
            [
                'pegawai',
                'pegawai.atasan',
                'pegawai.jabatan',
                'pegawai.status'=>function($q){
                        $q->where('active','Y');
                    },
                'pegawai.status.pangkat',
                'pegawai.status.kepegawaian',
                'pegawai.atasan'
            ]
        )->find(\Auth::user()->id);
            
        return view('dashboard.user.profile')
            ->with('home','Dashboard')
            ->with('title','Profile')
            ->with('user',$user);
    }

    public function report_harian_belum_konfirmasi(Request $request,$id){
        $bawahan=\App\Pegawai::with(
                    [
                        'harian'=>function($q){
                            $q->whereNull('approved');
                        }
                    ]
                )->find($id);

        return view('dashboard.pegawai.harian_belum_dikonfirmasi')
            ->with('home','Dashboard')
            ->with('title','Kegiatan Harian')
            ->with('pegawai',$bawahan);
    }

    public function approve_kegiatan(Request $request){
        if($request->ajax()){
            $rules=['kegiatan'=>'required'];

            $pesan=['kegiatan.required'=>'Kegiatan harus diisi'];

            $validasi=\Validator::make($request->all(),$rules,$pesan);

            if($validasi->fails()){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Validasi Error',
                    'error'=>$validasi->errors()->all()
                );
            }else{
                $nilai=\App\Nilaiharian::find($request->input('kegiatan'));

                $nilai->approved='Y';
                $nilai->approved_by=\Auth::user()->id;
                $nilai->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data Berhasil diupdate',
                    'error'=>''
                );
            }

            return $data;
        }
    }
}
