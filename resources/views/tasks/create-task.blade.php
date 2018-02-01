@extends('layouts.innerpage')
@section('title', ' - Create  Task')

@section('content')
    <div style="width:50%;border:1px solid">
        <div class="row">
                <div class="modal-header">
                    Create-Task
                </div>
        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('add-task')}}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="hidden" name="project_id" id="project_id" value="{{$project_id}}">
                <input type="hidden" name="project_board_id" id="project_board_id" value="{{$board_id}}">
                <input type="hidden" name="project_board_column_id" id="project_board_column_id" value="{{$project_board_column_id}}">

                <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject">
                <textarea name="description" id="description" class="form-control" placeholder="Description"></textarea>
                <input type="submit" class="btn btn-success" value="Submit">
            </div>
        </form>
    </div>
@endsection