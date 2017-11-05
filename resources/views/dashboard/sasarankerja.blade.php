@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Sasaran Kerja</h6>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" id="addSasaran">
                <i class="icon-add"></i> Add New
            </a>

            <table class="table table-striped datatable-colvis-basic-sasaran"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <style>
        .daterangepicker{z-index:1151 !important;}
    </style>
    {{Html::script('limitless1/assets/js/plugins/ui/moment/moment.min.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/daterangepicker.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/anytime.min.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.date.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/picker.time.js')}}
    {{Html::script('limitless1/assets/js/plugins/pickers/pickadate/legacy.js')}}
    <script>
        $(function(){
            var idsasaran="";

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

            function showSasaran(){
                $('.datatable-colvis-basic-sasaran').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/sasaran-kerja')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'nama_sasaran', name: 'nama_sasaran',title:'Sasaran Kerja'},
                        {data: 'start_periode', name: 'start_periode',title:'Start Periode'},
                        {data: 'end_periode', name: 'end_periode',title:'End Periode'},
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

            $(document).on("click","#addSasaran",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Sasaran Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formSasaran">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanSasaran"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Sasaran Kerja</label>'+
                                        '<input class="form-control" name="nama" placeholder="Sasaran Kerja" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Start periode</label>'+
                                        '<div class="input-group">'+
											'<span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
											'<input type="text" class="form-control daterange-single" name="start" required>'+
										'</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">End periode</label>'+
                                        '<div class="input-group">'+
											'<span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
											'<input type="text" class="form-control daterange-single" name="end" required>'+
										'</div>'+
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
                // Single picker
                $('.daterange-single').daterangepicker({ 
                    singleDatePicker: true,
                    dateFormat: 'dd/mm/yyyy'
                });
            })

            $(document).on("submit","#formSasaran",function(e){
                var data = new FormData(this);
                if($("#formSasaran")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/sasaran-kerja')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanSasaran').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanSasaran').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showSasaran();
                            }else{
                                $('#pesanSasaran').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanSasaran').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editsasaran",function(){
                idsasaran=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/sasaran-kerja')}}/"+idsasaran,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Add New Perilaku</h6>'+
                                    '</div>'+

                                    '<div id="divFormSasaran"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormSasaran').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateSasaran' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanSasaran"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Sasaran Kerja</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Sasaran Kerja" value="'+result.nama_sasaran+'" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Start periode</label>'+
                                        '<div class="input-group">'+
											'<span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
											'<input type="text" class="form-control daterange-single1" name="start" required>'+
										'</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">End periode</label>'+
                                        '<div class="input-group">'+
											'<span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
											'<input type="text" class="form-control daterange-single2" name="end" required>'+
										'</div>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormSasaran').empty().html(el);

                        $('.daterange-single1').daterangepicker({ 
                            singleDatePicker: true,
                            dateFormat: 'dd/mm/yyyy'
                        }).on("show", function() {
                            $(this).val(result.start_periode).datepicker('update');
                        });

                        $('.daterange-single2').daterangepicker({ 
                            singleDatePicker: true,
                            dateFormat: 'dd/mm/yyyy'
                        }).on("show", function() {
                            $(this).val(result.end_periode).datepicker('update');
                        });
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateSasaran",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateSasaran")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/sasaran-kerja')}}/"+idsasaran,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanSasaran').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanSasaran').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showSasaran();
                            }else{
                                $('#pesanSasaran').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanSasaran').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapussasaran",function(){
                idsasaran=$(this).attr("kode");

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
                            url:"{{URL::to('home/sasaran-kerja')}}/"+idsasaran,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showSasaran();
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

            showSasaran();
        })
    </script>
@stop