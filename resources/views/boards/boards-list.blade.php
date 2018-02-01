
@extends('layouts.afterlogin')
@section('title', ' - Board List')

@section('content')
@if(!empty(Session::has('success')))
    <div class="alert alert-success">
        <h2> {{ Session::get('success') }}</h2>
    </div>
@endif
<a href="{{route('create-board',$proId)}}">Create-Board</a><br /><br />
@if(!empty($data))
<table border="1">

    <tr>
        <th>Board</th>
        <th>Description</th>
    </tr>
        @foreach($data as $ind => $board)
            <tr>
                <td>
                    <a href="{{route('board-detail',$board->project_board_id)}}">
                        {{$board->project_board_name}}
                    </a>
                </td>
                <td>{{$board->description}}</td>
            </tr>
        @endforeach
</table>
@endif
@endsection