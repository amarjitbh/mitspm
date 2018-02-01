@extends('layouts.innerpage')
@section('title', ' - Project Assigned Page')

@section('content')

    <div class="form-group">
        @include('flash_message')
    </div>

    <table>

        <thead>
        <tr>
            <th>Project Name</th>
            <th>Assigned User</th>
            <th>Started On</th>

        </tr>
        </thead>
        @if(count($fetchResult)>0)
            @foreach($fetchResult as $resultData)

                <tr>
                    <td>{{$resultData['name']}}</td>
                    <td>{{$resultData['username']}}</td>
                    <td>{{date('M-d-Y', strtotime($resultData['start_date']))}}</td>

                </tr>
            @endforeach
        @else
            <tr>
                <td>No Record Found</td>
            </tr>
        @endif
        <?php echo $fetchResult->links()?>
    </table>


@endsection
