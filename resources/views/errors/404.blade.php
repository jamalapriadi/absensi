
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

    {{Html::script('limitless1/assets/js/core/app.js')}}

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.html"><img src="assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container login-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->
				<div class="content">

					<!-- Error wrapper -->
					<div class="container-fluid text-center">
						<h1 class="error-title">404</h1>
						<h6 class="text-semibold content-group">Oops, Page Not Found!</h6>

						<div class="row">
							<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3">
								<form action="#" class="main-search">
									

									<div class="row">
										<div class="col-sm-12">
											<a href="{{URL::to('home')}}" class="btn btn-primary btn-block content-group"><i class="icon-circle-left2 position-left"></i> Go to dashboard</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- /error wrapper -->


				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
