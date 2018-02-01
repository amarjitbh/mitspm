<div class="sub-board-list">
@if(!empty($boards))
    @foreach($boards as $board)
        <div class="sub-board-list-items">
            <a href="{{route('board-detail',$board['project_board_id'])}}" id="boardNameId_{{$board['project_board_id']}}" class="font12 board-label">{{$board['project_board_name']}}</a>

            <form id="txtBox" action="#" class="form-inline hide">
                <input  type="text" class="form-control input-sm">
            </form>
            @if($role == \Config::get('constants.ROLE.SUPERADMIN') || $role == \Config::get('constants.ROLE.ADMIN') || $userIsAdmin->is_admin == 1)
            <span class="action-btn pull-right">
                <a href="JavaScript:void(0);" id="{{$board['project_board_id']}}" class="btn btn-default btn-sm rename-board">Rename</a>
                <a href="JavaScript:void(0);" id="{{$board['project_board_id']}}" class="btn btn-success btn-sm btn-submit-rename hide">Submit</a>
                <a href="JavaScript:void(0);" id="{{$board['project_board_id']}}" class="btn btn-warning btn-sm btn-cancel-rename hide">Cancel</a>
            </span>
            @endif
        </div>
    @endforeach
    @else
    <div class="sub-board-list-items no-record-found text-center">
        <span class="font12">Sorry, no board found.</span>
    </div>
@endif
</div>

