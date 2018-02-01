@extends('layouts.app')
@section('title', ' - Current Task')

@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            User Today Task
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
                        <li class="active">Send Report</li>
                    </ol>
                </div>
            </div>

        </div>
        <div class="panel panel-default dashboard-panel-filter manage-project-add-people">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-6">
                        <input id="reportrange" class="selectbox pull-left font12">
                        <span class="input-group-btn">
                        <button class="btn btn-default btn-success font12" type="button" onclick="getAccordingDate()">
                            Filter
                        </button>
                    </span>

                    </div>

                </div>


                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="row  row-ht-equal col-no-flex mt15">
                    @if(!empty($final))

                                @foreach($final as $projectName)
                            <div class="col-sm-4 mb15">
                                <div class="user-detail-card">
                                    <div class="user-header">

                                        <span class="task-name user-name" id="projectName{{$projectName['project_id']}}"> {{$projectName['project_name']}}</span>

                                        <span class="task-name total-time text-right" id="projectTime{{$projectName['project_id']}}">
                                       <?php
                                       $times = array_column($projectName['tasks'],'total_time');
                                       $total = '';
                                       foreach($times as $t) {
                                           if(!empty($t)){
                                               $total += toSeconds($t);
                                           }
                                       }
                                       echo toTime($total);
                                       ?>
                                                    </span>
                                    </div>
                                    <div class="task-list" id="sendReport{{$projectName['project_id']}}">


                                        <ul>
                                            @foreach($projectName['tasks'] as $todayTaskWithTime)
                                                <li>
                                                    <div class="task-list-items">

                                                        <span
                                                                class="task-name"
                                                                data-toggle="modal"
                                                                data-user-id="{{$todayTaskWithTime['user_id']}}"
                                                                data-id="{{$todayTaskWithTime['task_id']}}"
                                                                onclick="getData('{{$todayTaskWithTime['task_id']}}','{{$todayTaskWithTime['user_id']}}');"
                                                                >{{$todayTaskWithTime['task_name']}}</span>
                                                        <span class="task-time text-right">{{$todayTaskWithTime['total_time']}}</span>

                                                    </div>
                                                </li>

                                            @endforeach
                                                <hr class="mt0 mb0">
                                                <div class="user-comment-section">
                                                    <div class="control-label font12 mb5">Comment(Optional):</div>
                                                    <div class="task-name comment-section1"><textarea class="form-control" rows="2" id="comment{{$projectName['project_id']}}" name="comment{{$projectName['project_id']}}"></textarea></div>
                                                    <button type="button" class="btn btn-xs mt10 btn-primary pull-right send-reports" id="{{$projectName['project_id']}}">Send report</button>
                                                        <div class="clearfix"></div>
                                                </div>

                                        </ul>
                                    </div>
                                </div>

                            </div>
                                    @endforeach


                    @else
                        <div class="col-sm-12">
                            <p class="font12 mb15">No Record Found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('users-work-time.task-detail-modal')

@endsection
@section('scripts')
    @include('js-helper.user-work-task-detail')
    @include('js-helper.close_modal_button')

    <script>
        function getAccordingDate(){

            var myDate = $('#reportrange').val();
            window.location = '?date='+myDate;
        }

        $(function() {

            $('#reportrange').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                maxDate: new Date(),
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });


        })
        $( document ).ready(function() {
            $('body').on('click','.send-reports',function(){
                var project_id = $(this).attr('id');
                var comment = $('textarea#comment'+project_id).val();
                var email_template = $('#sendReport'+project_id).html();
                if(project_id != ''){
                    $.ajax({
                        url: '{{route('users-current-task-time')}}',
                        type: 'POST',
                        data: {
                            _token: '{{csrf_token()}}',
                            'project_id': project_id,
                            'comment': comment,
                            'email_template': email_template,
                            'action': 'action-send-report',
                        },
                        success: function (response) {
                            if (response.success == true) {
                                toastr.success('Report send successfully done');
                                $('textarea#comment'+project_id).val('');
                            } else {
                                alert("sorry something went wrong please reload page and try again");
                            }
                        }
                    });
                }
            })
        });
    </script>

@endsection