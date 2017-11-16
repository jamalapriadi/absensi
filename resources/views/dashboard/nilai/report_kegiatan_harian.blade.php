@extends('layouts.limitless')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title">Laporan kegiatan Harian</h6>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" onsubmit="return false" id="formSkp">
                <div class="form-group">
                    <label class="control-label">Dari Tanggal</label>
                    <input class="form-control" type="date" id="dari" name="dari" required>
                </div>

                <div class="form-group">
                    <label class="control-label">Sampai Tanggal</label>
                    <input class="form-control" type="date" name="sampai" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-folder-search"></i> Tampilkan
                    </button>
                </div>
            </form>
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
                        url         : "{{URL::to('home/report/kegiatan-harian-preview')}}",
                        type        : 'get',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanPerilaku').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanPerilaku').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showPerilaku();
                            }else{
                                $('#pesanPerilaku').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanPerilaku').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });
        })
    </script>
@stop