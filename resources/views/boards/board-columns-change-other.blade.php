<input type="hidden" readonly name="removeColumn" value="{{$columnId}}">
<input type="hidden" readonly name="boardID" value="{{$data->project_board_id}}">
<div class="font12 alert alert-info btn-no-border">You have some tasks in column. Please select the board to move your existing tasks.
</div>
@foreach($boards as $board)
    @if($columnId !=$board->project_board_column_id)

        <div class="radio mt0 mb5">
            <label for="chk_{{$board->project_board_column_id}}" class="font14 text-capitalize">
            <input type="radio" id="chk_{{$board->project_board_column_id}}" name="radioColumns" class="boardChk" value="{{$board->project_board_column_id}}">
            {{$board->column_name}}</label>
        </div>
    @endif
@endforeach