@extends('layouts.limitless')

@section('content')
    <form class="form-horizontal" name="formPegawai" id="formPegawai" onsubmit="return false;" enctype="multipart/form-data">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h6 class="panel-title">Add New Pegawai</h6>
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

        <div class="panel panel-default">
            <div class="panel-body">
                <div id="pesanPegawai"></div>

                <button class="btn btn-primary">
                    <i class="icon-floppy-disk"></i>
                    Simpan
                </button>
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