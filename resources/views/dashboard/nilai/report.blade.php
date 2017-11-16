@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Report SKP</h6>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:5%">NO</th>
                    <th colspan="2">I. PEJABAT PENILAI</th>
                    <th style="width:5%">NO</th>
                    <th colspan="2">II. PEGAWAI YANG DINILAI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>Nama</td>
                    <td>{{$nilai->penilai->nama_lengkap}}</td>
                    <td>1.</td>
                    <td>Nama</td>
                    <td>{{$nilai->pegawai->nama_lengkap}}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>NIP</td>
                    <td>{{$nilai->penilai->nip}}</td>
                    <td>2.</td>
                    <td>NIP</td>
                    <td>{{$nilai->pegawai->nip}}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Pangkat / Gol. Ruang</td>
                    <td>{{$nilai->penilai->pangkat[0]->nama_pangkat}}</td>
                    <td>3.</td>
                    <td>Pangkat / Gol. Ruang</td>
                    <td>{{$nilai->pegawai->pangkat[0]->nama_pangkat}}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Jabatan</td>
                    <td>{{$nilai->penilai->jabatan[0]->nama_jabatan}}</td>
                    <td>4.</td>
                    <td>Jabatan</td>
                    <td>{{$nilai->pegawai->jabatan[0]->nama_jabatan}}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Unit Kerja</td>
                    <td>{{$instansi->nama_instansi}}</td>
                    <td>5.</td>
                    <td>Unit Kerja</td>
                    <td>{{$instansi->nama_instansi}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-highlight text-left">
                    <li class="active" id="tabDataSkp"><a href="#right-tab1" data-toggle="tab">Form SKP</a></li>
                    <li id="tabFormSkp"><a href="#right-tab2" data-toggle="tab">Pengukuran</a></li>
                    <li id="tabPengukuran"><a href="#right-tab3" data-toggle="tab">Perilaku Kerja</a></li>
                    {{--  <li id="tabPerilaku"><a href="#right-tab4" data-toggle="tab">Penilaian</a></li>  --}}
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="right-tab1">
                        <div id="divFormSkp"></div>
                    </div>

                    <div class="tab-pane" id="right-tab2">
                        <div id="divFormSkpRealisasi"></div>
                    </div>

                    <div class="tab-pane" id="right-tab3">
                        <a class="btn btn-primary" id="addPerilaku">
                            <i class="icon-add"></i> Add Perilaku Kerja
                        </a>
                        <br><br>
                        <div id="divPerilakuKerja"></div>
                    </div>

                    <div class="tab-pane" id="right-tab4">
                        penilaian
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
            var jabatan="{{$nilai->pegawai->id}}";
            var skp_id="{{$nilai->id}}";
            var idtugas="";
            var idtarget="";
            
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

            function showFormSkp(){
                $.ajax({
                    url:"{{URL::to('home/data/form-skp')}}/"+jabatan,
                    beforeSend:function(){
                        $("#divFormSkp").empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success:function(result){
                        var el="";
                        el+='<table class="table table-bordered">'+
                            '<thead>'+
                                '<tr>'+
                                    '<th rowspan="2" class="text-center">No.</th>'+
                                    '<th rowspan="2" class="text-center">Kegiatan Tugas Jabatan</th>'+
                                    '<th rowspan="2" class="text-center">AK</th>'+
                                    '<th colspan="4" class="text-center">TARGET</th>'+
                                '</tr>'+
                                '<tr>'+
                                    '<th class="text-center">Kuant / Output</th>'+
                                    '<th class="text-center">Kual / Mutu</th>'+
                                    '<th class="text-center">Waktu</th>'+
                                    '<th class="text-center">Biaya</th>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>';
                            var no=0;
                            $.each(result.tugas,function(a,b){
                                no++;
                                el+='<tr>'+
                                    '<td>'+no+'</td>'+
                                    '<td>'+b.nama_tugas+'</td>'+
                                    '<td></td>';
                                    $.each(b.target,function(c,d){
                                        var biaya="";
                                        if(d.biaya==null){
                                            biaya="";
                                        }else{
                                            biaya=d.biaya;
                                        }
                                        el+='<td>'+d.kuant+' / '+d.output+'</td>'+
                                        '<td>'+d.kual+'</td>'+
                                        '<td>'+d.waktu+' '+d.periode_waktu+'</td>'+
                                        '<td>'+biaya+'</td>';
                                    })
                                el+='</tr>';
                            })
                            el+='</tbody>'+
                        '</table>';

                        $("#divFormSkp").empty().html(el);
                    },
                    error:function(){
                        $("#divFormSkp").empty().html('<div class="alert alert-danger"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Data Failed to load</div>');
                    }
                })
            }

            function showFormSkpRealisasi(){
                $.ajax({
                    url:"{{URL::to('home/data/form-skp-realisasi')}}/"+jabatan,
                    beforeSend:function(){
                        $("#divFormSkpRealisasi").empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success:function(result){
                        var el="";
                        if(result.tugas.length>0){
                            el+='<form name="formHasilSkp" id="formHasilSkp" onsubmit="return false;"><table class="table table-bordered">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th rowspan="2" class="text-center">No.</th>'+
                                        '<th rowspan="2" class="text-center">Kegiatan Tugas Jabatan</th>'+
                                        '<th rowspan="2" class="text-center">AK</th>'+
                                        '<th colspan="4" class="text-center">REALISASI</th>'+
                                        '<th rowspan="2" class="text-center">PENGHITUNGAN</th>'+
                                        '<th rowspan="2" class="text-center">NILAI CAPAIAN SKP</th>'+
                                        '<th rowspan="2" class="text-center" style="width:7%"></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th class="text-center">Kuant / Output</th>'+
                                        '<th class="text-center">Kual / Mutu</th>'+
                                        '<th class="text-center">Waktu</th>'+
                                        '<th class="text-center">Biaya</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>';
                                var no=0;
                                $.each(result.tugas,function(a,b){
                                    no++;
                                    el+='<tr>'+
                                        '<td>'+no+'</td>'+
                                        '<td>'+b.nama_tugas+'</td>'+
                                        '<td></td>';
                                        if(b.target.length>0){
                                            $.each(b.target,function(c,d){
                                                var biaya="";
                                                if(d.biaya==null){
                                                    biaya="";
                                                }else{
                                                    biaya=d.biaya;
                                                }
                                                el+='<td>'+d.kuant+' / '+d.output+'</td>'+
                                                '<td>'+d.kual+'</td>'+
                                                '<td>'+d.waktu+' '+d.periode_waktu+'</td>'+
                                                '<td>'+biaya+'</td>'+
                                                '<td>'+d.perhitungan+'</td>'+
                                                '<td>'+d.nilai_pencapaian+'</td>'+
                                                '<td><a class="btn btn-sm btn-warning edittarget" target="'+d.id+'" kode="'+b.id+'" tugas="'+b.nama_tugas+'" kuan="'+d.kuant+'" kual="'+d.kual+'" output="'+d.output+'" waktu="'+d.waktu+'" periode="'+d.periode_waktu+'" biaya="'+d.biaya+'" perhitungan="'+d.perhitungan+'" nilai="'+d.nilai_pencapaian+'"><i class="icon-pencil4"></i></a></td>';
                                            })
                                        }else{
                                            el+="<td colspan='6' class='text-center'>Tidak ada Data</td>"+
                                                '<td><a class="btn btn-sm btn-primary addtarget" kode="'+b.id+'" tugas="'+b.nama_tugas+'"><i class="icon-add"></i></a></td>';
                                        }
                                    el+='</tr>';
                                })
                                el+="<tr>"+
                                    '<td></td>'+
                                    '<td colspan="9"><a class="btn btn-sm btn-info" id="addNilaiTambahan" skp="'+result.nilai.id+'"><i class="icon-add"></i></a> <strong>Tugas Tambahan Kreativitas</strong></td>'+
                                '</tr>';
                                if(result.nilai.tambahan.length>0){
                                    var n=0;
                                    $.each(result.nilai.tambahan,function(a,b){
                                        n++;
                                        el+="<tr>"+
                                            "<td>"+n+"</td>"+
                                            "<td colspan='7'>"+b.nama+"</td>"+
                                            "<td class='text-center'>"+b.nilai+"</td>"+
                                            "<td><a class='edittugastambahan' skp='"+b.nilai_skp_id+"' type='"+b.type+"' id='"+b.id+"' nama='"+b.nama+"' nilai='"+b.nilai+"'><i class='icon-pencil5'></i></a> <a class='hapustugastambahan' id='"+b.id+"'><i class='icon-trash'></i></a></td>"+
                                        "</tr>";
                                    })
                                }
                                el+="<tr>"+
                                    '<td colspan="8" rowspan="2" class="text-center"><strong>Nilai Capaian SKP</strong></td>'+
                                    '<td colspan="2"><input type="hidden" name="idskp" id="idskp" value="'+result.nilai.id+'"><input class="form-control col-lg-1" name="nilaiskp" value="'+result.nilai.nilai_pencapaian+'" required></td>'+
                                '</tr>'+
                                '<tr>'+
                                    '<td colspan="2"><button class="btn btn-block btn-primary pull-right"><i class="icon-floppy-disk"></i> Update</a></td>'+
                                '</tr>';
                                el+='</tbody>'+
                            '</table></div><div id="pesanTarget2"></div>';
                        }else{
                            el+="<a href='#' class='btn btn-primary'>Add Realisasi</a>";
                        }

                        $("#divFormSkpRealisasi").empty().html(el);
                    },
                    error:function(){
                        $("#divFormSkpRealisasi").empty().html('<div class="alert alert-danger"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Data Failed to load</div>');
                    }
                })
            }

            function showPerilakuKerja(){
                $.ajax({
                    url:"{{URL::to('home/data/perilaku-kerja-by-id-skp')}}/"+skp_id,
                    type:"GET",
                    beforeSend:function(){
                        $("#divPerilakuKerja").empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success:function(result){
                        var el="";
                        el+='<table class="table table-bordered" id="prestasi">'+
                            '<thead>'+
                                '<tr>'+
                                    '<th>No.</th>'+
                                    '<th>Perilaku Kerja</th>'+
                                    '<th>Nilai</th>'+
                                    '<th></th>'+
                                '</tr>'+
                            '</thead>';
                            if(result.prestasi.length>0){
                                var no=0;
                                $.each(result.prestasi,function(a,b){
                                    no++;
                                    el+="<tr>"+
                                        "<td style='width:5%'>"+no+"</td>"+
                                        "<td>"+b.nama_perilaku+"</td>"+
                                        "<td>"+b.pivot.nilai+"</td>"+
                                        "<td style='width:15%'>"+
                                            "<div class='btn-group'>"+
                                                "<a class='btn btn-sm btn-warning editperilaku' kode='"+b.pivot.id+"' idper='"+b.pivot.perilaku_kerja_id+"' namaper='"+b.nama_perilaku+"' nilai='"+b.pivot.nilai+"'><i class='icon-pencil4'></i></a>"+
                                                "<a class='btn btn-sm btn-danger hapusperilaku' kode='"+b.pivot.id+"'><i class='icon-trash'></i></a>"+
                                            "</div>"+
                                        "</td>"+
                                    "</tr>";
                                })
                            }
                        el+='</table>';

                        $("#divPerilakuKerja").empty().html(el);
                        $("#prestasi").DataTable({
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
                    },
                    error:function(){
                        $("#divPerilakuKerja").empty().html("<div class='alert alert-danger'>Failed load data</div>");
                    }
                })
            }

            $(document).on("click","#tabDataSkp",function(){
                showFormSkp();
            });

            $(document).on("click","#tabFormSkp",function(){
                showFormSkpRealisasi();
            });

            $(document).on("click","#tabPengukuran",function(){
                showPerilakuKerja();
            })

            $(document).on("click","a.addtarget",function(){
                idtugas=$(this).attr("kode");
                var tugas=$(this).attr("tugas");

                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Realisasi Target Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formTarget">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Nama Tugas</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="tugas" value="'+tugas+'" readonly>'+
                                        '</div>'+
                                    '</div><hr>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Kuantitas</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="kuantitas" placeholder="Kuantitas" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Output</label>'+
                                        '<div class="col-lg-9">'+
                                            '<select name="output" id="output" class="form-control">'+
                                                '<option value="">--Pilih Output--</option>'+
                                                '<option value="Keg">Kegiatan</option>'+
                                                '<option value="Berkas">Berkas</option>'+
                                                '<option value="Daftar">Daftar</option>'+
                                                '<option value="Dok">Dokumen</option>'+
                                                '<option value="Lap">Laporam</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Kualitas / Mutu</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="mutu" placeholder="Kualitas / Mutu" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Waktu</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="waktu" placeholder="Waktu" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Periode</label>'+
                                        '<div class="col-lg-9">'+
                                            '<select name="periode" id="periode" class="form-control">'+
                                                '<option value="">--Pilih Periode--</option>'+
                                                '<option value="Hari">Hari</option>'+
                                                '<option value="Minggu">Minggu</option>'+
                                                '<option value="Bulan">Bulan</option>'+
                                                '<option value="Tahun">Tahun</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Biaya</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="biaya" placeholder="Biaya">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Penghitungan</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="penghitungan" placeholder="Penghitungan">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Nilai Capaian SKP</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="nilai" placeholder="nilai">'+
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
            })

            $(document).on("click","a.edittarget",function(){
                idtugas=$(this).attr("target");
                var tugas=$(this).attr("tugas");
                var kuan=$(this).attr("kuan");
                var kual=$(this).attr("kual");
                var output=$(this).attr("output");
                var waktu=$(this).attr("waktu");
                var periode=$(this).attr("periode");
                var biaya=$(this).attr("biaya");
                var perhitungan=$(this).attr("perhitungan");
                var nilai=$(this).attr("nilai");

                var el="";
                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Realisasi Target Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formUpdateTarget">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Nama Tugas</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="tugas" value="'+tugas+'" readonly>'+
                                        '</div>'+
                                    '</div><hr>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Kuantitas</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="kuantitas" placeholder="Kuantitas" value="'+kuan+'" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Output</label>'+
                                        '<div class="col-lg-9">'+
                                            '<select name="output" id="output" class="form-control">'+
                                                '<option value="">--Pilih Output--</option>'+
                                                '<option value="Keg">Kegiatan</option>'+
                                                '<option value="Berkas">Berkas</option>'+
                                                '<option value="Daftar">Daftar</option>'+
                                                '<option value="Dok">Dokumen</option>'+
                                                '<option value="Lap">Laporam</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Kualitas / Mutu</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="mutu" placeholder="Kualitas / Mutu" value="'+kual+'" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Waktu</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="waktu" placeholder="Waktu" value="'+waktu+'" required>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Periode</label>'+
                                        '<div class="col-lg-9">'+
                                            '<select name="periode" id="periode" class="form-control">'+
                                                '<option value="">--Pilih Periode--</option>'+
                                                '<option value="Hari">Hari</option>'+
                                                '<option value="Minggu">Minggu</option>'+
                                                '<option value="Bulan">Bulan</option>'+
                                                '<option value="Tahun">Tahun</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Biaya</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="biaya" value="'+biaya+'" placeholder="Biaya">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Penghitungan</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="penghitungan" value="'+perhitungan+'" placeholder="Penghitungan">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-3">Nilai Capaian SKP</label>'+
                                        '<div class="col-lg-9">'+
                                            '<input class="form-control" name="nilai" value="'+nilai+'" placeholder="nilai">'+
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
                $("#output").val(output);
                $("#periode").val(periode);
            })

            $(document).on("submit","#formTarget",function(e){
                var data = new FormData(this);
                data.append("tugas",idtugas);
                data.append("type","realisasi");
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
                                showFormSkpRealisasi();
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

            $(document).on("submit","#formUpdateTarget",function(e){
                var data = new FormData(this);
                data.append("type","realisasi");
                data.append("_method","PUT");
                if($("#formUpdateTarget")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/target/')}}"+idtugas,
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
                                showFormSkpRealisasi();
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

            $(document).on("click","#addNilaiTambahan",function(){
                var el="";
                var skp=$(this).attr("skp");

                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Tugas / Kreativitas Tambahan</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formTugasTambahan">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<input type="hidden" name="skp" value="'+skp+'">'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Type</label>'+
                                        '<div class="col-lg-8">'+
                                            '<select name="type" id="type" class="form-control">'+
                                                '<option value="">--Pilih Type--</option>'+
                                                '<option value="tugas">Tugas</option>'+
                                                '<option value="kreativitas">Kreativitas</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nama Tugas / Kreativitas</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nama" placeholder="Nama Tugas / Kreativitas">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nilai Tugas / Kreativitas</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nilai" placeholder="Nilai Tugas / Kreativitas">'+
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
            });

            $(document).on("click","a.edittugastambahan",function(){
                var el="";
                var skp=$(this).attr("skp");
                var type=$(this).attr("type");
                var nama=$(this).attr("nama");
                var nilai=$(this).attr("nilai");
                var id=$(this).attr("id");

                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Tugas / Kreativitas Tambahan</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formTugasTambahan">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<input type="hidden" name="kode" value="'+id+'">'+
                                    '<input type="hidden" name="skp" value="'+skp+'">'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Type</label>'+
                                        '<div class="col-lg-8">'+
                                            '<select name="type" id="type" class="form-control">'+
                                                '<option value="">--Pilih Type--</option>'+
                                                '<option value="tugas">Tugas</option>'+
                                                '<option value="kreativitas">Kreativitas</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nama Tugas / Kreativitas</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nama" placeholder="Nama Tugas / Kreativitas" value="'+nama+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nilai Tugas / Kreativitas</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nilai" placeholder="Nilai Tugas / Kreativitas" value="'+nilai+'">'+
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
                $("#type").val(type);
            });

            $(document).on("submit","#formTugasTambahan",function(e){
                var data = new FormData(this);
                if($("#formTugasTambahan")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/tugastambahan')}}",
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
                                showFormSkpRealisasi();
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

            $(document).on("click","a.hapustugastambahan",function(){
                id=$(this).attr("id");

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
                            url:"{{URL::to('home/data/tugastambahan')}}/"+id,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showFormSkpRealisasi();
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

            $(document).on("submit","#formHasilSkp",function(e){
                var id=$("#idskp").val();
                var data = new FormData(this);
                data.append("_method","PUT")
                if($("#formHasilSkp")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/update-nilai-skp')}}/"+id,
                        type        : 'post',
                        data        : data,
                        dataType    : 'JSON',
                        contentType : false,
                        cache       : false,
                        processData : false,
                        beforeSend  : function (){
                            $('#pesanTarget2').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                        },
                        success : function (data) {
                            console.log(data);

                            if(data.success==true){
                                $('#pesanTarget2').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                                showFormSkpRealisasi();
                            }else{
                                $('#pesanTarget2').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                            }
                        },
                        error   :function() {  
                            $('#pesanTarget2').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                        }
                    });
                }else console.log("invalid form");
            })

            $(document).on("click","#addPerilaku",function(){
                var el="";

                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Perilaku Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formAddPerilaku">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Perilaku Kerja</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="remote-data-perilaku" name="perilaku">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nilai" placeholder="Nilai">'+
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

                $(".remote-data-perilaku").select2({
                    placeholder: "Cari Perilaku Kerja",
                    ajax: {
                        url: "{{URL::to('home/data/list-perilaku-by-skp')}}/"+skp_id,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params, // search term
                                page_limit: 50,
                                type:"Add"
                            };
                        },
                        results: function (data, page){
                            return {
                                results: data
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

            $(document).on("submit","#formAddPerilaku",function(e){
                var data = new FormData(this);
                data.append("skp",skp_id);
                if($("#formAddPerilaku")[0].checkValidity()) {
                    //updateAllMessageForms();
                    e.preventDefault();
                    $.ajax({
                        url         : "{{URL::to('home/data/list-perilaku-by-skp')}}",
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
                                showPerilakuKerja();
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

            $(document).on("click","a.hapusperilaku",function(){
                var ids=$(this).attr("kode");

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
                            url:"{{URL::to('home/data/list-perilaku-by-skp')}}/"+ids,
                            type:"DELETE",
                            success:function(result){
                                if(result.success=true){
                                    swal("Deleted!", result.pesan, "success");
                                    showPerilakuKerja();
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
            
            $(document).on("click",".editperilaku",function(){
                var kode=$(this).attr("kode");
                var idper=$(this).attr("idper");
                var namaper=$(this).attr("namaper");
                var nilai=$(this).attr("nilai");
                var el="";

                el+='<div id="modalHistory" class="modal fade" data-backdrop="static" data-keyboard="false">'+
                    '<div class="modal-dialog">'+
                        '<div class="modal-content">'+
                            '<div class="modal-header bg-info">'+
                                '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                '<h6 class="modal-title">Add Perilaku Kerja</h6>'+
                            '</div>'+

                            '<form class="form-horizontal" onsubmit="return false;" id="formAddPerilaku">'+
                                '<div class="modal-body">'+
                                    '<div id="pesanTarget"></div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Perilaku Kerja</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input type="hidden" name="kode" value="'+kode+'">'+
                                            '<input class="remote-data-perilaku" name="perilaku" value="'+idper+'">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group">'+
                                        '<label class="control-label col-lg-4">Nilai</label>'+
                                        '<div class="col-lg-8">'+
                                            '<input class="form-control" name="nilai" placeholder="Nilai" value="'+nilai+'">'+
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

                $(".remote-data-perilaku").select2({
                    initSelection: function(element, callback) {
                        callback({id: idper, text: namaper });
                    },
                    ajax: {
                        url: "{{URL::to('home/data/list-perilaku-by-skp')}}/"+skp_id,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params, // search term
                                page_limit: 50,
                                type:"Add"
                            };
                        },
                        results: function (data, page){
                            return {
                                results: data
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


            showFormSkp();
        })
    </script>
@stop