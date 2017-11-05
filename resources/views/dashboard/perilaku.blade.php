@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Perilaku Kerja</h6>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" id="addPerilaku">
                <i class="icon-add"></i> Add New
            </a>
            <table class="table table-striped datatable-colvis-basic-perilaku"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var idperilaku="";

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

            function showPerilaku(){
                $('.datatable-colvis-basic-perilaku').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/perilaku-kerja')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'nama_perilaku', name: 'nama_perilaku',title:'Perilaku'},
                        {data: 'deskripsi', name: 'deskripsi',title:'Deskripsi'},
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

            $(document).on("click","#addPerilaku",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Perilaku Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formPerilaku">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanPerilaku"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Perilaku Kerja</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Perilaku Kerja" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Deskripsi</label>'+
                                        '<textarea class="form-control" name="deskripsi" id="desc"></textarea>'+
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

            $(document).on("submit","#formPerilaku",function(e){
                var data = new FormData(this);
                var desc = CKEDITOR.instances.desc.getData();
                data.append("desc", desc);
                if($("#formPerilaku")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/perilaku-kerja')}}",
                        type        : 'post',
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

            $(document).on("click","a.editperilaku",function(){
                idperilaku=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/perilaku-kerja')}}/"+idperilaku,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Add New Perilaku</h6>'+
                                    '</div>'+

                                    '<div id="divFormPerilaku"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormPerilaku').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdatePerilaku' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanPerilaku"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Perilaku Kerja</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Perilaku Kerja" value="'+result.nama_perilaku+'" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Deskripsi</label>'+
                                        '<textarea class="form-control" name="deskripsi" id="desc">'+result.deskripsi+'</textarea>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormPerilaku').empty().html(el);
                        getCkeditor();
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdatePerilaku",function(e){
                var data = new FormData(this);
                var desc = CKEDITOR.instances.desc.getData();
                data.append("desc", desc);
                data.append("_method","PUT");
                if($("#formUpdatePerilaku")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/perilaku-kerja')}}/"+idperilaku,
                        type        : 'post',
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

            $(document).on("click","a.hapusperilaku",function(){
                idperilaku=$(this).attr("kode");

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
                            url:"{{URL::to('home/perilaku-kerja')}}/"+idperilaku,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showPerilaku();
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

            showPerilaku();
        })
    </script>
@stop