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
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">

			<ul class="nav navbar-nav navbar-right">


				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="#" alt="Profile">
						<span>{{Auth::user()->name}}</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="{{URL::to('home/profile')}}"><i class="icon-user-plus"></i> My profile</a></li>
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
            <div class="col-sm-9 kunjungan">
                <div class="kunjungan-id">Kunjungan 1</div>
                <div>Start :</div>
                <div>Sabtu, 01 Oktober 2016</div>
                <div>End :</div>
                <div>Kamis, 20 Oktober 2016</div>
                <div><a class="white" href="http://[::1]/bjb/home/session/4">Go &#9654;</a></div>
            </div>
        </div>
    </div>

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
