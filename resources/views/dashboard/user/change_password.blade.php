@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Change Password</h6>
        </div>
        <div class="panel-body">
            <form id="formPassword" name="formPassword" onsubmit="return false">
                <div id="pesanPassword"></div>

                <div class="form-group">
                    <label class="control-label">Password Sekarang</label>
                    <input type="password" class="form-control" name="current"/>
                </div>

                <div class="form-group">
                    <label class="control-label">Password Baru</label>
                    <input type="password" class="form-control" name="password" />
                </div>

                <div class="form-group">
                    <label class="control-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirmation" />
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function(){
            $(document).on("submit","#formPassword",function(e){
                var data = new FormData(this);
                if($("#formPassword")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/change-password')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanPassword').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanPassword').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                            }else{
                                $('#pesanPassword').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanPassword').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });
        })
    </script>
@stop