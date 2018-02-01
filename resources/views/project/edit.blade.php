@extends('layouts.app')
@section('title', ' - Edit  Project')

@section('content')

    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="dashboard-main">
                        <span class="title font16">
                            Project Setting - Admin
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
                @if(Session::has('success'))
                    <div class="alert alert-success errorDanger">
                        <h2>{{ Session::get('success') }}</h2>
                    </div>
                @endif
                @if(Session::has('errors'))
                    <div class="alert alert-danger">
                        <h2>{{ Session::get('error') }}</h2>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb-bar">

            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="active">Edit Project Name</li>
                    </ol>
                </div>
            </div>

        </div>
        <div class="panel panel-default">


            <div class="panel-body">

                <div class="row mb25">

                    <div class="clearfix"></div>
                    {!! Form::model($fetchResult, ['method' => 'PATCH', 'route' => ['projects.update', $fetchResult['project_id']]]) !!}
                    <div class="col-sm-4">
                        <div class="form-group form-group-sm">
                            <label for="project_title" class="control-label">Project Name</label>
                            {!! Form::text('name',$fetchResult['name'],array('class'=>'form-control')) !!}
                            <input type="hidden" value="{{$fetchResult['project_id']}}" name="project_id">
                        </div>

                        <div class="form-group">
                            {{--<button type="reset" class="btn btn-sm btn-default mr5">Cancel</button>--}}
                            <a class="btn btn-sm btn-default mr5" href="{{ route('dashboard') }}">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-default btn-success">Save</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>




            </div>
        </div>
    </div>

 {{--
    @if(Session::has('success'))
    <div class="alert alert-success errorDanger">
        <h2>{{ Session::get('success') }}</h2>
    </div>
@endif
@if(Session::has('errors'))
    <div class="alert alert-danger">
        <h2>{{ Session::get('error') }}</h2>
    </div>
@endif
{!! Form::model($fetchResult, ['method' => 'PATCH', 'route' => ['projects.update', $fetchResult['project_id']]]) !!}

 Project Name* {!! Form::text('name',$fetchResult['name'],array('class'=>'form-control')) !!} <br/> <br/>
<input type="hidden" value="{{$fetchResult['project_id']}}" name="project_id">
 {!! Form::submit('Submit') !!} <br/>
{!! Form::close() !!}--}}