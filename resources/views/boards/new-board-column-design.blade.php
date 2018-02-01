@if(count($data)>0)
    <div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass" data-value="{{$data->project_board_column_id}}" id="boardColumnBox{{$data->project_board_column_id}}">
        <div class="panel panel-default panel-main h-100p mb0">
            <div id="panel-heading-{{$data->project_board_column_id}}" class="panel-heading">
                <h3 class="panel-title font13">
                    <span id="panel-span-{{$data->project_board_column_id}}">
                        {{$data->column_name}}
                    </span>
                    <span id="panel-input-{{$data->project_board_column_id}}" style="display:none;color:black">
                        <input type="text" value="{{$data->column_name}}" id="imp-val-{{$data->project_board_column_id}}">
                    </span>
                    <span class="panel-action-btn pull-right">
                        <div id="panel-setting-{{$data->project_board_column_id}}" class="btn-group">
                            <a href="javascript:void(0);" class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-rt">
                                <li>
                                    <a href="javascript:void(0);" onclick="editBoardColumn('{{$data->project_board_column_id}}');">
                                        Edit
                                    </a>
                                </li>
                                <li><a href="javascript:void(0);" onclick="removeBoardColumn('{{$data->project_board_column_id}}');">Remove</a></li>
                            </ul>
                        </div>
                        <div id="panel-done-{{$data->project_board_column_id}}" class="btn-group" style="display:none;">
                            <a href="javascript:void(0);" style="color:#fff;" onclick="updateBoardColumns('{{$data->project_board_column_id}}')">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0);" style="color:#fff;" onclick="cancleBoardColumns('{{$data->project_board_column_id}}')">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        </div>

                    </span>
                </h3>
            </div>
            <div class="panel-body mb0 p0" data-simplebar>
                <ul class="list-group droptrue mb0 h-100p myCls{{$data->project_board_column_id}} drop sortable"

                    data-board-column-id="{{$data->project_board_column_id}}"></ul>

            </div>
        </div>
        <input type="hidden" class="newColumnAttrClasId" value="{{$data->project_board_column_id}}">
        <input type="hidden" class="newColumnAttrClasName" value="{{$data->column_name}}">
    </div>
@endif