@if($getAllColumns != '')
    <li>
        <a href="javascript:void(0);" class="get-all-tasks overflow-ellipse" id="chk_all" onclick="allTaskColumn('{{$boardId}}','all', this);">
            All Task
        </a>
        <input style="display:none;" type="checkbox" class="coloumn_checkbox chkb_all"  value="all">
    </li>
@foreach($getAllColumns as $getAllColumn)
    <li>
        <a href="javascript:void(0);" id="column_{{$getAllColumn->project_board_column_id}}" onclick="allTaskColumn('{{$getAllColumn->project_board_id}}','{{$getAllColumn->project_board_column_id}}', this);" class="newItemAdded overflow-ellipse">
            {{$getAllColumn->column_name}}
        </a>
        <input style="display:none;" type="checkbox" class="coloumn_checkbox chkb_{{$getAllColumn->project_board_id}}" id="chk_{{$getAllColumn->project_board_column_id}}" value="{{$getAllColumn->project_board_column_id}}">
    </li>

@endforeach
@else
    <div class="task-logs">
        <p class="font12 mb0 overflow-ellipse">No Record Found</p>
    </div>
@endif