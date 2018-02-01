<!DOCTYPE html>
<html lang="en">
<head>
    <title>Send Reports</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <table class="table">
        <h2>Project User Reports</h2>
        <table class="table">
            <tbody>
            @if(!empty($final))
                @foreach($final as $projectName)
                        <tr>
                            <td><label>Project Name :</label> {{$projectName['project_name']}}</td>
                            <td><label>Total Time :</label> <?php
                                $times = array_column($projectName['tasks'],'total_time');
                                $total = '';
                                foreach($times as $t) {
                                    if(!empty($t)){
                                        $total += toSeconds($t);
                                    }
                                }
                                echo toTime($total);
                                ?></td>
                        </tr>
                        <tr>
                            <td><label>{{$projectName['title']}} :</label> </td>
                            <td><label>{{$projectName['time']}} :</label> </td>
                        </tr>

                        @foreach($projectName['tasks'] as $todayTaskWithTime)

                            <tr>
                                <td>{{$todayTaskWithTime['task_name']}} </td>
                                <td>{{$todayTaskWithTime['total_time']}} </td>
                            </tr>
                        @endforeach
                @endforeach
                <tr>
                    <td>Comment: </td>
                    <td>{{isset($comments) ? $comments : 'on comment'}}</td>
                </tr>
            @else
                <div class="col-sm-12">
                    <p class="font12 mb15">No Record Found</p>
                </div>
            @endif
            </tbody>
        </table>
</div>

</body>
</html>