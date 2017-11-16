@extends('layouts.limitless')

@section('content')
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h6 class="panel-title">Add Form SKP</h6>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" onsubmit="return false" id="formSkp">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Tanggal</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="tanggal">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Pegawai</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="tanggal">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Pejabat Penilai</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="tanggal">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Atasan Pejabat Penilai</label>
                    <div class="col-lg-8">
                        <input class="form-control" name="tanggal">
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop