@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Master Data</h6>
        </div>
        <div class="panel-body">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-highlight text-left">
                    <li class="active" id="tabInstansi"><a href="#right-tab1" data-toggle="tab">Instansi</a></li>
                    <li id="tabJabatan"><a href="#right-tab5" data-toggle="tab">Jabatan</a></li>
                    <li id="tabGolongan"><a href="#right-tab2" data-toggle="tab">Golongan</a></li>
                    <li id="tabPangkat"><a href="#right-tab3" data-toggle="tab">Pangkat</a></li>
                    <li id="tabStatus"><a href="#right-tab4" data-toggle="tab">Status</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="right-tab1">
                        <div id="divShowInstansi"></div>
                    </div>

                    <div class="tab-pane" id="right-tab2">
                        <a class="btn btn-primary" id="addGolongan">
                            <i class="icon-add"></i> Add New
                        </a>
                        <table class="table table-striped datatable-colvis-basic-category"></table>
                    </div>

                    <div class="tab-pane" id="right-tab3">
                        <a class="btn btn-primary" id="addPangkat">
                            <i class="icon-add"></i> Add New
                        </a>

                        <table class="table table-striped datatable-colvis-basic-season"></table>
                    </div>

                    <div class="tab-pane" id="right-tab4">
                        <a class="btn btn-primary" id="addStatus">
                            <i class="icon-add"></i> Add New
                        </a>

                        <table class="table table-striped datatable-colvis-basic-status"></table>
                    </div>

                    <div class="tab-pane" id="right-tab5">
                        <a class="btn btn-primary" id="addJabatan">
                            <i class="icon-add"></i> Add New
                        </a>
                        <table class="table table-striped datatable-colvis-basic-jabatan"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="divModal"></div>

@stop

@section('js')
    <script>
        $(function(){
            var kode="";
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

            $(document).on("click","#tabInstansi",function(){
                showInstansi();
            });

            $(document).on("click","#tabGolongan",function(){
                showGolongan();
            });

            $(document).on("click","#tabPangkat",function(){
                showPangkat();
            })

            $(document).on("click","#tabStatus",function(){
                showStatus();
            })

            $(document).on("click","#tabJabatan",function(){
                showJabatan();
            })

            function showInstansi(){
                $.ajax({
                    url:"{{URL::to('home/data/instansi')}}",
                    type:"GET",
                    beforeSend:function(){
                        $("#divShowInstansi").empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success:function(result){
                        console.log(result);
                        var el="";
                        el+="<form class='form-horizontal' id='formInstansi' onsubmit='return false;'>"+
                            "<div class='form-group'>"+
                                "<label class='control-label text-semibold'>Nama Instansi</label>"+
                                "<input type='hidden' class='form-control' name='kode' value='"+result.instansi.id+"'>"+
                                "<input class='form-control' name='nama' value='"+result.instansi.nama_instansi+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Kelas</label>"+
                                "<input class='form-control' name='kelas' value='"+result.instansi.kelas+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Alamat</label>"+
                                "<input class='form-control' name='alamat' value='"+result.instansi.alamat+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Kode POS</label>"+
                                "<input class='form-control' name='kodepos' value='"+result.instansi.kode_pos+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Telp.</label>"+
                                "<input class='form-control' name='telp' value='"+result.instansi.telp+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Fax</label>"+
                                "<input class='form-control' name='fax' value='"+result.instansi.fax+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Websiste</label>"+
                                "<input class='form-control' name='website' value='"+result.instansi.website+"'>"+
                            "</div>"+
                            "<div class='form-group'>"+
                                "<label class='control-label'>Email</label>"+
                                "<input class='form-control' name='email' value='"+result.instansi.email+"'>"+
                            "</div>"+
                            "<div id='pesanInstansi'></div>"+
                            "<div class='form-group well'>"+
                                "<button class='btn btn-primary'><i class='icon-floppy-disk'></i> Simpan</button>"+
                            "</div>"+
                        "</form>";
                        $("#divShowInstansi").empty().html(el);
                    }
                })
            } 

            function showGolongan(){
                $('.datatable-colvis-basic-category').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/data/golongan')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false},
                        {data: 'nama_golongan', name: 'nama_golongan',title:'Nama Golongan'},
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

            function showSeason(){
                $('.datatable-colvis-basic-season').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('mam/program/season')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false},
                        {data: 'season_name', name: 'season_name',title:'Season Name'},
                        {data: 'active', name: 'active',title:'Active'},
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

            function showPangkat(){
                $('.datatable-colvis-basic-season').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/data/pangkat')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false},
                        {data: 'nama_pangkat', name: 'nama_pangkat',title:'Nama Pangkat'},
                        {data: 'golongan.nama_golongan', name: 'golongan.nama_golongan',title:'Golongan'},
                        {data: 'ruang', name: 'ruang',title:'Ruang'},
                        {data: 'action', name: 'action',title:'',search:false, width:'17%'}
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

            function showStatus(){
                $('.datatable-colvis-basic-status').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/data/status')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false},
                        {data: 'nama_status', name: 'nama_status',title:'Name'},
                        {data: 'action', name: 'action',title:'',search:false, width:'17%'}
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

            $(document).on("submit","#formInstansi",function(e){
                var data = new FormData(this);
                if($("#formInstansi")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/instansi')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanInstansi').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanInstansi').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showInstansi();
                            }else{
                                $('#pesanInstansi').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanInstansi').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            /* crud golongan */
            var idgolongan="";
            $(document).on("submit","#formGolongan",function(e){
                var data = new FormData(this);
                if($("#formGolongan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/golongan')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanGolongan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanGolongan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showGolongan();
                            }else{
                                $('#pesanGolongan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanGolongan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","#addGolongan",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Golongan</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formGolongan">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanGolongan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Golongan</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Golongan" required>'+
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

            $(document).on("click","a.editgolongan",function(){
                idgolongan=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/data/golongan')}}/"+idgolongan,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Add New Golongan</h6>'+
                                    '</div>'+

                                    '<div id="divFormGolongan"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormGolongan').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateGolongan' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanGolongan"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Golongan</label>'+
                                        '<input class="form-control" value="'+result.nama_golongan+'" name="nama" placeholder="Nama Golongan" required>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormGolongan').empty().html(el);
                    },
                    error:function(){
                        
                    }
                })
            })

            $(document).on("submit","#formUpdateGolongan",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateGolongan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/golongan')}}/"+idgolongan,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanGolongan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanGolongan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showGolongan();
                            }else{
                                $('#pesanGolongan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanGolongan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapusgolongan",function(){
                idgolongan=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/golongan')}}/"+idgolongan,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showGolongan();
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
            /* end crud golongan */

            /* crud pangkat */
            var idpangkat="";
            $(document).on("click","#addPangkat",function(){
                var el="";

                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Golongan</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formPangkat">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanPangkat"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Pangkat</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Pangkat" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Golongan</label>'+
                                        '<input type="text" class="remote-data-golongan" name="golongan" id="golongan" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Ruang</label>'+
                                        '<input class="form-control" name="ruang" placeholder="Ruang" required>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            '</form>';
                        '</div>'+
                    '</div>'+
                '</div>';

                $("#divModal").empty().html(el);
                $("#modalHistory").modal('show');

                $(".remote-data-golongan").select2({
                    placeholder: "Search for a Golongan",
                    ajax: {
                        url: "{{URL::to('home/data/list-golongan')}}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params, // search term
                                page_limit: 10,
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
            });

            $(document).on("submit","#formPangkat",function(e){
                var data = new FormData(this);
                if($("#formPangkat")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/pangkat')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanPangkat').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanPangkat').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showPangkat();
                            }else{
                                $('#pesanPangkat').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanPangkat').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editpangkat",function(){
                idpangkat=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/data/pangkat')}}/"+idpangkat,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Pangkat</h6>'+
                                    '</div>'+

                                    '<div id="divFormPangkat"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormPangkat').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+='<form class="form-horizontal" onsubmit="return false;" id="formUpdatePangkat">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanPangkat"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Pangkat</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Pangkat" value="'+result.nama_pangkat+'" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Golongan</label>'+
                                        '<input type="text" class="remote-data-golongan" name="golongan" id="golongan" required>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Ruang</label>'+
                                        '<input class="form-control" name="ruang" placeholder="Ruang" value="'+result.ruang+'" required>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            '</form>';

                        $('#divFormPangkat').empty().html(el);

                        $(".remote-data-golongan").select2({
                            initSelection: function(element, callback) {
                                callback({id: result.id_golongan, text: result.golongan.nama_golongan });
                            },
                            placeholder: "Search for a Golongan",
                            ajax: {
                                url: "{{URL::to('home/data/list-golongan')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
                                        q: params, // search term
                                        page_limit: 10,
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
            });

            $(document).on("submit","#formUpdatePangkat",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdatePangkat")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/pangkat')}}/"+idpangkat,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanPangkat').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanPangkat').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showPangkat();
                            }else{
                                $('#pesanPangkat').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanPangkat').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapuspangkat",function(){
                idpangkat=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/pangkat')}}/"+idpangkat,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showPangkat();
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
            /* end crud pangkat */

            /* crud status */
            var idstatus="";
            $(document).on("click","#addStatus",function(){
                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add New Status Kepegawaian</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formStatus">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanStatus"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Status</label>'+
                                        '<input class="form-control" name="nama" placeholder="Nama Status" required>'+
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
            });

            $(document).on("submit","#formStatus",function(e){
                var data = new FormData(this);
                if($("#formStatus")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/status')}}",
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanStatus').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanStatus').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showStatus();
                            }else{
                                $('#pesanStatus').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanStatus').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.editstatus",function(){
                idstatus=$(this).attr("kode");
                var el="";
                
                $.ajax({
                    url:"{{URL::to('home/data/status')}}/"+idstatus,
                    type:"GET",
                    beforeSend:function(){
                        el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                            '<div class="modal-dialog">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-info">'+
                                        '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                        '<h6 class="modal-title">Edit Status Kepegawaian</h6>'+
                                    '</div>'+

                                    '<div id="divFormStatus"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

                        $("#divModal").empty().html(el);
                        $('#divFormStatus').empty().html('<div class="panel-body"><div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div></div>');
                        $("#modalHistory").modal('show');
                    },
                    success:function(result){
                        el+="<form class='form-horizontal' id='formUpdateStatus' onsubmit='return false;'>"+
                                "<div class='panel-body'>"+
                                    '<div id="pesanStatus"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label">Nama Status</label>'+
                                        '<input class="form-control" value="'+result.nama_status+'" name="nama" placeholder="Nama Status" required>'+
                                    '</div>'+
                                "</div>"+
                                '<div class="modal-footer">'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                    '<button type="submit" class="btn btn-primary">Save</button>'+
                                '</div>'+
                            "</form>";

                        $('#divFormStatus').empty().html(el);
                    },
                    error:function(){
                        
                    }
                })
            });

            $(document).on("submit","#formUpdateStatus",function(e){
                var data = new FormData(this);
                data.append("_method","PUT");
                if($("#formUpdateStatus")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/status')}}/"+idstatus,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanStatus').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanStatus').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showStatus();
                            }else{
                                $('#pesanStatus').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanStatus').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            });

            $(document).on("click","a.hapusstatus",function(){
                idstatus=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/status')}}/"+idstatus,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showStatus();
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
            /* end crud status */

            /* crud jabatan */
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
            /* end crud jabatan */

            showInstansi();
        })
    </script>
@stop