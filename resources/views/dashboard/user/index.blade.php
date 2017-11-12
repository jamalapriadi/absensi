@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">User</h6>
        </div>
        <div class="panel-body">
            @if(\Auth::user()->level=="admin")
                <a class="btn btn-primary" id="addUser">
                    <i class="icon-add"></i> Add New
                </a>
            @endif
            <table class="table table-striped datatable-colvis-basic-user"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var iduser="";

            // Setting datatable defaults
            $.extend( $.fn.dataTable.defaults, {
                autoWidth: false,
                columnDefs: [{ 
                    orderable: false,
                    width: '100px',
                    targets: [ 2 ]
                }],
                dom: '<"datatable-header"fCl><"datatable-scroll"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filter:</span> _INPUT_',
                    lengthMenu: '<span>Show:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
                },
                drawCallback: function () {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
                    $.uniform.update();
                },
                preDrawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                }
            });

            function showUser(){
                $('.datatable-colvis-basic-user').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/users')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'name', name: 'name',title:'Nama'},
                        {data: 'email', name: 'email',title:'Email'},
                        {data: 'level', name: 'level',title:'Level'},
                        {data: 'action', name: 'action',title:'',searchable:false,width:'17%'}
                    ],
                    buttons: [
                        'copy', 'excel', 'pdf'
                    ],
                    colVis: {
                        buttonText: "<i class='icon-three-bars'></i> <span class='caret'></span>",
                        align: "right",
                        overlayFade: 200,
                        showAll: "Show all",
                        showNone: "Hide all"
                    },
                    bDestroy: true
                }); 

                // Launch Uniform styling for checkboxes
                $('.ColVis_Button').addClass('btn btn-primary btn-icon').on('click mouseover', function() {
                    $('.ColVis_collection input').uniform();
                });


                // Add placeholder to the datatable filter option
                $('.dataTables_filter input[type=search]').attr('placeholder', 'Type to filter...');


                // Enable Select2 select for the length option
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: "-1"
                }); 
            } 

            $(document).on("click","#addUser",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New User</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formUser" enctype="multipart/form-data">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanUser"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama User" required>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Email</label>'+
                                        '<input type="email" class="form-control" name="email" placeholder="Email User" required>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Password</label>'+
                                        '<input type="password" class="form-control" name="password" placeholder="Password" required>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Level</label>'+
                                        '<select name="level" id="level" class="form-control">'+
                                            '<option value="">--Pilih Level--</option>'+
                                            '<option value="pegawai">Pegawai</option>'+
                                            '<option value="admin">Admin</option>'+
                                        '</select>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Foto</label>'+
                                        '<input type="file" class="form-control" name="file" placeholder="Password">'+
                                    '</div>'+
                                '</div>'+

                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            '</form>'+
                        '</div>'+
                    '</div>'+
                '</div>';

                $("#divModal").empty().html(el);
                $("#modalHistory").modal('show');
            })

            $(document).on("submit","#formUser",function(e){
                var data = new FormData(this);
                if($("#formUser")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/users')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanUser').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanUser').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showUser();
                            }else{
                                $('#pesanUser').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanUser').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.edituser",function(){
                iduser=$(this).attr("kode");
                var path="{{URL::asset('uploads/pegawai')}}";
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/users')}}/"+iduser+"/edit",
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit User</h6>'+
                                    '</div>'+

                                    '<div id="divFormUser"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormUser').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateUser' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanUser"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama User" value="'+result.name+'" required>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Email</label>'+
                                        '<input type="email" class="form-control" name="email" value="'+result.email+'" placeholder="Email User" required>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Level</label>'+
                                        '<select name="level" id="level" class="form-control">'+
                                            '<option value="">--Pilih Level--</option>'+
                                            '<option value="pegawai">Pegawai</option>'+
                                            '<option value="admin">Admin</option>'+
                                        '</select>'+
                                    '</div>'+

                                    '<div class="form-group">'+
                                        '<label class="control-label">Foto</label>'+
                                        '<img class="img-responsive" src="'+path+'/'+result.foto+'" style="width:120px;"><br>'+
                                        '<input type="file" class="form-control" name="file" placeholder="Password">'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormUser').empty().html(el);
                        $("#level").val(result.level);
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateUser",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateUser")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/users')}}/"+iduser,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanUser').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanUser').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showUser();
                            }else{
                                $('#pesanUser').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanUser').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapususer",function(){
                iduser=$(this).attr("kode");

                swal({
                    title: "Are you sure?",
                    text: "You will delete data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        $.ajax({
                            url:"{{URL::to('home/users')}}/"+iduser,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showUser();
                                }else{
                                    swal("Error!", result.pesan, "error");
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "Your data is safe :)", "error");
                    }
                });
            })

            showUser();
        })
    </script>
@stop