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
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div id="pesan"></div>

                        @foreach($sasaran as $row)
                        <div class="page-header content-group" style="border: 1px solid #ddd;margin-top:10px;">
                            <div class="page-header-content">
                                <div class="page-title">
                                    <h5>
                                        <i class="icon-arrow-left52 position-left"></i>
                                        <span class="text-semibold">{{$row->nama_sasaran}}</span> # Start Periode : {{$row->start_periode}} - End Periode : {{$row->end_periode}}
                                    </h5>
                                </div>

                                <div class="heading-elements">
                                    <a href="#" kode="{{$row->id}}" class="btn bg-teal-400 btn-icon btn-sm heading-btn sasaran">Go <i class="icon-play4"></i></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
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

            $(document).on("click",".sasaran",function(){
                var idsasaran=$(this).attr("kode");

                $.ajax({
                    url:"{{URL::to('home/data/save-session-sasaran')}}",
                    type:"POST",
                    data:"sasaran="+idsasaran,
                    beforeSend:function(){
                        $('#pesan').empty().html('<div class="alert alert-info"><i class="fa fa-spinner fa-2x fa-spin"></i>&nbsp;Please wait for a few minutes</div>');
                    },
                    success:function(data){
                        if(data.success==true){
                            $('#pesan').empty().html('<div class="alert alert-info">'+data.pesan+'</div>');
                            location.reload();
                        }else{
                            $('#pesan').empty().html('<div class="alert alert-danger"><h5>'+data.pesan+'</h5></div><pre>'+data.error+'</pre>');
                        }
                    },
                    error:function(){
                        $('#pesan').empty().html('<div class="alert alert-danger">Oppss Your request not send....</div>');
                    }
                })
            })
		})
	</script>

    @yield('js')

</body>
</html>
