@extends('layouts.limitless')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title">Laporan Nilai SKP</h6>
        </div>
        <div class="panel-body">
            <form onsubmit="return false" id="formSkp" name="formSkp">
                @if(\Auth::user()->level=="admin")
                    <div class="form-group">
                        <label class="control-label">Pegawai</label>
                        <select name="pegawai" class="form-control" id="pegawai">
                            <option value="">--Pilih Pegawai</option>
                            @foreach($pegawai as $row)
                                <option value="{{$row->id}}">{{$row->nama_lengkap}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="form-group">
                    <label class="control-label">Sasaran Kerja</label>
                    <select class="form-control" name="sasaran" id="sasaran">
                        <option value="">--Pilih Sasaran Kerja--</option>
                        @foreach($sasaran as $row)
                            <option value="{{$row->id}}">{{$row->nama_sasaran}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">
                        <i class="icon-folder-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-flat">
        <div class="panel-body">
            <div id="pesan"></div>
        </div>
    </div>
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
            $('.pickadate-year').pickadate({
                selectYears: true,
                selectMonths: true,
                selectYears: 4
            });

            $(document).on("submit","#formSkp",function(e){
                var data = new FormData(this);
                if($("#formSkp")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/report-skp-preview')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                var el="";
                                el+="<table class='table table-striped'>"+
                                    "<thead>"+
                                        "<tr>"+
                                            "<th>No.</th>"+
                                            "<th>Pegawai</th>"+
                                            "<th>Pejabat Penilai</th>"+
                                            "<th>Atasan Pejabat Penilai</th>"+
                                            "<th></th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody>";
                                        var no=0;
                                        $.each(data.nilai,function(a,b){
                                            no++;
                                            var preview="{{URL::to('home')}}/"+b.id+"/preview";
                                            var cetak="{{URL::to('home')}}/"+b.id+"/export-xls";
                                            
                                            el+="<tr>"+
                                                "<td>"+no+"</td>"+
                                                "<td>"+b.pegawai.nama_lengkap+"</td>"+
                                                "<td>"+b.penilai.nama_lengkap+"</td>"+
                                                "<td>"+b.atasan.nama_lengkap+"</td>"+
                                                "<td>"+
                                                    "<div class='btn group'>"+
                                                        "<a href='"+preview+"' class='btn btn-info btn-sm' title='Edit' title='Preview'>"+
                                                            "<i class='icon-search4'></i>"+
                                                        "</a>"+

                                                        "<a href='"+cetak+"' class='btn btn-warning btn-sm' title='Export Excel'>"+
                                                            "<i class='icon-file-excel'></i>"+
                                                        "</a>"+
                                                    "</div>"+
                                                "</td>"+
                                            "</tr>";
                                        })
                                    el+="</tbody>"+
                                "</table>";
                                $("#pesan").empty().html(el);
                            }else{
                                $('#pesan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });
        })
    </script>
@stop