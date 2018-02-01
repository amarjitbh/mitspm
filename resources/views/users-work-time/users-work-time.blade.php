@extends('layouts.app')
@section('title', ' - Work Time')

@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            WORK DETAILS
                        </span>
                        <span class="pull-right">

                            <a href="{{route('projects.create')}}"  class="btn btn-success btn-create-project btn-xs">Create New Project</a>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container alert-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="padding0">
                        {{--Flash Message--}}
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="breadcrumb-bar">

            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="active">Work Time</li>
                    </ol>
                </div>
            </div>

        </div>
        <div class="panel panel-default dashboard-panel-filter manage-project-add-people">

            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-6">

                            <?php if(empty($userId)){ ?>
                                <div id="reportrange" class="selectbox pull-left font12">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <b class="caret"></b>
                                </div>
                           {{-- <input class="form-control" type="text" name="date" id="datetimepicker1" value="{{ !empty($dateOne)? $dateOne : ''}}">
                            <span class="input-group-addon">to</span>
                                <input class="form-control" type="text" name="date_1" id="datetimepicker2" value="{{ !empty($dateOne)? $dateTwo : ''}}">--}}



                <span class="input-group-btn">
                    <button class="btn btn-default btn-success font12" type="button" onclick="getAccordingDate()">Filter</button>
                </span>
                                <?php }else{

                                    echo 'User-Task';
                                } ?>

                    </div>

                </div>


                <div class="clearfix"></div>
            </div>
            <div class="panel-body">

                <div class="row  row-ht-equal col-no-flex mt15">

                        <?php
                        if(!empty($finalData)){

                        foreach($finalData as $ind => $use){
                            $myTime = '';
                           /*foreach($logDAta as $logTime){
                               if($use['user_id'] == $logTime['user_id']){
                                   $myTime =  $logTime['timeSum'];
                               }
                           }*/
                        $count = count($use['usersTaks']);
                        $times = array_column($use['usersTaks'],'dayTotal');
                        $total = '';
                        foreach($times as $t) {

                            if(!empty($t)){
                                $total += toSeconds($t);
                            }
                        }
                        ?>
                            <div class=" {{($userId) ? 'col-sm-12' : 'col-sm-4'}} mb15 paginate-size-sm">
                        <div class="user-detail-card">
                            <div class="user-header">
                                <span class="user-name">{{$use['name']}}</span>
                                <span class="total-time text-right">

                                    {{$myTime}}{{--=={{toTime($total)}}--}}
                                    @if($use['user_id'] == $finalTime[$ind]['user_id'])
                                        @if(!empty($dateOne) && !empty($dateTwo))
                                            {{$finalTime[$ind]['userTime']}}
                                            @else
                                            {{$finalTime[$ind]['timeSum']}}
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <?php  ?>
                            <div class="task-list">
                                <ul>

                                @foreach($use['usersTaks'] as $tasks)
                                    <?php $times[] = $tasks['dayTotal']; /*pr($tasks['dayTotal']);*/  ?>
                                        <li>
                                            <div class="task-list-items">
                                                {{--<span class="task-name" data-toggle="modal" data-id="{{$tasks['task_id']}}" data-target="#view-task-detail">{{$tasks['subject']}}</span>--}}
                                                <span class="task-name" data-toggle="modal" data-user-id="{{$use['user_id']}}" data-id="{{$tasks['task_id']}}" onclick="getData('{{$tasks['task_id']}}','{{$use['user_id']}}');">{{$tasks['subject']}}</span>
                                                <span class="task-time text-right">
                                                    <?php
                                                    if(empty($dateOne) && empty($dateTwo) && !empty($tasks['logTime'])){
                                                            echo $tasks['logTime'];
                                                        }else{
                                                            echo $tasks['dayTotal'];
                                                        }
                                                    ?>
                                                </span>
                                            </div>
                                        </li>

                                @endforeach
                                @if(empty($userId))
                                    @if($count >= 5)
                                        <li class="text-center p0 read-more">
                                            @if(!empty($dateOne) && !empty($dateTwo))
                                                <a class="btn-block btn" href="{{route('users-work-time').'?user_id='.$use['user_id'].'&dateOne='.$dateOne.'&dateTwo='.$dateTwo}}">Read More</a>
                                            @else
                                                <a class="btn-block btn" href="{{route('users-work-time').'?user_id='.$use['user_id']}}">Read More</a>
                                            @endif
                                        </li>

                                    @endif
                                @endif
                                </ul>
                            </div>

                        </div>


                            {{--<div class="text-right">
                                @if(!empty($singleData['prev_page_url']))
                                    <a href="{{$singleData['prev_page_url']}}&user_id={{$userId}}">Prev</a>
                                @endif

                                    @if(!empty($singleData['next_page_url']))
                                        <a href="{{$singleData['next_page_url']}}&user_id={{$userId}}">Next</a>
                                    @endif
                            </div>--}}

                                <?php

                                    if (!empty($userId)) {
                                        echo $newData->appends(array('user_id' => $userId,'dateOne' => $dateOne,'dateTwo'=>$dateTwo))->render();
                                    }
                                ?>


                            </div>
                        <?php } } else { ?>

                            <div class="col-sm-12">
                                <p class="font12 mb15">No Record Found</p>
                            </div>
                       <?php } ?>
                </div>
            </div>
        </div>
    </div>
    @include('users-work-time.task-detail-modal')
@endsection

@section('scripts')

<script type="text/javascript">

    function getAccordingDate(){

        var myDate = $('#reportrange').text();
        var newDate=myDate.split('-');
        var dateOne = $.trim(newDate[0]);
        var dateTwo = $.trim(newDate[1]);
        window.location = '?dateOne='+dateOne+'&dateTwo='+dateTwo;
    }

    $(function() {
        var start = moment();
        var end = moment();
        var findComma = /,/g;

        var date_one = '{{!empty($dateOne) ? $dateOne : ''}}';
        date_one = date_one.replace(findComma, ', ');
        var date_two = '{{!empty($dateTwo) ? $dateTwo : ''}}';
        date_two = date_two.replace(findComma,', ');

        function cb(start, end) {
            $('#reportrange').on('show.daterangepicker, apply.daterangepicker', function (ev, picker) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            });
        }

        if (date_one && date_two) {
            $('#reportrange span').html(date_one + ' - ' + date_two);
        }
        else {
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    })

</script>
    @include('js-helper.user-work-task-detail')
@include('js-helper.close_modal_button')
@endsection