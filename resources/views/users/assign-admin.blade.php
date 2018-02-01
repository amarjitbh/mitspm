@extends('layouts.innerpage')
@section('title', ' - Admin  Assign')

@section('content')  <div class="form-group">
    @include('flash_message')
</div>
<table >

    <tr>
        <th>Action</th>
        <th>Project</th>
        <th>Assigned User</th>
        <th>Is Admin</th>

    </tr>
    @if(count($fetchProjects)>0)
        {!! Form::open(array('route' => 'post-assign-admin')) !!}

    @foreach($fetchProjects as $resultData)
             <tr>
                <td>{!!  Form::checkbox('assign_admin[]', $resultData['user_project_id'], '') !!}
                </td>
                <td>{{$resultData['name']}}</td>
                <td>{{$resultData['username']}}</td>
                <td>{{$resultData['is_admin']==1?'Admin':'User'}}</td>

            </tr>
        @endforeach
            {!! Form::submit('Add Admin') !!} <br/>
            {!! Form::close() !!}
    @else
        <tr>
            <td>No Record Found</td>
        </tr>
@endif
<?php echo $fetchProjects->links()?>


@endsection