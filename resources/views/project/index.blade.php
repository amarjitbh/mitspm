@extends('layouts.app')
@section('title', ' - Index  Project')

@section('content')
    {{--{{ Session::get('user_role')}}--}}

    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                           Project Detail
                        </span>
                        @if(Session::get('user_role')==\Config::get('constants.ROLE.SUPERADMIN') || Session::get('user_role')==\Config::get('constants.ROLE.ADMIN'))
                            <span class="pull-right">
                            <a href="{{route('projects.create')}}" class="btn btn-success btn-create-project btn-xs">Create
                                Project</a>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container alert-container">
        <div class="row">
            <div class="col-sm-12">
                @include('flash_message')
            </div>
        </div>
    </div>
    <div class="container">
        <div class="breadcrumb-bar">

            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="active">Manage Members</li>
                    </ol>
                </div>
            </div>

        </div>
        <div class="panel panel-default dashboard-panel-filter manage-project-add-people">

            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="emails" data-project-id={{$projectId}} name="email" placeholder="Add multiple email address separated by comma">
                            <select id="userRole" name="type" title="Select user role" class="selectpicker">
                                    @foreach(\Config::get('constants.USER_TYPE_FOR_PROJECT') as $key=>$value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                <span class="input-group-btn">
                    <button class="btn btn-default btn-success" type="button" id="invitingUser">ADD</button>
                </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        {{-- <div class="text-right">
                             <a href="{{route('projects.index')}}" class="btn btn-sm btn-default">Project Invites</a>&nbsp;<a
                                     href="{{route('assigned-user')}}" class="btn btn-sm btn-default">Project User
                                 Assigned</a>
                         </div>--}}

                    </div>
                </div>


                <div class="clearfix"></div>

            </div>
            <div class="panel-body pt0">

                <div class="row mb25">

                    <div class="clearfix"></div>
                    <div class="table-responsive table-common-style">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="120">Role</th>
                                <th width="185">Invitation</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="appendedInviteUser">
                            <?php $alreadyUsers = []; ?>
                            @if(!empty((array) $assignedProject))
                                @foreach($assignedProject as $assignedProject)
                                    <?php //pr($assignedProject->toArray());?>
                                    <?php $alreadyUsers[] = $assignedProject['email']; ?>
                                    <tr id="accepted_{{$assignedProject['user_project_id']}}">
                                        <input type="hidden" id="userProjectId" value="{{$assignedProject['user_project_id']}}">
                                        <input type="hidden" id="projectId" value="{{$assignedProject['project_id']}}">
                                        <td class="font-600">{{ucwords($assignedProject['username'])}}</td>
                                        <td id="email{{$assignedProject['user_id']}}">{{$assignedProject['email']}}</td>
                                        @if($assignedProject['is_admin']==0 && $assignedProject['role'] == \Config::get('constants.ROLE.USER'))
                                            <?php $user="Member"; ?>
                                        @elseif($assignedProject['role'] ==  \Config::get('constants.ROLE.USER'))
                                            <?php $user="Project-Admin";?>
                                        @elseif($assignedProject['role'] ==  \Config::get('constants.ROLE.SUPERADMIN'))
                                            <?php $user="Super-Admin";?>
                                        @endif
                                        <td id="role{{$assignedProject['user_id']}}">{{$user}} </td>
                                        <td>Accepted</td>
                                        <td>
                                            @if($assignedProject['role'] != \Config::get('constants.ROLE.SUPERADMIN') && $assignedProject['role'] != $roleSession)
                                                @if(\Illuminate\Support\Facades\Auth::user()->email != $assignedProject['email'])
                                                    <span class="btn btn-default btn-xs change-role" id="{{$assignedProject['user_id']}}">Change Role</span>
                                                @endif
                                                <a class="btn btn-default btn-xs btn-danger" href="javascript:void(0);"
                                               onclick="removeProjectMemeber('{{$assignedProject['user_project_id']}}','accepted')">Remove
                                                </a>

                                            @elseif($assignedProject['role'] == \Config::get('constants.ROLE.USER')  || $assignedProject['is_admin'] == 0 && $assignedProject['is_admin'] == 1)
                                                @if(\Illuminate\Support\Facades\Auth::user()->email != $assignedProject['email'])
                                                <span class="btn btn-default btn-xs change-role " id="{{$assignedProject['user_id']}}">Change Role</span>
                                                @endif
                                            @endif

                                        </td>


                                    </tr>
                                @endforeach
                            @endif
                            @if(count($data)>0)
                                @foreach($data as $UserInvite)
                                    @if(!empty($alreadyUsers))
                                        @if(!in_array($UserInvite['email'],$alreadyUsers))

                                            <tr id="pending_{{$UserInvite['project_invite_id']}}">
                                                <td class="font-600">{{ucwords($UserInvite['username'])}}</td>
                                                <td>{{$UserInvite['email']}}</td>
                                                @if($UserInvite['is_admin']==0)
                                                    <?php $user="Member";?>
                                                @else
                                                    <?php $user="Project-Admin";?>
                                                @endif
                                                <td>{{$user}} </td>
                                                <td>Pending invitation</td>
                                                <td><a class="btn btn-default btn-xs btn-danger" href="javascript:void(0);"
                                                       onclick="removeProjectMemeber('{{$UserInvite['project_invite_id']}}','pending')">Remove</a>
                                                </td>
                                            </tr>

                                        @endif
                                    @else
                                        <tr id="pending_{{$UserInvite['project_invite_id']}}">
                                            <td class="font-600">{{ucwords($UserInvite['username'])}}</td>
                                            <td>{{$UserInvite['email']}}</td>
                                            @if($UserInvite['is_admin']==0)
                                                <?php $user="Member";?>
                                            @else
                                                <?php $user="Project-Admin";?>
                                            @endif
                                            <td>{{$user}} </td>
                                            <td>Pending invitation</td>
                                            <td><a class="btn btn-default btn-xs btn-danger" href="javascript:void(0);"
                                                   onclick="removeProjectMemeber('{{$UserInvite['project_invite_id']}}','pending')">Remove</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>


            </div>
        </div>
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

    <div class="modal modal-common-style fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Change Role</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group mb0">
                        <label for="userEmail" class="control-label font-500">User Email:</label>
                        <p name="user_email" id="userEmail" class="font12 font-600 pt0 pb0 userEmailRoleChange form-control-static"></p>
                    </div>


                    <div class="form-group form-group-sm">
                        <label for="columns" class="control-label font-500">Select User Role:</label>
                        <input type="hidden" name="user_id" id="userId">
                        <select name="change_user_role" id="changeUserRole" class="selectpicker form-control">
                            <option value="0">Member</option>
                            <option value="1">Project-Admin</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm btn-no-radius" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-sm btn-no-radius" id="changeRoleButton">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('js-helper.inviteuser')
@endsection