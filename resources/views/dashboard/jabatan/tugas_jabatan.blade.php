@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Tugas Pegawai</h6>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label">Nama Tugas</label>
                <input class="form-control" name="tugas" value="{{$tugas->nama_tugas}}" readonly>
            </div>

            <a href="{{URL::to('home/tugas-pegawai/'.$tugas->pegawai_id)}}" class="btn btn-default">Kembali</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Daftar Target Tugas Jabatan</h6>
        </div>
        <div class="panel-body">
            <div id="cekTarget"></div>
            
            <table class="table table-striped datatable-colvis-basic-tugas"></table>
        </div>
    </div>

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var idtugas="{{$tugas->id}}";
            var idtarget="";

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
                $('.datatable-colvis-basic-tugas').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/data/target')}}/"+idtugas+"/jabatan",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'kualitas', name: 'kualitas',title:'Kuant / Output'},
                        {data: 'mutu', name: 'mutu',title:'Kual / Mutu'},
                        {data: 'periode', name: 'periode',title:'Waktu'},
                        {data: 'biaya', name: 'biaya',title:'Biaya',defaultContent: "-"},
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

            function cekTarget(){
                $.ajax({
                    url:"{{URL::to('home/data/cek-target')}}/"+idtugas,
                    type:"GET",
                    beforeSend:function(){
                        $("#cekTarget").empty().html('');
                    },
                    success:function(result){
                        if(result.length<1){
                            var el='<a href="#" class="btn btn-primary btn-sm" id="addTarget">'+
                                '<i class="icon-add"></i>'+
                                    'Add Target'+
                                '</a>';
                            $("#cekTarget").empty().html(el);
                        }
                    },
                    error:function(){

                    }
                })
            }

            $(document).on("click","#addTarget",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Target Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formTarget">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Kuantitas</label>'+
                                        '<input class="form-control" name="kuantitas" placeholder="Kuantitas" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Output</label>'+
                                        '<select name="output" id="output" class="form-control">'+
                                            '<option value="">--Pilih Output--</option>'+
                                            '<option value="Keg">Kegiatan</option>'+
                                            '<option value="Berkas">Berkas</option>'+
                                            '<option value="Daftar">Daftar</option>'+
                                            '<option value="Dok">Dokumen</option>'+
                                            '<option value="Lap">Laporam</option>'+
                                        '</select>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Kualitas / Mutu</label>'+
                                        '<input class="form-control" name="mutu" placeholder="Kualitas / Mutu" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Waktu</label>'+
                                        '<input class="form-control" name="waktu" placeholder="Waktu" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Periode</label>'+
                                        '<select name="periode" id="periode" class="form-control">'+
                                            '<option value="">--Pilih Periode--</option>'+
                                            '<option value="Hari">Hari</option>'+
                                            '<option value="Minggu">Minggu</option>'+
                                            '<option value="Bulan">Bulan</option>'+
                                            '<option value="Tahun">Tahun</option>'+
                                        '</select>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Biaya</label>'+
                                        '<input class="form-control" name="biaya" placeholder="Biaya">'+
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

            $(document).on("submit","#formTarget",function(e){
                var data = new FormData(this);
                data.append("tugas",idtugas);
                if($("#formTarget")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/target')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanTarget').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanTarget').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showTugas();
                                cekTarget();
                            }else{
                                $('#pesanTarget').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanTarget').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click",".edittugas",function(){
                idtarget=$(this).attr("kode");
                var el="";
                $.ajax({
                    url:"{{URL::to('home/data/target')}}/"+idtarget,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Taraget</h6>'+
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
                        el+='<form class="form-horizontal" onsubmit="return false;" id="formUpdateTarget">'+
                            '<div class="modal-body">'+
                                '<div id="pesanTarget"></div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Kuantitas</label>'+
                                    '<input class="form-control" name="kuantitas" placeholder="Kuantitas" value="'+result.kuant+'" required>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Output</label>'+
                                    '<select name="output" id="output" class="form-control">'+
                                        '<option value="">--Pilih Output--</option>'+
                                        '<option value="Keg">Kegiatan</option>'+
                                        '<option value="Berkas">Berkas</option>'+
                                        '<option value="Daftar">Daftar</option>'+
                                        '<option value="Dok">Dokumen</option>'+
                                        '<option value="Lap">Laporam</option>'+
                                    '</select>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Kualitas / Mutu</label>'+
                                    '<input class="form-control" name="mutu" placeholder="Kualitas / Mutu" value="'+result.kual+'" required>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Waktu</label>'+
                                    '<input class="form-control" name="waktu" placeholder="Waktu" value="'+result.waktu+'" required>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Periode</label>'+
                                    '<select name="periode" id="periode" class="form-control">'+
                                        '<option value="">--Pilih Periode--</option>'+
                                        '<option value="Hari">Hari</option>'+
                                        '<option value="Minggu">Minggu</option>'+
                                        '<option value="Bulan">Bulan</option>'+
                                        '<option value="Tahun">Tahun</option>'+
                                    '</select>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="control-label">Biaya</label>'+
                                    '<input class="form-control" name="biaya" placeholder="Biaya" value="'+result.biaya+'">'+
                                '</div>'+
                            '</div>'+

                            '<div class="modal-footer">'+
                                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                '<button type="submit" class="btn btn-primary">Save</button>'+
                            '</div>'+
                        '</form>';

                        $('#divFormTugas').empty().html(el);
                        $("#periode").val(result.periode_waktu);
                        $("#output").val(result.output);
                        //$("#periode option[value='"+result.periode_waktu+"']").prop('selected', true);
                    },
                    error:function(){

                    }
                })
                
            });

            $(document).on("submit","#formUpdateTarget",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateTarget")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/target')}}/"+idtarget,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanTarget').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanTarget').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showTugas();
                                cekTarget();
                            }else{
                                $('#pesanTarget').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanTarget').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click",".hapustugas",function(){
                var kode=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/target')}}/"+kode,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showTugas();
                                    cekTarget();
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
            cekTarget();
        })
    </script>
@stop