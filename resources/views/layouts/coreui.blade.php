
<!--
 * Core - Open Source Bootstrap Admin Template
 * @version v1.0.1
 * @link http://core.io
 * Copyright (c) 2017 creativeLabs Łukasz Holeczek
 * @license MIT
-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Core - Open Source Bootstrap Admin Template">
	<meta name="author" content="Łukasz Holeczek">
	<meta name="keyword" content="">
	<link rel="shortcut icon" href="img/favicon.png">
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Icons -->
	{{Html::style('core/node_modules/font-awesome/css/font-awesome.min.css')}}
	{{Html::style('core/node_modules/simple-line-icons/css/simple-line-icons.css')}}
	<link src="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

	<!-- Main styles for this application -->
	{{Html::style('core/css/style.css')}}
	<!-- Styles required by this views -->

</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
	<header class="app-header navbar">
		<button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">☰</button>
		<a class="navbar-brand" href="#"></a>
		<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">☰</button>

		<ul class="nav navbar-nav d-md-down-none">
			{{--  <li class="nav-item px-3">
				<a class="nav-link" href="{{URL::to('home')}}">Dashboard</a>
			</li>
			<li class="nav-item px-3">
				<a class="nav-link" href="{{URL::to('home/users')}}">Users</a>
			</li>
			<li class="nav-item px-3">
				<a class="nav-link" href="{{URL::to('home/setting')}}">Settings</a>
			</li>  --}}
		</ul>
		<ul class="nav navbar-nav ml-auto">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					<img src="{{URL::asset('core/img/avatars/6.jpg')}}" class="img-avatar" alt="admin@bootstrapmaster.com">
					<span class="d-md-down-none">{{\Auth::user()->name}}</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<div class="dropdown-header text-center">
						<strong>Account</strong>
					</div>
					<a class="dropdown-item" href="#"><i class="fa fa-bell-o"></i> Updates<span class="badge badge-info">42</span></a>
					<a href="{{ route('logout') }}" class="dropdown-item"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        <i class="fa fa-lock"></i> Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
				</div>
			</li>
		</ul>
		<button class="navbar-toggler aside-menu-toggler" type="button">☰</button>

	</header>

	<div class="app-body" id="app">
		<div class="sidebar">
			<nav class="sidebar-nav">
				<ul class="nav">
					<li class="nav-item">
						<router-link to="/" class="nav-link">
							<i class="icon-speedometer"></i> Dashboard
						</router-link>
					</li>

					<li class="nav-title">
						UI Elements
					</li>
					<li class="nav-item nav-dropdown">
						<a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> Master</a>
						<ul class="nav-dropdown-items">
							<li class="nav-item">
								<router-link to="/pegawai" class="nav-link">
									<i class="icon-puzzle"></i> Pegawai
								</router-link>
							</li>
							<li class="nav-item">
								<router-link to="/sasaran-kerja" class="nav-link">
									<i class="icon-puzzle"></i> Sasaran Kerja
								</router-link>
							</li>
							<li class="nav-item">
								<router-link to="/perilaku-kerja" class="nav-link">
									<i class="icon-puzzle"></i> Perilaku Kerja
								</router-link>
							</li>
						</ul>
					</li>
					<li class="nav-item nav-dropdown">
						<a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> Setting</a>
						<ul class="nav-dropdown-items">
							<li class="nav-item">
								<router-link to="/setting/status" class="nav-link">
									<i class="icon-puzzle"></i> Status
								</router-link>
							</li>
							<li class="nav-item">
								<router-link to="/setting/golongan" class="nav-link">
									<i class="icon-puzzle"></i> Golongan
								</router-link>
							</li>
							<li class="nav-item">
								<router-link to="/setting/pangkat" class="nav-link">
									<i class="icon-puzzle"></i> Pangkat
								</router-link>
							</li>
						</ul>
					</li>

				</ul>
			</nav>
			<button class="sidebar-minimizer brand-minimizer" type="button"></button>
		</div>

		<!-- Main content -->
		<main class="main">

			<!-- Breadcrumb -->
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Home</li>
				<li class="breadcrumb-item"><a href="#">Admin</a></li>
				<li class="breadcrumb-item active">Dashboard</li>
			</ol>

			<div class="container-fluid" id="app">

				<div class="animated fadeIn">
					@yield('content')
				</div>

			</div>
			<!-- /.conainer-fluid -->
		</main>
	</div>

		<footer class="app-footer">
			<span><a href="http://core.io">Core</a> © 2017 creativeLabs.</span>
			<span class="ml-auto">Powered by <a href="http://core.io">Core</a></span>
		</footer>

		<!-- Bootstrap and necessary plugins -->
		<script src="{{ asset('js/app.js') }}"></script>
		{{Html::script('core/node_modules/jquery/dist/jquery.min.js')}}
		{{Html::script('core/node_modules/popper.js/dist/umd/popper.min.js')}}
		{{Html::script('core/node_modules/bootstrap/dist/js/bootstrap.min.js')}}
		{{Html::script('core/node_modules/pace-progress/pace.min.js')}}

		<!-- Plugins and scripts required by all views -->
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

		<!-- GenesisUI main scripts -->

		{{Html::script('core/js/app.js')}}

		<!-- Plugins and scripts required by this views -->

		<!-- Custom scripts required by this view -->
		{{Html::script('core/js/views/main.js')}}

		@yield('js')
	</body>
	</html>