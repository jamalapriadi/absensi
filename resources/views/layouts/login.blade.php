
<!--
 * CoreUI - Open Source Bootstrap Admin Template
 * @version v1.0.1
 * @link http://coreui.io
 * Copyright (c) 2017 creativeLabs Łukasz Holeczek
 * @license MIT
-->
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="CoreUI Bootstrap 4 Admin Template">
  <meta name="author" content="Lukasz Holeczek">
  <meta name="keyword" content="CoreUI Bootstrap 4 Admin Template">
  <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Pengadilan Negeri Tegal</title>

  <!-- Icons -->
  {{Html::style('core/node_modules/font-awesome/css/font-awesome.min.css')}}
  {{Html::style('core/node_modules/simple-line-icons/css/simple-line-icons.css')}}

  <!-- Main styles for this application -->
  {{Html::style('core/css/style.css')}}

  <!-- Styles required by this views -->

</head>

<body class="app flex-row align-items-center">
@yield('content')

  <!-- Bootstrap and necessary plugins -->
  {{Html::script('core/node_modules/jquery/dist/jquery.min.js')}}
  {{Html::script('core/node_modules/popper.js/dist/umd/popper.min.js')}}
  {{Html::script('node_modules/bootstrap/dist/js/bootstrap.min.js')}}

</body>
</html>