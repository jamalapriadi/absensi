@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Pegawai</h6>
            <div class="heading-elements">
                <a class="btn btn-primary" href="{{URL::to('home/pegawai/create')}}">
                    <i class="icon-add"></i>
                    Add New
                </a>

                {{--  <a class="btn btn-success" href="#">
                    <i class="icon-add"></i>
                    Import Pegawai
                </a>  --}}
            </div>
        </div>
        <div class="panel-body">
            {{--  <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">--Pilih Status--</option>
                            @foreach($status as $row)
                                <option value="{{$row->id}}">{{$row->nama_status}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="control-label">Jabatan</label>
                        <select class="form-control" name="jabatan" id="jabatan">
                            <option value="">--Pilih Jabatan--</option>
                            @foreach($jabatan as $row)
                                <option value="{{$row->id}}">{{$row->nama_jabatan}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label class="control-label">Pangkat</label>
                        <select class="form-control" name="pangkat" id="pangkat">
                            <option value="">--Pilih Pangkat--</option>
                            @foreach($pangkat as $row)
                                <option value="{{$row->id}}">{{$row->nama_pangkat}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>  --}}
            <table class="table table-striped datatable-colvis-basic-pegawai"></table>
        </div>
    </div>

    {{--  <div class="panel panel-default">
        <div class="panel-body">
            
        </div>
    </div>  --}}

    <div id="divModal"></div>
@stop

@section('js')
    <script>
        $(function(){
            var idpegawai="";

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

            function showPegawai(){
                $('.datatable-colvis-basic-pegawai').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/pegawai')}}",
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'gambar', name: 'gambar',title:'',searchable:false},
                        {data: 'nip', name: 'nip',title:'NIP',defaultContent: "-"},
                        {data: 'tmk', name: 'tmk',title:'TMK',defaultContent: "-"},
                        {data: 'nama_lengkap', name: 'nama_lengkap',title:'Nama Lengkap',defaultContent: "-"},
                        {data: 'pangkats', name: 'pangkats',title:'Pangkat',defaultContent: "-",searchable:false},
                        {data: 'jabatans', name: 'jabatans',title:'Jabatan',defaultContent: "-",searchable:false},
                        {data: 'atasan.nama_lengkap', name: 'atasan.nama_lengkap',title:'Atasan Langsung',defaultContent: "-",searchable:false},
                        //{data: 'tanggal_lahir', name: 'tanggal_lahir',title:'Tanggal Lahir'},
                        //{data: 'agama', name: 'agama',title:'Agama'},
                        //{data: 'alamat', name: 'alamat',title:'Alamat'},
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

            function changePegawai(){
                var jabatan=$("#jabatan option:selected").val();
                var pangkat=$("#pangkat option:selected").val();
                var status=$("#status option:selected").val();

                $('.datatable-colvis-basic-pegawai').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: true,
                    destroy: true,
                    ajax: "{{URL::to('home/pegawai')}}?jabatan="+jabatan+"&pangkat="+pangkat+"&status="+status,
                    columns: [
                        {data: 'no', name: 'no',title:'No.',searchable:false,width:'5%'},
                        {data: 'gambar', name: 'gambar',title:'',searchable:false},
                        {data: 'nip', name: 'nip',title:'NIP',defaultContent: "-"},
                        {data: 'tmk', name: 'tmk',title:'TMK',defaultContent: "-"},
                        {data: 'nama_lengkap', name: 'nama_lengkap',title:'Nama Lengkap',defaultContent: "-"},
                        {data: 'pangkats', name: 'pangkats',title:'Pangkat',defaultContent: "-"},
                        {data: 'jabatans', name: 'jabatans',title:'Jabatan',defaultContent: "-"},
                        //{data: 'tempat_lahir', name: 'tempat_lahir',title:'Tempat Lahir'},
                        //{data: 'tanggal_lahir', name: 'tanggal_lahir',title:'Tanggal Lahir'},
                        //{data: 'agama', name: 'agama',title:'Agama'},
                        //{data: 'alamat', name: 'alamat',title:'Alamat'},
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

            $(document).on("click","a.hapuspegawai",function(){
                idpegawai=$(this).attr("kode");

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
                            url:"{{URL::to('home/pegawai')}}/"+idpegawai,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showPegawai();
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

            $(document).on("change","#status",function(){
                changePegawai();
            })

            $(document).on("change","#jabatan",function(){
                changePegawai();
            })

            $(document).on("change","#pangkat",function(){
                changePegawai();
            })

            showPegawai();
        })
    </script>
@stop