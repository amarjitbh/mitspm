@extends('layouts.after-login')
@section('title', ' - Board Task')
@section('content')
    <div id="page-content-wrapper" class="h-100p">
        <div class="content-header visible-xs">
            <div id="home">
                <a id="menu-toggle2" href="#" class=" btn-menu toggle">
                    <i class="fa fa-bars"></i> MENU
                </a>
            </div>
        </div>
        <div> @include('message_display')</div>
        <div class="page-content h-100p" data-spy="scroll" data-target="#spy">
            <div class="h-scrollable container-fluid h-100p">
                <div class="row h-100p" id="boardRowDiv">
                    {{--Test View--}}
                    <div class="expanded-view expView test-body" style="display:none;">
                        <div class="tile font12 ">
                            <span>Task Activity Logs</span>
                            <span class="action pull-right">
                                <a href="javascript:void(0);" class="action-compress closeExpandHistoryView">
                                    <i class="fa fa-compress"></i></a>
                            </span>
                        </div>
                        <div class="content-section">
                            <div class="panel panel-default board-panel-common font12">

                                <div class="panel-body board-panel-body-common" id="historyLogData">

                                </div>

                            </div>
                        </div>
                    </div>

                    {{--Test view end--}}
                    <div class="expanded-view expView " style="display:none;">
                        <div class="tile font12 ">
                            <span>Expanded View</span>
                            <span class="action pull-right">
                                <a href="javascript:void(0);" class="action-compress closeExpandView">
                                    <i class="fa fa-compress"></i></a>
                            </span>
                        </div>
                        <div class="content-section">
                            <div class="panel panel-default board-panel-common font12">

                                <div class="panel-body board-panel-body-common" id="expendedViewBody">

                                </div>

                            </div>
                        </div>
                    </div>
                    @if(count($boardWithTasks)>0)
                        @foreach($boardWithTasks as  $boardC)
                            <input type="hidden" class="inputValues">
                            <div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass"
                                 id="boardColumnBox">
                                <div class="panel panel-default panel-main h-100p mb0">
                                    <div id="panel-heading" class="panel-heading">
                                        <h3 class="panel-title font13">
                                        <span id="panel-span" class="project-board-class" projectBoardID="{{$boardC['project_board_id']}}">
                                            {{$boardC['project_board_name']}}
                                        </span>
                                            <span class="panel-action-btn pull-right">
                                            <div id="panel-setting-4" class="btn-group">
                                                <span class="panel-action-btn pull-right">
                                            <div id="panel-setting-4" class="btn-group dropdown">

                                                <a class="btn  dropdown-toggle columnWithTask" title="All Columns" href="javascript:void(0);" onclick="getAllColumnWithTask('{{$boardC['project_board_id']}}');" data-toggle="dropdown">
                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-rt boardColumnDropMenu" id="column_board_id_{{$boardC['project_board_id']}}" role="menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">

                                                </ul>
                                            </div>

                                        </span>
                                            </div>
                                        </span>
                                             <span class="panel-action-btn pull-right">
                                            <div id="panel-setting-4" class="btn-group">
                                                <a class="btn" title="Board Work" href="{{route('board-detail',$boardC['project_board_id'] )}}" >
                                                    <i class="fa fa-columns" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </span>
                                        <span id="panel-input"
                                              style="display:none;" class="panel-heading-edit-field form-inline">
                                            <input class="form-control" type="text" />
                                        </span>
                                        </h3>
                                    </div>
                                    @if(!empty($boardC['project_boards_all']))

                                        <div class="panel-body mb0 p0">

                                            <input type="hidden" value="1" name="board_hidden_field" id="boardHiddenField{{$boardC['project_board_id']}}">
                                            <ul class="list-group droptrue mb0 h-100p myCls drop new-appended-data " data-bid="{{$boardC['project_board_id']}}" id="board_{{$boardC['project_board_id']}}">
                                                @include('boards.board-task-data')
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <input type="hidden" id="columnIdFrom">
                                <input type="hidden" id="getProjectIdHiddenField" class="get-ptroject-id-hidden-field" value="{{$project_id}}">
                        @endforeach
                    @endif
                </div>

            </div>
        </div>

@endsection
@section('scripts')
    @include('js-helper.dragDropProjectTask')
    @include('js-helper.boards-columns-js')
    @include('js-helper.boards-columns-add-update-js')
    @include('js-helper.inviteuser')


@endsection