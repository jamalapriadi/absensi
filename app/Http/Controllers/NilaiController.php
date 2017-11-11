<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Nilaiskp;
use Yajra\DataTables\DataTables;

class NilaiController extends Controller
{
    public function nilai_skp(Request $request){
        return view('dashboard.nilai.skp')
            ->with('home','Dashboard')
            ->with('title','Form SKP');
    }

    public function nilai_skp_store(Request $request){
        $rules=[
            'tanggal'=>'required',
            'pegawai'=>'required',
            'pejabat'=>'required',
            'atasan'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal Harus diisi',
            'pegawai.required'=>'Pegawai Harus diisi',
            'pejabat.required'=>'Pejabat Penilai harus diisi',
            'atasan.required'=>'Atasan Pejabat harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            //cek
            $sasaran = $request->session()->get('sasarankerja');
            $cek=Nilaiskp::where('sasaran_kerja_id',$sasaran)
                ->where('pegawai_id',$request->input('pegawai'))
                ->get();
            
            if(count($cek)>0){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Data Pegawai ini sudah ada',
                    'error'=>''
                );
            }else{
                //simpan baru
                $nilai=new Nilaiskp;
                $nilai->sasaran_kerja_id=$sasaran;
                $nilai->pegawai_id=$request->input('pegawai');
                $nilai->tgl_penilaian=date('Y-m-d',strtotime($request->input('tanggal')));
                $nilai->pejabat_penilai=$request->input('pejabat');
                $nilai->atasan_pejabat_penilai=$request->input('atasan');
                $nilai->save();

                $data=array(
                    'success'=>true,
                    'pesan'=>'Data berhasil disimpan',
                    'error'=>''
                );
            }
        }

        return $data;
    }

    public function nilai_skp_data(Request $request,DataTables $dataTables){
        $sasaran = $request->session()->get('sasarankerja');

        \DB::statement(\DB::raw('set @rownum=0'));

        $nilai=Nilaiskp::where('sasaran_kerja_id',$sasaran)
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
                        $html.="<a href='".\URL::to('home/nilai-skp/'.$row->id.'/report')."' class='btn btn-info btn-sm' title='Edit' kode='".$row->id."'>
                            <i class='icon-stats-bars2'></i>
                            </a>";

                        $html.="<a href='#' class='btn btn-warning btn-sm editnilai' title='Edit' kode='".$row->id."'>
                            <i class='fa fa-edit'></i>
                            </a>";
                        $html.="<a href='#' class='btn btn-danger btn-sm hapusnilai' title='Hapus' kode='".$row->id."'>
                            <i class='fa fa-trash'></i>
                            </a>";
                    $html.="</div>";
                    return $html;
                })
                ->rawColumns(['action'])
            ->make(true);
    }

    public function nilai_skp_detail($id){
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'penilai',
                    'atasan'
                ]
            )->find($id);

        return $nilai;
    }

    public function nilai_skp_update(Request $request,$id){
        $rules=[
            'tanggal'=>'required',
            'pegawai'=>'required',
            'pejabat'=>'required',
            'atasan'=>'required'
        ];

        $pesan=[
            'tanggal.required'=>'Tanggal Harus diisi',
            'pegawai.required'=>'Pegawai Harus diisi',
            'pejabat.required'=>'Pejabat Penilai harus diisi',
            'atasan.required'=>'Atasan Pejabat harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $nilai=Nilaiskp::find($id);
            $nilai->pegawai_id=$request->input('pegawai');
            $nilai->tgl_penilaian=date('Y-m-d',strtotime($request->input('tanggal')));
            $nilai->pejabat_penilai=$request->input('pejabat');
            $nilai->atasan_pejabat_penilai=$request->input('atasan');

            if($request->has('nilaiskp')){
                $nilai->nilai_pencapaian=$request->input('nilaiskp');
            }

            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }

    public function update_skp_nilai(Request $request,$id){
        $rules=[
            'nilaiskp'=>'required',
        ];

        $pesan=[
            'nilaiskp.required'=>'Nilai SKP Harus diisi',
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $nilai=Nilaiskp::find($id);

            if($request->has('nilaiskp')){
                $nilai->nilai_pencapaian=$request->input('nilaiskp');
            }

            $nilai->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diupdate',
                'error'=>''
            );
        }

        return $data;
    }

    public function nilai_skp_delete($id){
        $nilai=Nilaiskp::find($id);

        if($nilai->delete()){
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

    public function nilai_skp_report(Request $request,$id){
        $nilai=Nilaiskp::with(
                [
                    'pegawai',
                    'pegawai.pangkat',
                    'pegawai.jabatan',
                    'penilai',
                    'penilai.pangkat',
                    'penilai.jabatan',
                    'atasan'
                ]
            )->find($id);
        
        $instansi=\App\Instansi::select('id','nama_instansi','kelas','alamat','kode_pos',
            'telp','fax','website','email')
                ->first();

        return view('dashboard.nilai.report')
                    ->with('home','Dashboard')
                    ->with('title','Report SKP')
                    ->with('nilai',$nilai)
                    ->with('instansi',$instansi);
    }

    public function form_skp(Request $request,$id){
        $sasaran = $request->session()->get('sasarankerja');

        $pegawai=\App\Pegawai::with(
            [
                'tugas'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'tugas.target'=>function($q){
                    $q->where('type','target');
                }
            ]
        )
        ->find($id);

        return $pegawai;
    }

    public function form_skp_realisasi(Request $request,$id){
        $sasaran = $request->session()->get('sasarankerja');

        $pegawai=\App\Pegawai::with(
            [
                'tugas'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'tugas.target'=>function($q){
                    $q->where('type','realisasi');
                },
                'nilai'=>function($q) use($sasaran){
                    $q->where('sasaran_kerja_id',$sasaran);
                },
                'nilai.tambahan'=>function($q){
                    $q->orderBy('type');
                },
                'nilai.prestasi'
            ]
        )
        ->find($id);

        return $pegawai;
    }

    public function tugas_tambahan_store(Request $request){
        $rules=[
            'type'=>'required',
            'nama'=>'required',
            'nilai'=>'required'
        ];

        $pesan=[
            'type.required'=>'Type harus diisi',
            'nama.required'=>'Nama Harus diisi',
            'nilai.required'=>'Nilai harus diisi'
        ];

        $validasi=\Validator::make($request->all(),$rules,$pesan);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            if($request->has('kode')){
                $tambahan=\App\Tugastambahan::find($request->input('kode'));
            }else{
                $tambahan=new \App\Tugastambahan;
                $tambahan->nilai_skp_id=$request->input('skp');
            }
            $tambahan->type=$request->input('type');
            $tambahan->nama=$request->input('nama');
            $tambahan->nilai=$request->input('nilai');
            $tambahan->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data Berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    public function tugas_tambahan_delete($id){
        $tambahan=\App\Tugastambahan::find($id);

        $hapus=$tambahan->delete();

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
}
