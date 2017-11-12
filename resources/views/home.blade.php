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
                            <th style="width: 50px">Due</th>
                            <th style="width: 300px;">User</th>
                            <th>Description</th>
                            <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <h6 class="no-margin">12 <small class="display-block text-size-small no-margin">hours</small></h6>
                            </td>
                            <td>
                                <div class="media-left media-middle">
                                    <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-xs">
                                        <span class="icon-add"></span>
                                    </a>
                                </div>

                                <div class="media-body">
                                    <a href="#" class="display-inline-block text-default text-semibold letter-icon-title">Annabelle Doney</a>
                                    <div class="text-muted text-size-small"><span class="status-mark border-blue position-left"></span> Active</div>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-default display-inline-block">
                                    <span class="text-semibold">[#1183] Workaround for OS X selects printing bug</span>
                                    <span class="display-block text-muted">Chrome fixed the bug several versions ago, thus rendering this...</span>
                                </a>
                            </td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="#"><i class="icon-undo"></i> Quick reply</a></li>
                                            <li><a href="#"><i class="icon-history"></i> Full history</a></li>
                                            <li class="divider"></li>
                                            <li><a href="#"><i class="icon-checkmark3 text-success"></i> Resolve issue</a></li>
                                            <li><a href="#"><i class="icon-cross2 text-danger"></i> Close issue</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
