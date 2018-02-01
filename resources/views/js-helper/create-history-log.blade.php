
@if($getHistoryLogData != '')
 @foreach($getHistoryLogData as $getHistoryLog)
     <div class="task-logs">
         <div>
             <span class="member-name">{{$getHistoryLog->name}}</span><span class="pull-right time-stamp">{{$getHistoryLog->date}}</span>
         </div>
         <p class="logs-comment mb0">{{$getHistoryLog->message}}</p>
     </div>
 @endforeach
    @else
    <div class="task-logs">
        <p class="font12 mb0">No Record Found</p>
    </div>
@endif
