@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Tambah Tugas Jabatan</h6>
        </div>
        <div class="panel-body">
            <div id="pesanTugas"></div>

            <form class="form-horizontal" id="formJabatan" onsubmit="return false;">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Nama Jabatan</label>
                    <input class="form-control" name="nama jabatan" value="{{$jabatan->nama_jabatan}}" readonly>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Tugas Jabatan</label>
                    <input class="form-control" placeholder="Nama Tugas Jabatan" id="tugas" name="tugas" required>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary">
                        <i class="icon-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Daftar Tugas Jabatan</h6>
        </div>
        <div class="panel-body">
            <table class="table table-striped datatable-colvis-basic-jabatan"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var idjabatan="{{$jabatan->id}}";
            var idtugas="";

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

            function showTugas(){
                $('.datatable-colvis-basic-jabatan').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/tugas')}}/"+idjabatan,
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'nama_tugas', name: 'nama_tugas',title:'Nama Tugas'},
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

            $(document).on("submit","#formJabatan",function(e){
                var data = new FormData(this);
                data.append("_method", "PUT");
                if($("#formJabatan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/tugas-jabatan')}}/"+idjabatan,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanTugas').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanTugas').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                $("#tugas").val('');
                                $("#pesanTugas").empty().html('');
                                showTugas();
                            }else{
                                $('#pesanTugas').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanTugas').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.edittugas",function(){
                var el="";
                idtugas=$(this).attr("kode");

                $.ajax({
                    url:"{{URL::to('home/data/tugas')}}/"+idtugas,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Tugas</h6>'+
                                    '</div>'+

                                    '<div id="divFormTugas"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormTugas').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateTugas' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanTugas2"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Tugas</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Tugas" value="'+result.nama_tugas+'" required>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormTugas').empty().html(el);
                    },
                    error:function(){

                    }
                })
            })

            $(document).on("submit","#formUpdateTugas",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateTugas")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/tugas')}}/"+idtugas,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanTugas2').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanTugas2').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showTugas();
                            }else{
                                $('#pesanTugas2').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanTugas2').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapustugas",function(){
                idtugas=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/tugas')}}/"+idtugas,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showTugas();
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
            
            showTugas();
        })
    </script>
@stop