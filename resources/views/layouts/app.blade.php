<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title><?php echo Config::get('constants.APP_NAME') ?>@yield('title')</title>

    <!-- Styles -->
    {{-- <link href="/css/app.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}"/>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <?php header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache'); ?>


</head>
<body>
<div class="c-loader loader-hide">
    <span class="loader-box">
        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </span>
</div>
<div id="app" class="top-navbar layout-login-dashboard">

    <nav class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand visible-xs" href="{{ url('dashboard') }}">
                    <?php echo Config::get('constants.APP_NAME') ?>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav hidden-xs">
                    <li>

                        <a  href="{{!empty(userRole()) ? url('dashboard'): url('dashboard') }} ">
                            <?php echo Config::get('constants.APP_NAME') ?>
                        </a>
                    </li>

                    @if(!empty(userRole()) && userRole() == \Config::get('constants.ROLE.SUPERADMIN'))

                        <li>
                            <a  href="{{ url('users-work-time') }}">
                                {{'Work Details'}}
                            </a>

                        </li>
                        <li>
                            <a  href="{{ url('users-current-task') }}">
                                {{'Current Task'}}
                            </a>

                        </li>
                    @endif

                    @if(!empty(userRole()))
                        <li>
                            <a  href="{{ url('users-current-task-time') }}">
                                {{'Send Report'}}
                            </a>

                        </li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->

                    @if (empty(authUser()->name))
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('userregister') }}">Sign Up</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ authUser()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <li>
                                    <a href="{{ route('changePassword') }}">
                                        Change Password
                                    </a>
                                </li>
                                <?php //pr(count(getCompanyUserData())); die; ?>
                                @if(count(getCompanyUserData()) > 1)
                                    @if(!empty(userRole()))
                                        <li>
                                            <a href="{{ route('companies') }}">
                                                Change company
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                @if(!empty(userRole()) && userRole() == \Config::get('constants.ROLE.SUPERADMIN'))
                                    <li>
                                        <a href="{{ route('settings') }}">
                                            Settings
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ route('userLogout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('userLogout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>

                            </ul>
                        </li>
                    @endif
                </ul>
                <?php //echo  Request::segment(1) ?>
                @if(Request::segment(1) != 'login' && Auth::check())
                <div class="navbar-form navbar-right">
                <div class="form-group ">
                    <div class="input-group input-group-sm" id="autoCompleteSearchBox">
                        <input type="text" id="search" class="form-control" placeholder="Search...">
                        {{ csrf_field() }}
                        <span class="input-group-btn"><button type="submit" id="searchButton" class="btn btn-default"><i
                                        class="fa fa-search"></i></button></span>
                    </div>

                </div>

                </div>
                @endif

                {{--   <ul class="nav navbar-nav navbar-right">
                       <!-- Authentication Links -->

                       <li class="dropdown open">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">
                               User List <span class="caret"></span>
                           </a>

                           <ul class="dropdown-menu" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                               <li>
                                   <a href="http://localhost/project_management/public/userLogout" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                       a
                                   </a>

                               </li>
                           </ul>
                       </li>
                   </ul>--}}
            </div>
        </div>
    </nav>
</div>
@yield('content')


        <!-- Scripts -->
<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/moment.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/autosize.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/progressbar.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/toast.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/perfectscroll.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/daterangepicker.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/responsive-paginate.js') }}"></script>
<script src="{{ URL::asset('assets/js/main.js') }}"></script>

{{--<script src="http://momentjs.com/downloads/moment.js"></script>--}}
<script src="http://momentjs.com/downloads/moment-with-locales.js"></script>


@yield('scripts')
    @include('js-helper.search-user-task')
</body>
</html>
