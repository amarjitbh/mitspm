<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Config::get('constants.APP_NAME') ?>@yield('title')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}" />

</head>
<body class="bgLightGrey">
<?php //echo link_to_route('login', $title = 'Login', $parameters = [], $attributes = []); ?>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="commonContainer">
@yield('content')
</div>

<div class="footer" id="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="textgrey mt5"><?php echo Config::get('constants.SERVER_NAME') ?>
                </div>
            </div>

        </div>
    </div>
    <!--/.row-->
</div>
<!--/.container-->

<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/daterangepicker.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/autosize.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/progressbar.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/toast.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/nicescroll.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/daterangepicker.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/responsive-paginate.js') }}"></script>
<script src="{{ URL::asset('assets/js/main.js') }}"></script>

@yield('script')
</body>
</html>
