@extends('layouts.limitless')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h6 class="panel-title">Kegiatan</h6>
                </div>

                <div class="panel-body">
                    <div id="pesan"></div>

                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Kegiatan</th>
                                <th>Hasil</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0;?>
                            @foreach($pegawai->harian as $row)
                                <?php $no++;?>
                                <tr>
                                    <td>{{$no}}</td>
                                    <td>{{$row->tanggal}}</td>
                                    <td>{{$row->dari_jam}} s/d {{$row->sampai_jam}}</td>
                                    <td>{{$row->kegiatan}}</td>
                                    <td>{{$row->hasil}}</td>
                                    <td>{{$row->keterangan}}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info approve" kode="{{$row->id}}">
                                            <i class="icon-user-check"></i> Approve
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- User thumbnail -->
            <div class="thumbnail">
                <div class="thumb thumb-rounded thumb-slide">
                    {{Html::image('uploads/pegawai/'.$pegawai->foto)}}
                    <div class="caption">
                        <span>
                            <a href="#" class="btn bg-success-400 btn-icon btn-xs" data-popup="lightbox"><i class="icon-plus2"></i></a>
                            <a href="user_pages_profile.html" class="btn bg-success-400 btn-icon btn-xs"><i class="icon-link"></i></a>
                        </span>
                    </div>
                </div>
            
                <div class="caption text-center">
                    <h6 class="text-semibold no-margin">{{$pegawai->nama_lengkap}} <small class="display-block">{{$pegawai->jabatan[0]->nama_jabatan}}</small></h6>
                </div>
            </div>
            <!-- /user thumbnail -->
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function(){
            $(document).on("click","a.approve",function(){
                var kode=$(this).attr("kode");

                $.ajax({
                    url:"{{URL::to('home/data/approve-kegiatan')}}",
                    type:"POST",
                    data:"kegiatan="+kode,
                    beforeSend:function(){
                        $('#pesan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success : function (data) {
                        console.log(data);

                        if(data.success==true){
                            $('#pesan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                            location.reload();
                        }else{
                            $('#pesan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                        }
                    },
                    error   :function() {  
                        $('#pesan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                    }
                })
            })
        })
    </script>
@stop