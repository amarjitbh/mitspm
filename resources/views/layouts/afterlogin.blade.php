<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo Config::get('constants.APP_NAME') ?>@yield('title')</title>
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
    <style>
        .navbar {
            min-height: 38px;
        }
    </style>
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

                        <a href="{{route('dashboard')}}"><i class="fa fa-star"></i> {{getCompanyName()}}</a>
                    </li>

                </ul>



                <ul class="nav navbar-nav navbar-left">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">
                            @if(isset($data->name))
                                {{$data->name}}
                            @endif
                            <span class="caret"></span>
                        </a>
                        <input type="hidden" id="searchProjectId" value="@if(isset($data->name)){{$data->project_id}}@endif">
                        <ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            @if(count($projects)>0)
                                @foreach($projects as $pro)
                                    <li>
                                        <a href="{{route('board-detail',$pro->project_board_id)}}">
                                            {{$pro->name}}
                                        </a>

                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    {{--<li><a href="#">Link 1</a></li>
                    <li><a href="#">Link 2</a></li>
                    <li><a href="#">Link 3</a></li>--}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false">
                            @if(isset($data->project_board_name))
                                {{$data->project_board_name}}
                            @endif
                            <span class="caret"></span>
                        </a>
                        <input type="hidden" id="searchProjectBoardId" value="@if(isset($data->project_board_id)){{$data->project_board_id}}@endif">
                        <ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            @if(!empty($boards))
                                @foreach($boards as $board)
                                    <li>
                                        <a href="{{route('board-detail',$board['project_board_id'])}}">
                                            {!! $board['project_board_name'] !!}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" id="selected_user_name" data-toggle="dropdown" role="button" aria-expanded="false">

                        </a>
                        <ul class="dropdown-menu" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            {{--<select class="form-control" onchange="changeUser(this)">--}}
                            {{--<option value="{{$user_list->id}}"
                              if( $user_list->id == $userId){ echo 'selected';} else{  echo '';}
                           {{$user_list->name}}

                       </option>--}}
                            @if(is_object($usersList))

                                <li id="selectUserName">
                                    <a data-value="All Users"
                                       href="{{route('board-detail',[$boardId])}}" >All Users</a>

                                </li>

                                @foreach($usersList as $user_list)
                                    <li id="selectUserName">
                                        <a data-value="<?php
                                        if(!empty($allUsers)){
                                            echo 'All Users';
                                        }
                                        else if( $user_list->id == $userId){
                                            echo $user_list->name;
                                        } ?>"
                                           href="{{route('board-detail',[$boardId,$user_list->id])}}"

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
                <div class="visible-xs">
                    <div class="clearfix"></div>
                    <ul class="nav navbar-nav">
                        <li class="hidden-xs">

                            <a id="menu-toggle1" href="javascript:void(0);" class=" btn-menu toggle">
                                <i class="fa fa-bars"></i>
                            </a>

                        </li>

                    </ul>



                </div>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</div>

<div id="wrapper" class="h-100p">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <nav id="spy" class="sidebar-nav nav">
            <ul class="p0">

                <li>
                    <a href="{{route('board-detail',$boardId)}}" data-scroll class="font12 {{isActiveRoute('board-detail')}}">
                        <span class="fa fa-folder font12"></span> Board Work
                    </a>
                </li>
                <li>
                    <a href="{{route('my-tasks',$boardId)}}" data-scroll class="font12 {{isActiveRoute('my-tasks')}}">
                        <span class="fa fa-tasks font12"></span> My Tasks
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" id="createTask" data-scroll class="font12">
                        <span class="fa fa-plus font12"></span> Create Task
                    </a>
                </li>
            </ul>
            <?php $showName = ColumnName($boardId);
            $ColumnName = fetchColumnsName($boardId);
            ?>
            <ul class="p0  hasSubMenu columnNameList">
                <li>
                    <a href="javascript:void(0);" data-toggle="collapse" data-target="#sub1" aria-labelledby="false" class="font12">
                        <span class="fa fa-columns font12"></span> All Columns <span class="caret pull-right"></span>
                    </a>

                    <ul  id="sub1" role="menu" class="pl0 subMenuList collapse in">
                        @if(!empty($ColumnName))
                            @foreach($ColumnName as $columnName)
                                <?php if (in_array($columnName['project_board_column_id'], $showName)) {
                                    $status = 'show';
                                    $class = 'active-column-select';
                                } else {
                                    $status = 'hide';
                                    $class = '';
                                }?>
                                <li>
                                    <a id="{{$columnName['project_board_column_id']}}" href="javascript:void(0);" data-scroll
                                       class="showHideColumn font12 {{$class}}"
                                       data-column-name="{{ucwords($columnName['column_name'])}}"
                                       data-column-id="{{$columnName['project_board_column_id']}}" data-status="{{$status}}"
                                       onclick="showHideColumn({{$columnName['project_board_column_id']}})">
                                        {{ucwords($columnName['column_name'])}}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
            </ul>
            <ul class="p0">
                <li>
                    <a href="#anch4" data-scroll class="font12">
                        <span class="fa fa-history font12"></span> Project History
                    </a>
                </li>
                {{--only admin and super admin can add columns--}}
                @if(\Config::get('constants.ROLE.SUPERADMIN')==Session::get('user_role')
                || \Config::get('constants.ROLE.ADMIN')==Session::get('user_role')
                || $UserIsAdmin ==1
                )
                    <li>
                        <a href="javascript:void(0);" onclick="AddNewColumn()" data-scroll class="font12">
                            <span class="icon-column-add font12"></span> Add Column
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    @yield('content')


</div>



<script src="{{ URL::asset('assets/js/vendor/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendor/moment.js') }}"></script>
{{--<link href="http://demo.expertphp.in/css/jquery.ui.autocomplete.css" rel="stylesheet">
<script src="http://demo.expertphp.in/js/jquery.js"></script>
<script src="http://demo.expertphp.in/js/jquery-ui.min.js"></script>--}}
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

    $('#search').bind('keypress', function(e) {
        var search_value =  $('#search').val();
        if(e.keyCode==13 && search_value != '' ){

            var search_project_id = ($('#searchProjectId').val() !== undefined) ?  +$('#searchProjectId').val()  : '';

            var  search_board_id = ($('#searchProjectBoardId').val() !== undefined) ?  +$('#searchProjectBoardId').val() : '';

            if(search_value != '') {

                var url =   '{{ route('search-task') }}?project_id=' + search_project_id  +'&board_id='+ search_board_id +'&keyword=' + search_value;
                window.location = url;
            }
        }
    });

    $(function()
    {
        src = "{{ route('search') }}";
        search_project_id = $('#searchProjectId').val();
        search_board_id = $('#searchProjectBoardId').val();
        $("#search").autocomplete({

            source: function(request, response) {
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {term : request.term, search_project_id : search_project_id, search_board_id : search_board_id},
                    success: function(data) {
                        if(data.board_id != ''){
                            response(data);

                        }
                        else{
                            var result = [
                                {
                                    label: 'No matches found',
                                    board_id: ''
                                }
                            ];
                            response(result);
                            $('#autoCompleteSearchBox').find('.ui-menu-item').css('pointer-events','none');

                        }
                    }
                });
            },
            autoFocus: false,
            appendTo: "#autoCompleteSearchBox",
            minLength: 2,
            select: function(request, response) {
                $.each(response,function(ind,val){

                    if (val.board_id > 0) {
                        window.location = '{{ route('search-task') }}?project_id='+val.project_id+'&board_id='+val.board_id+'&keyword='+val.value;
                    } else {
                        setTimeout(function() {
                                    $('body').find('#search').val('')
                                }, 100
                        );
                    }
                });
            }
        });
    });

    
    $('#searchButton').click(function(){
        var search_value =  $('#search').val();
        var search_project_id = ($('#searchProjectId').val() !== undefined) ? $('#searchProjectId').val() : '';

        var  search_board_id = ($('#searchProjectBoardId').val() !== undefined) ? $('#searchProjectBoardId').val() : '';

        if(search_value != '') {

            var url =   '{{ route('search-task') }}?project_id=' + search_project_id  +'&board_id='+ search_board_id +'&keyword=' + search_value;

            window.location = url;
        }
    });
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