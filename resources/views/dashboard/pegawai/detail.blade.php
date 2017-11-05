@extends('layouts.limitless')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            
        </div>

        <div class="col-lg-3">
            <!-- User thumbnail -->
            <div class="thumbnail">
                <div class="thumb thumb-rounded thumb-slide">
                    {{Html::image('uploads/pegawai/'.$pegawai->foto,'',array('class'=>'img-responsive'))}}
                    <div class="caption">
                        <span>
                            <a href="#" class="btn bg-success-400 btn-icon btn-xs" data-popup="lightbox"><i class="icon-plus2"></i></a>
                            <a href="user_pages_profile.html" class="btn bg-success-400 btn-icon btn-xs"><i class="icon-link"></i></a>
                        </span>
                    </div>
                </div>
            
                <div class="caption text-center">
                    <h6 class="text-semibold no-margin">{{$pegawai->nama_lengkap}} <small class="display-block">{{$pegawai->alamat}}</small></h6>
                </div>
            </div>
            <!-- /user thumbnail -->

            <!-- Navigation -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Navigation</h6>
                    <div class="heading-elements">
                        <a href="#" class="heading-text">See all &rarr;</a>
                    </div>
                </div>

                <div class="list-group list-group-borderless no-padding-top">
                    <a href="#" class="list-group-item"><i class="icon-user"></i> My profile</a>
                    <a href="#" class="list-group-item"><i class="icon-cash3"></i> Balance</a>
                    <a href="#" class="list-group-item"><i class="icon-tree7"></i> Connections <span class="badge bg-danger pull-right">29</span></a>
                    <a href="#" class="list-group-item"><i class="icon-users"></i> Friends</a>
                    <div class="list-group-divider"></div>
                    <a href="#" class="list-group-item"><i class="icon-calendar3"></i> Events <span class="badge bg-teal-400 pull-right">48</span></a>
                    <a href="#" class="list-group-item"><i class="icon-cog3"></i> Account settings</a>
                </div>
            </div>
            <!-- /navigation -->
        </div>
    </div>
@stop