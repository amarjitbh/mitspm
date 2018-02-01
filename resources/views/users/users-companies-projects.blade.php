@extends('layouts.app')
@section('title', ' - User Company Project')

@section('content')

    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            DASHBOARD <?php if($user_role != \Config::get('constants.ROLE.USER')){ echo '- Admin'; } ?>
                        </span>
                        <span class="pull-right">
                            <?php if($user_role != \Config::get('constants.ROLE.USER')){ ?>
                                <a href="{{route('projects.create')}}"  class="btn btn-success btn-create-project btn-xs">Create New Project</a>
                            <?php } ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if(!empty(Session::has('error')))
            <div class="alert alert-danger">
                {{Session::get('error')}}
            </div>
        @endif
            <div class="breadcrumb-bar">

                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li class="active">Dashboard</li>
                            </ol>
                        </div>
                    </div>

            </div>
        <div class="panel panel-default dashboard-panel-filter">
          {{--  <div class="panel-heading hide">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="input-group input-group-sm hide">
                            <select id="lunch" class="selectpicker" title="Search by...">
                                <option>Archive</option>
                                <option>Favourite</option>
                            </select>

                            <input type="text" class="form-control" name="x" placeholder="Search term...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><span class="fa fa-search"></span></button>
                </span>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="text-right">
                            <a href="{{route('projects.index')}}" class="btn btn-sm btn-default">Project Invites</a>&nbsp;<a
                                    href="{{route('assigned-user')}}" class="btn btn-sm btn-default">Project User
                                Assigned</a>
                        </div>

                    </div>
                </div>


                <div class="clearfix"></div>
            </div>
--}}
            <div class="panel-body">

                <div class="row mb25">
                    <div class="col-sm-12">
                        <h4 class="font16"><i class="fa fa-star"></i> Projects</h4>
                    </div>
                    <div class="clearfix"></div>
                    <?php //pr($company['projects_list']); ?>
                    @if(count($company)>0)
                        @foreach($company['projects_list'] as $ind => $pro)
                    <div class="col-sm-12">
                        <div class="project-card">

                            <div class="project-heading">
                                <span class="font14 title cur-pointer" data-toggle="collapse" data-target="#projectBoards{{$pro['project_id']}}" onclick="getAllBoards({{$pro['project_id']}})">{{$pro['name']}}</span>


                                <div class="pull-right" role="toolbar">

                                    <div class="" role="group" >
                                        <a  data-toggle="collapse" data-target="#projectBoards{{$pro['project_id']}}" onclick="getAllBoards({{$pro['project_id']}})" data-tooltip="true" data-placement="bottom" title="View Boards" class="btn btn-default btn-no-border">
                                            <i class="fa fa-list-ul"></i>
                                        </a>
                                        <a  href="{{route('getAllBoardsAndTaskProjectLabel',$pro['project_id'])}}"  data-tooltip="true" data-placement="bottom" title="View boards & tasks project label" class="btn btn-default btn-no-border">
                                            <i class="fa fa-columns"></i>
                                        </a>

                                        @if(!empty($pro['is_admin']) && $user_role == \Config::get('constants.ROLE.USER'))

                                            @if($pro['is_admin'] == \Config::get('constants.PROJECT_ADMIN.ADMIN'))
                                                <a href="{{route('create-board',$pro['project_id'])}}" data-tooltip="true" data-placement="bottom" title="Create board" class="btn btn-default btn-xs  btn-no-border">
                                                    <i class="fa fa-plus"></i>
                                                </a>

                                                <a href="{{route('projects.index').'?projectId='.$pro['project_id']}}" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Manage project members">
                                                    <i class="fa fa-users"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if(!empty($user_role) && $user_role ==1)

                                        <a href="{{route('create-board',$pro['project_id'])}}" data-tooltip="true" data-placement="bottom" title="Create board" class="btn btn-default btn-xs  btn-no-border">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        {{--<a href="#" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Add to favourite">--}}
                                            {{--<i class="fa fa-heart-o"></i>--}}
                                        {{--</a>--}}
                                        <a href="{{route('projects.index').'?projectId='.$pro['project_id']}}" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Manage project members">
                                            <i class="fa fa-users"></i>
                                        </a>
                                        <a href="{{route('projects.edit',$pro['project_id'])}}" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Project Name">
                                            <i class="fa fa-cog"></i>
                                        </a>
                                        @elseif($user_role ==2)
                                            <a href="{{route('create-board',$pro['project_id'])}}" data-tooltip="true" data-placement="bottom" title="Create board" class="btn btn-default btn-xs  btn-no-border">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                            {{--<a href="#" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Add to favourite">--}}
                                                {{--<i class="fa fa-heart-o"></i>--}}
                                            {{--</a>--}}
                                            <a href="{{route('projects.index').'?projectId='.$pro['project_id']}}" class="btn btn-default btn-no-border" data-tooltip="true" data-placement="bottom" title="Manage project members">
                                                <i class="fa fa-users"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="project-sub-board" id="projectBoards{{$pro['project_id']}}">
                                {{---Project Boards List---}}

                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                @if (!count($company['projects_list']))
                    <tr><td colspan="5">No Project found.</td> </tr>
                @endif


            </div>
        </div>
    </div>

    {{--Create New Sub Board Modal--}}
    <div class="modal fade modal-common-style" id="newBoard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create New Board</h4>
                </div>
                <div class="modal-body">
                    <form role="form form-common">
                        <div class="form-group form-group-sm">
                            <label class="control-label">Project Name</label>
                            <input class="form-control" placeholder="Project name" type="text">
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="control-label">Description</label>
                            <textarea class="form-control no-resize" placeholder="Project Description" type="text" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-default btn-success">Submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
@section('scripts')
@include('js-helper.user-company-projects-js')

@endsection


