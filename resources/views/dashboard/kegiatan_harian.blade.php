@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Data Kegiatan Harian</h6>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" id="addKegiatan">
                <i class="icon-add"></i> Add New
            </a>
            <table class="table table-striped datatable-colvis-basic-kegiatan"></table>
        </div>
    </div>

    <div id="divModal"></div>
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
            var idharian="";

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

            function showKegiatan(){
                $('.datatable-colvis-basic-kegiatan').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/nilai-harian')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'type_kegiatan', name: 'type_kegiatan',title:'Type'},
                        {data: 'tanggal', name: 'tanggal',title:'Hari / Tanggal'},
                        {data: 'dari_jam', name: 'dari_jam',title:'Dari Jam'},
                        {data: 'sampai_jam', name: 'sampai_jam',title:'Sampai Jam'},
                        {data: 'kegiatan', name: 'kegiatan',title:'Kegiatan'},
                        {data: 'hasil', name: 'hasil',title:'Hasil / Volume'},
                        {data: 'keterangan', name: 'keterangan',title:'Keterangan'},
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

            $(document).on("click","#addKegiatan",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Kegiatan Harian</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formKegiatan">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanKegiatan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Type Kegiatan</label>'+
                                        '<select name="type" id="type" class="form-control">'+
                                            '<option value="harian">Harian</option>'+
                                            '<option value="bulanan">Bulanan</option>'+
                                            '<option value="tahunan">Tahunan</option>'+
                                        '</select>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Tanggal</label>'+
                                        '<input class="form-control pickadate-year" name="tanggal" placeholder="Tanggal" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Dari Jam</label>'+
                                        '<input class="form-control pickatime" name="darijam" placeholder="Dari Jam" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Sampai Jam</label>'+
                                        '<input class="form-control pickatime" name="sampaijam" placeholder="Sampai Jam" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Kegiatan</label>'+
                                        '<input class="form-control" name="kegiatan" placeholder="Kegiatan" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Hasil / Volume</label>'+
                                        '<input class="form-control" name="hasil" placeholder="Hasil" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Keterangan</label>'+
                                        '<textarea name="keterangan" id="desc"></textarea>'+
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

                $('.pickadate-year').pickadate({
                    selectYears: true,
                    selectMonths: true,
                    selectYears: 4
                });

                $('.pickatime').pickatime();
            })

            $(document).on("submit","#formKegiatan",function(e){
                var data = new FormData(this);
                var desc = CKEDITOR.instances.desc.getData();
                data.append("desc", desc);
                if($("#formKegiatan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/nilai-harian')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanKegiatan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanKegiatan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showKegiatan();
                            }else{
                                $('#pesanKegiatan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanKegiatan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editharian",function(){
                idharian=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/nilai-harian')}}/"+idharian,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Nilai Harian</h6>'+
                                    '</div>'+

                                    '<div id="divFormKegiatan"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormKegiatan').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateKegiatan' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanKegiatan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Type Kegiatan</label>'+
                                        '<select name="type" id="type" class="form-control">'+
                                            '<option value="harian">Harian</option>'+
                                            '<option value="bulanan">Bulanan</option>'+
                                            '<option value="tahunan">Tahunan</option>'+
                                        '</select>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Tanggal</label>'+
                                        '<input class="form-control pickadate-year" value="'+result.tanggal+'" name="tanggal" placeholder="Tanggal" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Dari Jam</label>'+
                                        '<input class="form-control pickatime" name="darijam" value="'+result.dari_jam+'" placeholder="Dari Jam" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Sampai Jam</label>'+
                                        '<input class="form-control pickatime" name="sampaijam" value="'+result.sampai_jam+'" placeholder="Sampai Jam" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Kegiatan</label>'+
                                        '<input class="form-control" name="kegiatan" value="'+result.kegiatan+'" placeholder="Kegiatan" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Hasil / Volume</label>'+
                                        '<input class="form-control" name="hasil" value="'+result.hasil+'" placeholder="Hasil" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Keterangan</label>'+
                                        '<textarea name="keterangan" id="desc">"'+result.keterangan+'"</textarea>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormKegiatan').empty().html(el);
                        getCkeditor();

                        $('.pickadate-year').pickadate({
                            selectYears: true,
                            selectMonths: true,
                            selectYears: 4
                        });

                        $('.pickatime').pickatime();

                        $("#type").val(result.type_kegiatan);
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateKegiatan",function(e){
                var data = new FormData(this);
                var desc = CKEDITOR.instances.desc.getData();
                data.append("desc", desc);
                data.append("_method","PUT");
                if($("#formUpdateKegiatan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/nilai-harian')}}/"+idharian,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanKegiatan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanKegiatan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showKegiatan();
                            }else{
                                $('#pesanKegiatan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanKegiatan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapusharian",function(){
                idharian=$(this).attr("kode");

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
                            url:"{{URL::to('home/nilai-harian')}}/"+idharian,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showKegiatan();
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

            showKegiatan();
        })

    </script>
@stop