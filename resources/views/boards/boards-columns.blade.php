@extends('layouts.afterlogin')
@section('title', ' - Board Column')
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
                    <?php
                    $ColumnName = fetchColumnsName($boardId);
                    $columnId = array_column($ColumnName, 'project_board_column_id');
                    ?>
                    @if(count($data)>0)

                        @foreach($data->boardColumns as $ind =>  $boardC)
                            <?php $ind++;
                            if(in_array($boardC->project_board_column_id, $columnId)){
                            //  pr($columnId); ?>
                            {{--<div id="boardMainBox{{$boardC->project_board_column_id}}" style="float: left;">--}}
                            <input type="hidden" class="inputValues" value="{{$boardC->project_board_column_id}}" id="input_{{$boardC->project_board_column_id}}">
                            <div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass"
                                 id="boardColumnBox{{$boardC->project_board_column_id}}">
                                <div class="panel panel-default panel-main h-100p mb0">
                                    <div id="panel-heading-{{$boardC->project_board_column_id}}" class="panel-heading">
                                        <h3 class="panel-title font13">
                                        <span id="panel-span-{{$boardC->project_board_column_id}}">
                                            {{ucwords($boardC->column_name)}}
                                        </span>
                                        <span id="panel-input-{{$boardC->project_board_column_id}}"
                                              style="display:none;" class="panel-heading-edit-field form-inline">
                                            <input class="form-control" type="text"
                                                   value="{{ucwords($boardC->column_name)}}"
                                                   id="imp-val-{{$boardC->project_board_column_id}}"/>
                                        </span>
                                        <span class="panel-action-btn pull-right">
                                            <div id="panel-setting-{{$boardC->project_board_column_id}}"
                                                 class="btn-group">
                                                <a href="javascript:void(0);" class="btn font14 open-create-task-form" data-id = {{$boardC->project_board_column_id}}  title="Add Task">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn dropdown-toggle"
                                                   data-toggle="dropdown">
                                                    @if($user_role == \Config::get('constants.ROLE.ADMIN') || $user_role == \Config::get('constants.ROLE.SUPERADMIN') || $UserIsAdmin == '1')
                                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                                    @endif
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-rt">
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                           onclick="editBoardColumn('{{$boardC->project_board_column_id}}');">
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><a href="javascript:void(0);"
                                                           onclick="removeBoardColumn('{{$boardC->project_board_column_id}}');">
                                                            <i class="fa fa-remove"></i> Remove
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div id="panel-done-{{$boardC->project_board_column_id}}"
                                                 class="btn-group panel-heading-edit-field-action-btn"
                                                 style="display:none;">
                                                <a class="btn" href="javascript:void(0);"
                                                   onclick="updateBoardColumns('{{$boardC->project_board_column_id}}')">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                </a>
                                                <a class="btn" href="javascript:void(0);"
                                                   onclick="cancleBoardColumns('{{$boardC->project_board_column_id}}')">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                </a>
                                            </div>

                                        </span>
                                        </h3>
                                    </div>
                                    @if(!empty($data->ProjectsTasks))
                                        <div class="panel-body mb0 p0">

                                            <input type="hidden" value="1" name="board_hidden_field" id="boardHiddenField{{$boardC->project_board_column_id}}">
                                            <ul class="list-group droptrue mb0 h-100p myCls{{$boardC->project_board_column_id}} drop sortable"
                                                data-board-column-id="{{$boardC->project_board_column_id}}"
                                                data-project-id="{{$data->project_id}}"
                                                data-board-id="{{$boardC->project_board_id}}">
                                                @include('boards.board-column-task')
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                            </div>
                                <input type="hidden" id="columnIdFrom">

                            <?php  } else {
                            //  pr($ColumnName); die;?>
                            {{--<div id="boardColumnBox{{$boardC->project_board_column_id}}" style="float: left;">--}}
                            <input type="hidden" class="inputValues" value="{{$boardC->project_board_column_id}}" id="input_{{$boardC->project_board_column_id}}">
                            <div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass"
                                 id="boardColumnBox{{$boardC->project_board_column_id}}">
                                <div class="panel panel-default panel-main h-100p mb0">
                                    <div id="panel-heading-{{$boardC->project_board_column_id}}" class="panel-heading">
                                        <h3 class="panel-title font13">
                                        <span id="panel-span-{{$boardC->project_board_column_id}}">
                                            {{ucwords($boardC->column_name)}}
                                        </span>
                                        <span id="panel-input-{{$boardC->project_board_column_id}}"
                                              style="display:none;color:black">
                                            <input type="text" value="{{ucwords($boardC->column_name)}}"
                                                   id="imp-val-{{$boardC->project_board_column_id}}"/>
                                        </span>
                                        <span class="panel-action-btn pull-right">
                                            <div id="panel-setting-{{$boardC->project_board_column_id}}"
                                                 class="btn-group">
                                                <a href="javascript:void(0);" class="btn dropdown-toggle"
                                                   data-toggle="dropdown">
                                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-rt">
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                           onclick="editBoardColumn('{{$boardC->project_board_column_id}}');">
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li><a href="javascript:void(0);"
                                                           onclick="removeBoardColumn('{{$boardC->project_board_column_id}}');">Remove</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div id="panel-done-{{$boardC->project_board_column_id}}" class="btn-group"
                                                 style="display:none;">
                                                <a href="javascript:void(0);" style="color:#fff;"
                                                   onclick="updateBoardColumns('{{$boardC->project_board_column_id}}')">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="javascript:void(0);" style="color:#fff;"
                                                   onclick="cancleBoardColumns('{{$boardC->project_board_column_id}}')">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                </a>
                                            </div>

                                        </span>
                                        </h3>
                                    </div>

                                    @if(!empty($data->ProjectsTasks))
                                        <div class="panel-body mb0 p0">

                                            <ul class="list-group droptrue mb0 h-100p myCls{{$boardC->project_board_column_id}} drop sortable"
                                                data-board-column-id="{{$boardC->project_board_column_id}}">

                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{--</div>--}}
                            <?php } ?>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
        <!-- Modal for addTask -->
        @if(count($data)>0)
            <div id="addTask"  class="modal fade modal-common-style jp-custom-modal-slide-left" data-backdrop="static" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="panel-main-heading">Create Task</div>
                        <div class="modal-body">
                            <div id="alert_fail" class="alert alert-danger" style="display:none;">
                            </div>
                            <form id="taskForm" class="form-common">
                                <div class="form-group form-group-sm">
                                    <label for="columns" class="control-label font-600">Board-Column:<span class="text-danger">*</span></label>
                                    <select id="columns" class="form-control ">
                                        <option selected value="">Select</option>
                                        @foreach($data->boardColumns as $boardsCol)
                                            <option value="{{$boardsCol->project_board_column_id}}">
                                                {{ucfirst($boardsCol->column_name)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label for="subject" class="control-label font-600">Title:<span class="text-danger">*</span></label>
                                    <input type="text" name="subject" id="subject" class="form-control">
                                    <input type="hidden" name="submitType" id="submitType">
                                    <input type="hidden" name="subject" id="project_id" value="{{$data->project_id}}">
                                    <input type="hidden" name="subject" id="project_board_id"
                                           value="{{$data->project_board_id}}">
                                </div>
                                <div class="form-group form-group-sm">
                                    <label for="description" class="control-label font-600">Description:<span class="text-danger"></span></label>
                                    <textarea name="description" id="description" class="form-control input-sm no-resize autoExpand"></textarea>
                                </div>
                                <div class="form-group form-group-sm">
                                    <input name="image" id="imageFile" type="file">
                                </div>
                                <div class="form-group form-group-sm" style="display:none;">
                                    <label for="priority" class="control-label font-600">Priority</label>
                                    <?php $priorities = Config::get('constants.priority');?>
                                    <select id="priority" class="form-control ">
                                        @foreach($priorities as $key => $pName)
                                            <option value="{{$key}}">
                                                {{$pName}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--<div class="form-group form-group-sm">
                                    <a href="javascript:void(0);" onclick="popupOpen({{$data->project_id}})">Invite-Users</a><br />
                                </div>--}}
                                <div class="form-group" id="usersBox">
                                    {{--@if(Session::get('user_role')==\Config::get('constants.ROLE.USER') )

                                    @else--}}
                                    @if(!empty($usersList))
                                        <label for="users" class="control-label font-600">Select Users:<span class="text-danger">*</span></label>

                                        @foreach($usersList as $users)
                                            <div class="checkbox mt0 member-list-select">
                                                <input type="checkbox" class="checkbox ml0 users" name="users"
                                                       id="user_{{$users['id']}}"
                                                       value="{{$users['id']}}">
                                                <label class="font14" for="user_{{$users['id']}}">{{ucwords($users['name'])}}</label>
                                            </div>
                                        @endforeach
                                   @endif
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm btn-no-radius" data-dismiss="modal">
                                Close
                            </button>
                            <button type="button" id="submit" onclick="taskSubmit()"
                                    class="btn btn-sm btn-success btn-no-radius">Create
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div id="removeColumns" class="modal modal-common-style fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <form action="{{route('moveColumnsTask')}}" method="POST">
                            {{csrf_field()}}
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Move Tasks</h4>
                            </div>
                            <div class="modal-body" id="columnNames">


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-sm btn-no-radius" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" id="submit" class="btn btn-sm btn-success btn-no-radius">Move
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="spiner"
                 style="position: fixed;top: 0;z-index: 99999;width: 100%;text-align: center;display: none;">
                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
            </div>


            <div class="modal fade" id="inviteUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content width450">
                        <div class="bgorange">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <div class="form-group">

                                    <div class="alert-warning"></div>

                                </div>
                                <h4 class="modal-title textwhite" id="exampleModalLabel">Invite User</h4>
                                {!! Form::open(array('route' => 'invitedUserofProject')) !!}
                                {!! Form::hidden('projectId','',array('id'=>'projectId')) !!} <br/><br/>
                                Send Invite as
                                Admin* {!! Form::text('adminEmail','',array('class'=>'form-control','placeholder'=>'Invite user')) !!}
                                <br/><br/>
                                Send Invite as
                                Users* {!! Form::text('UserEmail','',array('class'=>'form-control','placeholder'=>'Invite user')) !!}
                                <br/><br/>
                                Send Invite as
                                Users* {!! Form::hidden('page','board_detail',array('class'=>'form-control')) !!}
                                <br/><br/>
                                {!! Form::button('Submit',array('class'=>'inviteMoreUsers')) !!} <br/>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="modal-body viewDetailBody">
                            <div class="viewDetailContent" id="interesteddetail">


                            </div>
                        </div>
                        <div class="clearfix">&nbsp;</div>
                    </div>

                </div>
            </div>

            <!-- Modal for column not remove -->
            <div id="columnNotRemove" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>

                            <p style="color:red">Sorry you can't remove last column</p>
                        </div>
                    </div>

                </div>
            </div>
            {{--<div class="btnSpiner" style="">

                <button type="button" class="btn btn-default btn-success btn-xs ml0 mt5 mb5">
                    <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
                </button>
            </div>--}}
            {{--testing--}}
    @endif
@endsection
@section('scripts')
    @include('js-helper.dragDropProjectTask')
    @include('js-helper.boards-columns-js')
    @include('js-helper.boards-columns-add-update-js')
    @include('js-helper.inviteuser')


@endsection