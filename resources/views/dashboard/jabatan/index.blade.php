@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Jabatan</h6>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" id="addJabatan">
                <i class="icon-add"></i> Add New
            </a>
            <table class="table table-striped datatable-colvis-basic-jabatan"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var idjabatan="";

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

            function getCkeditor(){
                CKEDITOR.replace('desc', {
                    enterMode:2,forceEnterMode:false,shiftEnterMode:1,
                    toolbar :
                        [
                            [ 'Bold', 'Italic', 'Underline','Paste', 'PasteText', 'PasteFromWord'],
                            //[ 'Paste', 'PasteText', 'PasteFromWord'],
                            [ 'NumberedList', 'BulletedList', '-','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                            [ 'Link', 'Unlink', 'Anchor' ],
                            [ 'Table', 'Image'],
                            [ 'Font', 'FontSize' ],             
                            ['TextColor','BGColor']
                ],        
                toolbarCanCollapse:true
                });
                
            }

            function showJabatan(){
                $('.datatable-colvis-basic-jabatan').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/jabatan')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'nama_jabatan', name: 'nama_jabatan',title:'Nama Jabatan'},
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

            $(document).on("click","#addJabatan",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Perilaku Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formJabatan">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanJabatan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Jabatan</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Jabatan" required>'+
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
                getCkeditor();
            })

            $(document).on("submit","#formJabatan",function(e){
                var data = new FormData(this);
                if($("#formJabatan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/jabatan')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanJabatan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanJabatan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showJabatan();
                            }else{
                                $('#pesanJabatan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanJabatan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editjabatan",function(){
                idjabatan=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/jabatan')}}/"+idjabatan+"/edit",
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Add New Jabatan</h6>'+
                                    '</div>'+

                                    '<div id="divFormJabatan"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormJabatan').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateJabatan' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanJabatan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Jabatan</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Jabatan" value="'+result.nama_jabatan+'" required>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormJabatan').empty().html(el);
                        getCkeditor();
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateJabatan",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateJabatan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/jabatan')}}/"+idjabatan,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanJabatan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanJabatan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showJabatan();
                            }else{
                                $('#pesanJabatan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanJabatan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapusjabatan",function(){
                idjabatan=$(this).attr("kode");

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
                            url:"{{URL::to('home/jabatan')}}/"+idjabatan,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showJabatan();
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

            showJabatan();
        })
    </script>
@stop