@extends('layouts.limitless')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title">{{$sasaran->nama_sasaran}}</h6>
            <div class="heading-elements">
                <button type="button" class="btn btn-link heading-btn text-semibold">
                    <i class="icon-calendar3 position-left"></i> <span>{{date('d F Y',strtotime($sasaran->start_periode))}} - {{date('d F Y',strtotime($sasaran->end_periode))}}</span> <b class="caret"></b>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-xlg text-nowrap">
                <tbody>
                    <tr>
                        <td class="col-md-3">
                            <div class="media-left media-middle">
                                <a href="#" class="btn border-indigo-400 text-indigo-400 btn-flat btn-rounded btn-xs btn-icon"><i class="icon-user-tie"></i></a>
                            </div>

                            <div class="media-left">
                                <h5 class="text-semibold no-margin">{{count($pegawai)}}</h5>
                                <span class="text-muted"><span class="status-mark border-success position-left"></span> Pegawai</span>
                            </div>
                        </td>

                        <td class="col-md-3">
                            <div class="media-left media-middle">
                                <a href="#" class="btn border-indigo-400 text-indigo-400 btn-flat btn-rounded btn-xs btn-icon"><i class="icon-certificate"></i></a>
                            </div>

                            <div class="media-left">
                                <h5 class="text-semibold no-margin">
                                    {{count($skp)}} <small class="display-block no-margin">Form SKP</small>
                                </h5>
                            </div>
                        </td>

                        <td class="col-md-3">
                            <div class="media-left media-middle">
                                <a href="#" class="btn border-indigo-400 text-indigo-400 btn-flat btn-rounded btn-xs btn-icon"><i class="icon-spinner11"></i></a>
                            </div>

                            <div class="media-left">
                                <h5 class="text-semibold no-margin">
                                    {{count($pengukuran)}} <small class="display-block no-margin">Realisasi</small>
                                </h5>
                            </div>
                        </td>

                        <td class="text-right col-md-2">
                            <a href="{{URL::to('home/report')}}" class="btn bg-teal-400"><i class="icon-statistics position-left"></i> Report</a>
                        </td>
                    </tr>
                </tbody>
            </table>	
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Data Aktivitas Pegawai</h6>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px">Tanggal</th>
                            <th style="width: 300px;">Pegawai</th>
                            <th style="width: 300px;">Jam</th>
                            <th>Kegiatan</th>
                            <th>Hasil</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nilai as $row)
                            <tr>
                                <td class="text-center">
                                    <h6 class="no-margin">{{date('d',strtotime($row->tanggal))}} <small class="display-block text-size-small no-margin">{{date('F Y',strtotime($row->tanggal))}}</small></h6>
                                </td>
                                <td>
                                    <div class="media-left media-middle">
                                        @if(count($row->pegawai)>0)
                                            {{Html::image('uploads/pegawai/'.$row->pegawai->foto,'',array('class'=>'img-responsive'))}}
                                        @endif
                                    </div>

                                    <div class="media-body">
                                        <a href="#" class="display-inline-block text-default text-semibold letter-icon-title">{{$row->pegawai->nama_lengkap}}</a>
                                        @if(count($row->pegawai->jabatan)>0)
                                            <div class="text-muted text-size-small">{{$row->pegawai->jabatan[0]->nama_jabatan}}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <p>{{$row->dari_jam}} - {{$row->sampai_jam}}</p>
                                </td>
                                <td>
                                    {{$row->kegiatan}}
                                </td>
                                <td>
                                    {{$row->hasil}}
                                </td>
                                <td>
                                    {{strip_tags($row->keterangan)}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{$nilai->links()}}

            <hr>

            @if(count($bawahan)>0)
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">Ativity Pegawai</h6>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th></th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=0;?>
                                @foreach($bawahan as $row)
                                    <?php $no++;?>
                                    <tr>
                                        <td>{{$no}}</td>
                                        <td>
                                            <div class="media-left media-middle">
                                                {{Html::image('uploads/pegawai/'.$row->foto,'',array('class'=>'img-responsive'))}}
                                            </div>
                                        </td>
                                        <td>{{$row->nip}}</td>
                                        <td>{{$row->nama_lengkap}}</td>
                                        <td>
                                            @if(count($row->harian)>0)
                                                <span class="badge badge-primary">{{count($row->harian)}}</span>
                                                <span class="label label-flat border-danger text-danger-600">
                                                    Report Kegiatan
                                                </span>
                                            @else
                                                <span class="label label-flat border-grey text-grey-600">Tidak Ada Report</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{URL::to('home/'.$row->id.'/report-harian-belum-konfirmasi')}}">
                                                <i class="icon-search4"></i>
                                            </a>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    {{Html::script('limitless1/assets/js/plugins/visualization/d3/d3.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/visualization/d3/d3_tooltip.js')}}
	{{Html::script('limitless1/assets/js/plugins/forms/styling/switchery.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/forms/styling/uniform.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/ui/moment/moment.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/pickers/daterangepicker.js')}}

    <script>
        $(function(){

        })
    </script>
@stop
