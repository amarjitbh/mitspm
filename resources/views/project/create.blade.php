@extends('layouts.app')
@section('title', ' - Create  Project')

@section('content')
    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            Create Project - Admin
                        </span>
                        <span class="pull-right">
                            <a href="{{route('projects.create')}}"  class="btn btn-success btn-create-project btn-xs">Create New Project</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id='cover'></div>
    <div class="container">
        <div class="panel panel-default">
            <div class="form-group">
                @include('flash_message')
            </div>

            <div class="panel-body">

                <div class="row mb25">

                    <div class="clearfix"></div>
                    {!! Form::open(array('route' => 'projects.store', 'class' => 'form')) !!}
                    <div class="col-sm-4">
                        <div class="form-group form-group-sm">
                            <label for="project_title" class="control-label">Project Name:<span class="text-danger">*</span></label>
                            {!! Form::text('name','',array('class'=>'form-control', 'placeholder'=>'Project name')) !!}
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="invite_as_admin" class="control-label">Invite users as Project Admin<span class="small"> (with comma separated)</span>:<span class="text-danger">*</span></label>
                            {!! Form::text('adminEmail','',array('class'=>'form-control','placeholder'=>'Add multiple email address separated by comma')) !!}
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="invite_as_admin" class="control-label">Invite user as Users <span class="small">(with comma separated)</span></label>
                            {!! Form::text('UserEmail','',array('class'=>'form-control','placeholder'=>'Add multiple email address separated by comma')) !!}
                        </div>
                        <div class="form-group">

                            <button type="submit" class="btn btn-sm btn-default btn-success" id="loader">Submit</button>
                            <a class="btn btn-default btn-sm btn-close" href="{{ route('dashboard') }}">Cancel</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </div>


{{--
<a href="{{route('projects.index')}}">Project Invites</a>

{!! Form::open(array('route' => 'projects.store')) !!}
 Project Name* {!! Form::text('name','Project Name',array('class'=>'form-control')) !!} <br/> <br/>
 Send Invite as Admin* {!! Form::text('adminEmail','Invite user',array('class'=>'form-control')) !!} <br/><br/>
 Send Invite as Users* {!! Form::text('UserEmail','Invite user',array('class'=>'form-control')) !!} <br/><br/>
 {!! Form::submit('Submit') !!} <br/>
{!! Form::close() !!}

--}}

@endsection
@section('scripts')
    <script>
        $("#loader").click(function(){
            var name =  $( "input[name='name']").val();
            var adminEmail =  $( "input[name='adminEmail']").val();
            if(name != '' && adminEmail != ''){
                $('.c-loader').removeClass('loader-hide');
            }
            else{
                $('.c-loader').removeClass('loader-hide');
            }

        });
    </script>
@endsection
