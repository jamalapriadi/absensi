<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Aplikasi Kepegawaian</title>

	<!-- Global stylesheets -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	{{Html::style('limitless1/assets/css/icons/icomoon/styles.css')}}
	{{Html::style('limitless1/assets/css/minified/bootstrap.min.css')}}
	{{Html::style('limitless1/assets/css/minified/core.min.css')}}
	{{Html::style('limitless1/assets/css/minified/components.min.css')}}
	{{Html::style('limitless1/assets/css/minified/colors.min.css')}}
	{{Html::style('limitless1/assets/js/plugins/sweetalert/sweetalert.css')}}
	<!-- /global stylesheets -->

	<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	

	<!-- Core JS files -->
	{{--  <script src="{{ asset('js/app.js') }}"></script>  --}}
	{{Html::script('limitless1/assets/js/plugins/loaders/pace.min.js')}}
	{{Html::script('limitless1/assets/js/core/libraries/jquery.min.js')}}
	{{Html::script('limitless1/assets/js/core/libraries/bootstrap.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/loaders/blockui.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/ui/nicescroll.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/ui/drilldown.js')}}
	<!-- /core JS files -->

    <!--DATATABLE AND CKEDITOR -->
    {{Html::script('limitless1/ckeditor/ckeditor.js')}}

	<!-- Theme JS files -->
    {{Html::script('limitless1/assets/js/plugins/tables/datatables/datatables.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/tables/datatables/extensions/col_vis.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/forms/styling/uniform.min.js')}}
	{{Html::script('limitless1/assets/js/core/libraries/jquery_ui/interactions.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/forms/selects/select2.min.js')}}
    {{Html::script('limitless1/assets/js/plugins/notifications/pnotify.min.js')}}
	{{Html::script('limitless1/assets/js/plugins/sweetalert/sweetalert.min.js')}}

	<!-- Theme JS files -->
	{{Html::script('limitless1/assets/js/core/app.js')}}
	<!-- /theme JS files -->

	<style>
		th { font-size: 12px; }
		td { font-size: 11px; }
	</style>

	@yield('css')

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			{{--  <a class="navbar-brand" href="{{URL::to('home')}}">
            	<i class="icon-home2"></i>&nbsp;Homepage
			</a>  --}}

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li>
					<a class="sidebar-control sidebar-main-toggle hidden-xs">
						<i class="icon-paragraph-justify3"></i>
					</a>
				</li>
			</ul>

			<ul class="nav navbar-nav navbar-right">


				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
                        {{Html::image('uploads/pegawai/'.\Auth::user()->foto),'',array('class'=>'')}}
						<span>{{Auth::user()->name}}</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						{{--  <li><a href="{{URL::to('home/profile')}}"><i class="icon-user-plus"></i> My profile</a></li>  --}}
						<li class="divider"></li>
						<li>
							<a href="{{ route('logout') }}"
								onclick="event.preventDefault();
											document.getElementById('logout-form').submit();">
								<i class="icon-switch2"></i> Logout
							</a>

							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								{{ csrf_field() }}
							</form>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left">
									{{Html::image('uploads/pegawai/'.\Auth::user()->foto),'',array('class'=>'img-circle img-sm')}}
                                </a>
								<div class="media-body">
									<span class="media-heading text-semibold">{{\Auth::user()->name}}</span>
									<div class="text-size-mini text-muted">
										<i class="icon-pin text-size-small"></i> &nbsp;
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="#"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<ul class="navigation navigation-main navigation-accordion">
									<li class="{{ Request::path() == 'home' ? 'active' : '' }}">
                                        <a href="{{URL::to('home')}}">
                                        <i class="icon-home2"></i> <span>Dashboard</span></a>
                                    </li>
									@if(\Auth::user()->level=="admin")
										<li class="{{ Request::path() == 'home/master' ? 'active' : '' }}">
											<a href="{{URL::to('home/master')}}">
											<i class="icon-stack3"></i> <span>Master</span></a>
										</li>
										<li class="{{ Request::path() == 'home/pegawai' ? 'active' : '' }}">
											<a href="{{URL::to('home/pegawai')}}">
											<i class="icon-user-tie"></i> <span>Pegawai</span></a>
										</li>
										<li class="{{ Request::path() == 'home/users' ? 'active' : '' }}">
											<a href="{{URL::to('home/users')}}">
												<i class="icon-users4"></i> User
											</a>
										<li>
											<a href="#"><i class="icon-newspaper"></i> <span>Penilaian</span></a>
											<ul>
												<li class="{{ Request::path() == 'home/perilaku-kerja' ? 'active' : '' }}"><a href="{{URL::to('home/perilaku-kerja')}}">Perilaku Kerja</a></li>
												<li class="{{ Request::path() == 'home/sasaran-kerja' ? 'active' : '' }}"><a href="{{URL::to('home/sasaran-kerja')}}">Sasaran Kerja</a></li>
												<li class="{{ Request::path() == 'home/nilai-skp' ? 'active' : '' }}"><a href="{{URL::to('home/nilai-skp')}}">Nilai SKP</a></li>
											</ul>
										</li>
									@endif

									@if(\Auth::user()->level=="pegawai")
										<li class="{{ Request::path() == 'home/nilai-harian' ? 'active' : '' }}">
											<a href="{{URL::to('home/nilai-harian')}}">
											<i class="icon-reading"></i> <span>Kegiatan</span></a>
										</li>
										<li>
											<a href="#"><i class="icon-newspaper"></i> <span>Report</span></a>
											<ul>
												<li class="{{ Request::path() == 'home/report/kegiatan-harian' ? 'active' : '' }}"><a href="{{URL::to('home/report/kegiatan-harian')}}">Nilai Kegiatan</a></li>
												<li class="{{ Request::path() == 'home/report/nilai-skp' ? 'active' : '' }}"><a href="{{URL::to('home/report/nilai-skp')}}">Nilai SKP</a></li>
											</ul>
										</li>
									@endif
                                </ul>
							</ul>

							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><span class="text-semibold">{{$home}}</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="{{URL::to('sam/package')}}"><i class="icon-price-tag2 position-left"></i> {{$home}}</a></li>
							<li class="active">{{$title}}</li>
						</ul>
						
						<!--
						<ul class="breadcrumb-elements">
							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
						</ul>
						-->
					</div>
				</div>
				<!-- /page header -->


				<!-- Content area -->
				<div class="content">

                    @yield('content')
					<!-- Footer -->
					<div class="footer text-muted">
						&copy; {{date('Y')}}. <a href="{{URL::to('team-development')}}">INTRANET SALES MARKETING</a>
					</div>
					<!-- /footer -->

				</div>
				<!-      - /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->
    
	<script>
		$(function(){

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
                },
                preDrawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                }
            });
		})
	</script>

    @yield('js')

</body>
</html>
