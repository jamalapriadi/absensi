@extends('layouts.limitless')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Report SKP</h6>
            <div class="heading-elements">
                <a class="btn btn-primary" href="{{URL::to('home/'.$nilai->id.'/export-xls')}}">
                    <i class="icon-file-excel"></i>
                    Export Excel
                </a>

                <a class="btn btn-success" href="">
                    <i class="icon-file-pdf"></i>
                    Export PDF
                </a>
            </div>
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
                    {{--  <li id="tabPerilaku"><a href="#right-tab4" data-toggle="tab">Penilaian</a></li>  --}}
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="right-tab1">
                        <div id="divFormSkp"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-highlight text-left">
                    <li class="active" id="tabFormSkp"><a href="#right-tab2" data-toggle="tab">Pengukuran</a></li>
                    {{--  <li id="tabPerilaku"><a href="#right-tab4" data-toggle="tab">Penilaian</a></li>  --}}
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="right-tab2">
                        <div id="divFormSkpRealisasi"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-flat">
        <div class="panel-body">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-highlight text-left">
                    <li class="active" id="tabPengukuran"><a href="#right-tab3" data-toggle="tab">Perilaku Kerja</a></li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="right-tab3">
                        <div id="divPerilakuKerja"></div>
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
                                                '<td class="text-center">'+d.nilai_pencapaian+'</td>';
                                            })
                                        }else{
                                            el+="<td colspan='6' class='text-center'>Tidak ada Data</td>";
                                        }
                                    el+='</tr>';
                                })
                                el+="<tr>"+
                                    '<td></td>'+
                                    '<td colspan="8"><strong>Tugas Tambahan Kreativitas</strong></td>'+
                                '</tr>';
                                if(result.nilai.tambahan.length>0){
                                    var n=0;
                                    $.each(result.nilai.tambahan,function(a,b){
                                        n++;
                                        el+="<tr>"+
                                            "<td>"+n+"</td>"+
                                            "<td colspan='7'>"+b.nama+"</td>"+
                                            "<td class='text-center'>"+b.nilai+"</td>"+
                                        "</tr>";
                                    })
                                }
                                el+="<tr>"+
                                    '<td colspan="8" class="text-center"><strong>Nilai Capaian SKP</strong></td>'+
                                    '<td colspan="2" class="text-center">'+result.nilai.id+'</td>'+
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
                                    "</tr>";
                                })
                            }
                        el+='</table>';

                        $("#divPerilakuKerja").empty().html(el);
                    },
                    error:function(){
                        $("#divPerilakuKerja").empty().html("<div class='alert alert-danger'>Failed load data</div>");
                    }
                })
            }



            showFormSkp();
            showFormSkpRealisasi();
            showPerilakuKerja();
        })
    </script>
@stop