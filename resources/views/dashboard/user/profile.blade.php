@extends('layouts.limitless')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h6 class="panel-title">Profile</h6>
                </div>

                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <td>NIP</td>
                            <td>{{$user->pegawai[0]->nip}}</td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>{{$user->pegawai[0]->nama_lengkap}}</td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>{{$user->pegawai[0]->jabatan[0]->nama_jabatan}}</td>
                        </tr>
                        <tr>
                            <td>Tempat, Tanggal Lahir</td>
                            <td>{{$user->pegawai[0]->tempat_lahir}}, {{date('d F Y',strtotime($user->pegawai[0]->tanggal_lahir))}}</td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>{{$user->pegawai[0]->agama}}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>{{$user->pegawai[0]->alamat}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- User thumbnail -->
            <div class="thumbnail">
                <div class="thumb thumb-rounded thumb-slide">
                    {{Html::image('uploads/pegawai/'.$user->foto)}}
                    <div class="caption">
                        <span>
                            <a href="#" class="btn bg-success-400 btn-icon btn-xs" data-popup="lightbox"><i class="icon-plus2"></i></a>
                            <a href="user_pages_profile.html" class="btn bg-success-400 btn-icon btn-xs"><i class="icon-link"></i></a>
                        </span>
                    </div>
                </div>
            
                <div class="caption text-center">
                    <h6 class="text-semibold no-margin">{{$user->pegawai[0]->nama_lengkap}} <small class="display-block">{{$user->pegawai[0]->jabatan[0]->nama_jabatan}}</small></h6>
                </div>
            </div>
            <!-- /user thumbnail -->
        </div>
    </div>
@stop