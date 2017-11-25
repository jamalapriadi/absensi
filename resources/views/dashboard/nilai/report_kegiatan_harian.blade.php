@extends('layouts.limitless')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title">Laporan kegiatan Harian</h6>
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
                @else 
                    @if(count($bawahan)>0)
                        <div class="form-group">
                            <label class="control-label">Pegawai</label>
                            <select name="pegawai" class="form-control" id="pegawai">
                                <option value="">--Pilih Pegawai</option>
                                @foreach($bawahan as $row)
                                    <option value="{{$row->id}}">{{$row->nama_lengkap}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endif
                <div class="form-group">
                    <label class="control-label">Dari Tanggal</label>
                    <input class="form-control pickadate-year" type="text" id="dari" name="dari" required>
                </div>

                <div class="form-group">
                    <label class="control-label">Sampai Tanggal</label>
                    <input class="form-control pickadate-year" type="text" name="sampai" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
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
                        url         : "{{URL::to('home/data/kegiatan-harian-preview')}}",
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
                                var url="{{URL::to('home/data/export-kegiatan-harian')}}?dari="+data.dari+"&sampai="+data.sampai+"&pegawai="+data.pegawai;

                                el+="<a href='"+url+"' class='btn btn-primary'><i class='icon-file-excel'></i> Export Excel</a>";

                                el+="<table class='table table-striped' id='data'>"+
                                    "<thead>"+
                                        "<tr>"+
                                            "<th>No.</th>"+
                                            "<th>Type Kegiatan</th>"+
                                            "<th>Tanggal</th>"+
                                            "<th>Dari Jam</th>"+
                                            "<th>Sampai Jam</th>"+
                                            "<th>Kegiatan</th>"+
                                            "<th>Hasil</th>"+
                                            "<th>Keterangan</th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody>";
                                        var no=0;
                                        $.each(data.nilai,function(a,b){
                                            no++;
                                            el+="<tr>"+
                                                "<td>"+no+"</td>"+
                                                "<td>"+b.type_kegiatan+"</td>"+
                                                "<td>"+b.tanggal+"</td>"+
                                                "<td>"+b.dari_jam+"</td>"+
                                                "<td>"+b.sampai_jam+"</td>"+
                                                "<td>"+b.kegiatan+"</td>"+
                                                "<td>"+b.hasil+"</td>"+
                                                "<td>"+b.keterangan+"</td>"+
                                            "</tr>";
                                        })
                                    el+"</tbody>"+
                                "</table>";

                                $("#pesan").empty().html(el);
                                $("#data").DataTable();
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