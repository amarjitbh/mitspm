<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo Config::get('constants.APP_NAME') ?>@yield('title')</title>
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
</head>

<body>
{{--<div class="c-loader">
    <span class="loader-box">
        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
<span class="sr-only">Loading...</span>
    </span>
</div>--}}
<div class="top-navbar">
    <nav class="navbar navbar-default navbar-fixed-top navbar-custom pt5">
        <div class="container-fluid plr10">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand hide" href="#">Brand</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">
                            <i class="fa fa-star"></i>
                            @if(isset($data->name))
                                {{$data->name}}
                            @endif
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            @if(count($projects)>0)
                                @foreach($projects as $pro)
                                    <li>
                                        <a href="{{route('getAllBoardsAndTaskProjectLabel',$pro->project_id)}}">
                                            {{$pro->name}}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </li>

                </ul>
                <ul class="nav navbar-nav">
                    <li class="dropdown">

                        <a href="{{route('dashboard')}}">Dashboard</a>
                    </li>
                </ul>


                <ul class="nav navbar-nav navbar-left">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" id="selected_user_name" data-toggle="dropdown" role="button" aria-expanded="false">

                        </a>
                        <ul class="dropdown-menu" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            {{--<select class="form-control" onchange="changeUser(this)">--}}
                            {{--<option value="{{$user_list->id}}"--}}
                              {{--if( $user_list->id == $userId){ echo 'selected';} else{  echo '';}--}}
                           {{--{{$user_list->name}}--}}

                       {{--</option>--}}
                            @if(is_object($usersList))
                                @foreach($usersList as $user_list)
                                    <li id="selectUserName">
                                        <a data-value="<?php if( $user_list->id == $userId){ echo $user_list->name; } ?>"
                                           @if($user_list->id == Auth::user()->id)
                                           href="{{route('board-detail',[$boardId])}}"
                                           @else
                                           href="{{route('board-detail',[$boardId,$user_list->id])}}"
                                                @endif
                                                >{{$user_list->name}}</a>
                                    </li>
                                @endforeach
                            @endif


                        </ul>
                    </li>
                </ul>


                <ul class="nav navbar-nav navbar-right">
                    <li class="hide"><a href=""><i class="fa fa-bell-o"></i></a></li>
                    {{-- <li class="dropdown">
                         <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                            aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                         <ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                             <li><a id="logout-form"  href="{{ url('/logout') }}"
                                    onclick="event.preventDefault();
                                                      document.getElementById('logout-form').submit();">
                                     Logout
                                 </a> </li>
                         </ul>
                     </li>--}}

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ authUser()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <li>
                                <a href="{{ route('changePassword') }}"
                                        >
                                    Change Password
                                </a>
                                <a href="{{ route('userLogout') }}"
                                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('userLogout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="navbar-form navbar-right">
                    <div class="form-group">
                        <div class="input-group input-group-sm" id="autoCompleteSearchBox">
                            <input type="text" id="search" class="form-control" placeholder="Search...">
                            {{ csrf_field() }}
                            <span class="input-group-btn"><button type="submit" id="searchButton" class="btn btn-default"><i
                                            class="fa fa-search"></i></button></span>
                        </div>

                    </div>

                </div>
                <div class="">
                    <div class="clearfix"></div>
                    {{--<ul class="nav navbar-nav">--}}
                        {{--<li class="hidden-xs">--}}

                            {{--<a id="menu-toggle1" href="javascript:void(0);" class=" btn-menu toggle">--}}
                                {{--<i class="fa fa-bars"></i>--}}
                            {{--</a>--}}

                        {{--</li>--}}
                        {{--<li><a href="#">Link 1</a></li>--}}
                        {{--<li><a href="#">Link 2</a></li>--}}
                        {{--<li><a href="#">Link 3</a></li>--}}
                        {{--<li class="dropdown">--}}
                            {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"--}}
                               {{--aria-haspopup="true" aria-expanded="false">--}}
                                {{--@if(isset($data->project_board_name))--}}
                                    {{--{{$data->project_board_name}}--}}
                                {{--@endif--}}
                                {{--<span class="caret"></span>--}}
                            {{--</a>--}}
                            {{--<ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">--}}
                                {{--@if(!empty($boards))--}}
                                    {{--@foreach($boards as $board)--}}
                                        {{--<li>--}}
                                            {{--<a href="{{route('board-detail',$board['project_board_id'])}}">--}}
                                                {{--{!! $board['project_board_name'] !!}--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                    {{--@endforeach--}}
                                {{--@endif--}}
                            {{--</ul>--}}
                        {{--</li>--}}
                    {{--</ul>--}}



                </div>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</div>

<div id="wrapper" class="h-100p" style="padding-left:0;">
    <!-- Sidebar -->

    @yield('content')


</div>


<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/moment.js') }}"></script>
{{--<link href="http://demo.expertphp.in/css/jquery.ui.autocomplete.css" rel="stylesheet">--}}
{{--<script src="http://demo.expertphp.in/js/jquery.js"></script>--}}
{{--<script src="http://demo.expertphp.in/js/jquery-ui.min.js"></script>--}}
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

@yield('scripts')
@include('js-helper.search-user-task')
<script>

    /*Function to extract class name string*/
    function getClassStartsWith(t, n) {
        var r = $.grep(t.split(" "), function (t, r) {
            return 0 === t.indexOf(n);
        }).join();
        return r || !1;
    }
    /* /Function to extract class name string*/

    $('.priority-status .btn-group .dropdown .dropdown-menu a').click(function (e) {
        e.stopPropagation();
        $(this).parent('li').siblings().find('a.active').removeClass('active');
        $(e.target).addClass('active');

        var $element = $(e.target).find('.status-box');
        var classes = $element[0].className;

        var $bgValue = getClassStartsWith(classes, 'bg-');

        var panelStatusIcon = $(e.target).parents('.btn-group').eq(0).find('.dropdown-toggle .status-box');

        var $parElement = panelStatusIcon;
        var classes = $parElement[0].className;
        var $parBgValue = getClassStartsWith(classes, 'bg-');

        panelStatusIcon.removeClass($parBgValue).addClass($bgValue);

    });
    $('.priority-status .btn-group .dropdown').on('hide.bs.dropdown', function () {
        //  alert('dropmenu about to hidden');
    })

    function changeUser(ths){
        if(ths.value == '{{Auth::user()->id}}'){

            window.location = '{{route('board-detail',[$boardId])}}';
        }else {
            window.location = '{{route('board-detail',[$boardId])}}/' + ths.value;
        }
    }

    $('docuement').ready(function(){

        $('#selectUserName a').each(function(ind,val){
            var name = $(this).attr('data-value');
            if(name != '') {
                $('#selected_user_name').html(name + '  <span class="caret"></span>');
            }
        })
        /*name = ;*/
    });

</script>
</body>
</html>