@extends('layouts.limitless')

@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title">Laporan Nilai SKP</h6>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label">Sasaran Kerja</label>
                <select class="form-control" name="sasaran" id="sasaran">
                    <option value="">--Pilih Sasaran Kerja--</option>
                    @foreach($sasaran as $row)
                        <option value="{{$row->id}}">{{$row->nama_sasaran}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="icon-folder-search"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>
@stop