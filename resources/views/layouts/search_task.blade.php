@extends('layouts.app')

@section('content')

    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                           <i class="fa fa-search"></i> Search results
                            @if(!empty($searchTasks))
                            <p class="font12 mb0"> {{$searchTasks->total()}} Results of "{{$search}}" found</p>
                            @endif
                        </span>
                        <span class="pull-right">
                            <a href="projects/create"  class="btn btn-success btn-create-project btn-xs hide">Create New Project</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="panel panel-default dashboard-panel-filter search-results-list">
            <div class="panel-body">
                <div class="row mb25">
                    <div class="col-sm-12">
                        <h4 class="font16"> </h4>
                    </div>
                    <div class="clearfix"></div>

                    @if(!empty($searchTasks))
                        @foreach($searchTasks as $task)

                            <div class="col-sm-12">
                                <div class="project-card">
                                    <div class="project-heading">
                                        <div class="font14 title text-capitalize">
                                            <a href="javascript:void(0);"><span
                                                        class="task-name"
                                                        data-user-id="{{$task->user_id}}"
                                                        data-toggle="modal"
                                                        data-id="{{$task->project_task_id}}"
                                                        onclick="getData('{{$task->project_task_id}}','{{$task->user_id}}');"
                                                        >{{$task->subject}}</span></a>

                                        </div>
                                        <div class="other-info">
                                            <ul class="list-inline">
                                                <li> <span class="label-title">Project Name: </span> {{$task->name}}</li>
                                                <li> <span class="label-title">Board Name: </span> {{$task->project_board_name}}</li>
                                                <li> <span class="label-title">Task Create Date: </span>{{getLocalTimeZone($task->created_at, 3)}} </li>
                                                <li> <a href="{{route('board-detail',[$task->project_board_id])}}"><i class="fa fa-eye"></i> Go to board</a></li>
                                                <input type="hidden" id="searchProjectId" value="@if(isset($task->name)){{$task->project_id}}@endif">
                                                <input type="hidden" id="searchProjectBoardId" value="@if(isset($task->project_board_id)){{$task->project_board_id}}@endif">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach

                            {{ $searchTasks->appends(['project_id' => $projectId, 'board_id' => $boardId,'keyword' => $keyword])->links() }}
                    @endif

                    @if($searchTasks->total() == 0)
                        <div class="col-sm-12">
                            <div class="project-card">
                                <div class="project-heading">
                                    <div class="font14 title text-capitalize">
                                       No data found..
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>


        @endsection
@include('users-work-time.task-detail-modal')
        @section('scripts')
            <script>/**** For script ***/</script>
    @include('js-helper.user-work-task-detail')
    @include('js-helper.close_modal_button')
@endsection

