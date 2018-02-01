@extends('layouts.app')
@section('title', ' - Current Task')

@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            User Current Task
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
                        <li class="active">Current Task</li>
                    </ol>
                </div>
            </div>

        </div>
    <div class="panel panel-default dashboard-panel-filter manage-project-add-people">


        <div class="panel-body">

            <div class="row  row-ht-equal col-no-flex mt15">
                @if(!empty($data))
                    <?php //pr($data); die; ?>
                    @foreach($data as $dt)
                <div class="col-sm-4 mb15 paginate-size-sm">
                    <div class="user-detail-card">
                        <div class="user-header">
                            <span class="user-name">{{$dt['name']}}</span>

                            <span class="total-time text-right">{{--{{$dt['logging_time']}}--}}</span>
                        </div>

                        <div class="task-list">
                            <ul>
                                <li>
                                    <div class="task-list-items">
                                        {{--<span class="task-name">{{$dt['subject']}}</span>--}}
                                       <span
                                               class="task-name"
                                               data-toggle="modal"
                                               data-user-id="{{$dt['id']}}"
                                               data-id="{{$dt['task_id']}}"
                                               onclick="getData('{{$dt['task_id']}}','{{$dt['id']}}');">{{$dt['subject']}}</span>
                                        <span class="task-time text-right">
                                            <?php
                                                $time = $dt['logging_time'];

                                                $time2 = getTotalTime($dt['current_start_time'],date('Y-m-d H:i:s'));
                                           echo $time2;

                                                ?>
                                        </span>
                                    </div>
                                </li>
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
    <script>/**** For script ***/</script>
    @include('js-helper.user-work-task-detail')
    @include('js-helper.close_modal_button')

@endsection