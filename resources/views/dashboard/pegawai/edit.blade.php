@extends('layouts.limitless')

@section('content')
    <form class="form-horizontal" name="formPegawai" id="formPegawai" onsubmit="return false;" enctype="multipart/form-data">
        {{--  <div class="panel panel-primary">
            <div class="panel-heading">
                <h6 class="panel-title">User Account</h6>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Email</label>
                    <div class="col-lg-8">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Passoword</label>
                    <div class="col-lg-8">
                        <input type="password" class="form-control" name="passoword" placeholder="Passoword">
                    </div>
                </div>
            </div>
        </div>  --}}

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h6 class="panel-title">Edit Pegawai</h6>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">NIP</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" name="nip" placeholder="NIP" value="{{$pegawai->nip}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">TMK</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" name="tmk" placeholder="TMK" value="{{$pegawai->tmk}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Nama Lengkap</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" value="{{$pegawai->nama_lengkap}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Tempat Lahir</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" name="tempat" placeholder="Tempat Lahir" value="{{$pegawai->tempat_lahir}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Tanggal Lahir</label>
                    <div class="col-lg-8">
                        <input type="date" class="form-control" name="tanggal" placeholder="Tanggal Lahir" value="{{$pegawai->tanggal_lahir}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Agama</label>
                    <div class="col-lg-8">
                        <select name="agama" id="agama" class="form-control">
                            <option value="Islam">Islam</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Alamat</label>
                    <div class="col-lg-8">
                        <textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control">{{$pegawai->alamat}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-lg-2 control-label">Foto</label>
                    <div class="col-lg-8">
                        <div id="tmpPackage">
                            {{Html::image('uploads/pegawai/'.$pegawai->foto,'',array('class'=>'img-responsive','style'=>'width:120px'))}}
                        </div>
                        <input type="file" id="file" name="file" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        @if(count($pegawai->pangkat)>0)
            @foreach($pegawai->pangkat as $p)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Pangkat</h6>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="" class="col-lg-2 control-label">Status Pegawai</label>
                            <div class="col-lg-8">
                                <select name="status" id="status" class="form-control">
                                    <option value="">--Pilih Status--</option>
                                    @foreach($status as $row)
                                        <option value="{{$row->id}}" @if($row->id==$pegawai->status_id) selected='selected' @endif>{{$row->nama_status}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-2 control-label">Pangkat</label>
                            <div class="col-lg-8">
                                <select name="pangkat" id="pangkat" class="form-control">
                                    <option value="">--Pilih Pangkat--</option>
                                    @foreach($pangkat as $row)
                                        <option value="{{$row->id}}" @if($row->id==$p->id) selected='selected' @endif>{{$row->nama_pangkat}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">TMT</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control pickadate-year" name="tmtpangkat" value="{{$p->pivot->tmt}}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @if(count($pegawai->jabatan)>0)
            @foreach($pegawai->jabatan as $j)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Jabatan</h6>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="" class="col-lg-2 control-label">Jabatan</label>
                            <div class="col-lg-8">
                                <select name="jabatan" id="jabatan" class="form-control">
                                    <option value="">--Pilih Jabatan--</option>
                                    @foreach($jabatan as $row)
                                        <option value="{{$row->id}}" @if($j->id==$row->id) selected='selected' @endif>{{$row->nama_jabatan}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-2 control-label">TMT</label>
                            <div class="col-lg-8">
                                <input type="text" name="tmtjabatan" class="form-control pickadate-year" value="{{$j->pivot->tmt}}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                <div id="pesanPegawai"></div>

                <button class="btn btn-primary">
                    <i class="icon-floppy-disk"></i>
                    Simpan
                </button>

                <a href="{{URL::to('home/pegawai')}}" class="btn btn-default">Kembali</a>
            </div>
        </div>
    </form>
@stop

@section('js')
    {{Html::script('limitless1/assets/js/plugins/ui/moment/moment.min.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/daterangepicker.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/anytime.min.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.date.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.time.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/legacy.js')}}
    <script>
        $(function(){
            $('.daterange-single').daterangepicker({ 
                singleDatePicker: true,
                dateFormat: 'dd/mm/yyyy'
            });

            // $(document).on("change","#filepackage",function(objEvent){
            //     var objFormData=new FormData();
            //     var objFile=$(this)[0].files[0];
            //     var tmppath=URL.createObjectURL(objEvent.target.files[0]);

            //     $("#tmpPackage").html("<br><a href='"+tmppath+"' class='btn btn-info btn-sm  filepackage' target='_blank'><i class='fa fa-file'></i> Preview</a> <a class='btn btn-danger btn-sm hapuspackage'>Hapus</a>");
            // })

            // $(document).on("click","a.hapuspackage",function(){
            //     $("#tmpPackage").empty();
            //     $("#filepackage").val('');
            // })

            $(document).on("submit","#formPegawai",function(e){
                var idpegawai="{{$pegawai->id}}";

                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formPegawai")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/pegawai')}}/"+idpegawai,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanPegawai').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanPegawai').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                            }else{
                                $('#pesanPegawai').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanPegawai').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });
        })
    </script>
@stop