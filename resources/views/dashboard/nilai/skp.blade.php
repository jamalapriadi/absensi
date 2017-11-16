@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Form SKP</h6>
            <div class="heading-elements">
                <a class="btn btn-primary" href="#" id="addSkp">
                    <i class="icon-add"></i>
                    Add Form SKP
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped datatable-colvis-basic-nilai"></table>
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
            var idskp="";
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

            function showData(){
                $('.datatable-colvis-basic-nilai').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/data/nilai-skp')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'tgl_penilaian', name: 'tgl_penilaian',title:'Tanggal',searchable:false},
                        {data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap',title:'Pegawai',defaultContent: "-"},
                        {data: 'penilai.nama_lengkap', name: 'penilai.nama_lengkap',title:'Pejabat Penilai',defaultContent: "-"},
                        {data: 'atasan.nama_lengkap', name: 'atasan.nama_lengkap',title:'Atasan Pejabat Penilai',defaultContent: "-"},
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

            $(document).on("click","#addSkp",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Form SKP</h6>'+
                            '</div>'+
                            '<form class="form-horizontal" onsubmit="return false" id="formSkp">'+
                                '<div class="panel-body">'+
                                    '<div id="pesanSkp"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Tanggal</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control pickadate-year" name="tanggal">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Pegawai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-pegawai" name="pegawai">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Pejabat Penilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-pegawai" name="pejabat">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Atasan Pejabat Penilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-pegawai" name="atasan">'+
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

                $('.pickadate-year').pickadate({
                    selectYears: true,
                    selectMonths: true,
                    selectYears: 4
                });

                $(".remote-data-pegawai").select2({
                    placeholder: "Cari Pegawai",
                    ajax: {
                        url: "{{URL::to('home/data/list-pegawai')}}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params, // search term
                                page_limit: 50,
                            };
                        },
                        results: function (data, page){
                            return {
                                results: data.data
                            };
                        },
                        cache: true,
                        pagination: {
                            more: true
                        }
                    },
                    formatResult: function(m){
                        var markup="<option value='"+m.id+"'>"+m.text+"</option>";
            
                        return markup;
                    },
                    formatSelection: function(m){
                        return m.text;
                    },
                    escapeMarkup: function (m) { return m; }
                })
            })

            $(document).on("submit","#formSkp",function(e){
                var data = new FormData(this);
                if($("#formSkp")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/nilai-skp')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanSkp').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanSkp').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showData();
                            }else{
                                $('#pesanSkp').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanSkp').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editnilai",function(){
                idskp=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/data/nilai-skp')}}/"+idskp,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Form SKP</h6>'+
                                    '</div>'+

                                    '<div id="divFormSkp"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormSkp').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+='<form class="form-horizontal" onsubmit="return false" id="formUpdateSkp">'+
                                '<div class="panel-body">'+
                                    '<div id="pesanSkp"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Tanggal</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control pickadate-year" name="tanggal" value="'+result.tgl_penilaian+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Pegawai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-pegawai" name="pegawai" value="'+result.pegawai_id+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Pejabat Penilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-pejabat" name="pejabat" value="'+result.pejabat_penilai+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="col-lg-4 control-label">Atasan Pejabat Penilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-atasan" name="atasan" value="'+result.atasan_pejabat_penilai+'">'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            '</form>';

                        $('#divFormSkp').empty().html(el);
                        
                        $('.pickadate-year').pickadate({
                            selectYears: true,
                            selectMonths: true,
                            selectYears: 4
                        });

                        $(".remote-data-pegawai").select2({
                            initSelection: function(element, callback) {
                                callback({id: result.pegawai_id, text: result.pegawai.nama_lengkap });
                            },
                            ajax: {
                                url: "{{URL::to('home/data/list-pegawai')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
                                        q: params, // search term
                                        page_limit: 50,
                                    };
                                },
                                results: function (data, page){
                                    return {
                                        results: data.data
                                    };
                                },
                                cache: true,
                                pagination: {
                                    more: true
                                }
                            },
                            formatResult: function(m){
                                var markup="<option value='"+m.id+"'>"+m.text+"</option>";
                    
                                return markup;
                            },
                            formatSelection: function(m){
                                return m.text;
                            },
                            escapeMarkup: function (m) { return m; }
                        })

                        $(".remote-data-pejabat").select2({
                            initSelection: function(element, callback) {
                                callback({id: result.pejabat_penilai, text: result.penilai.nama_lengkap });
                            },
                            ajax: {
                                url: "{{URL::to('home/data/list-pegawai')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
                                        q: params, // search term
                                        page_limit: 50,
                                    };
                                },
                                results: function (data, page){
                                    return {
                                        results: data.data
                                    };
                                },
                                cache: true,
                                pagination: {
                                    more: true
                                }
                            },
                            formatResult: function(m){
                                var markup="<option value='"+m.id+"'>"+m.text+"</option>";
                    
                                return markup;
                            },
                            formatSelection: function(m){
                                return m.text;
                            },
                            escapeMarkup: function (m) { return m; }
                        })

                        $(".remote-data-atasan").select2({
                            initSelection: function(element, callback) {
                                callback({id: result.atasan_pejabat_penilai, text: result.atasan.nama_lengkap });
                            },
                            ajax: {
                                url: "{{URL::to('home/data/list-pegawai')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
                                        q: params, // search term
                                        page_limit: 50,
                                    };
                                },
                                results: function (data, page){
                                    return {
                                        results: data.data
                                    };
                                },
                                cache: true,
                                pagination: {
                                    more: true
                                }
                            },
                            formatResult: function(m){
                                var markup="<option value='"+m.id+"'>"+m.text+"</option>";
                    
                                return markup;
                            },
                            formatSelection: function(m){
                                return m.text;
                            },
                            escapeMarkup: function (m) { return m; }
                        })
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateSkp",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateSkp")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/nilai-skp')}}/"+idskp,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanSkp').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanSkp').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showData();
                            }else{
                                $('#pesanSkp').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanSkp').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapusnilai",function(){
                idskp=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/nilai-skp')}}/"+idskp,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showData();
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

            showData();
        })
    </script>
@stop