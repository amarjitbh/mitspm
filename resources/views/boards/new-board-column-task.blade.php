<input type="hidden" class="inputValues" value="{{$data['project_board_column_id']}}" id="input_{{$data['project_board_column_id']}}">
    <div class="col-xs-3 h-100p plr-0 col-min-width boardColumnDivClass" id="boardColumnBox{{$data['project_board_column_id']}}">
        <div class="panel panel-default panel-main h-100p mb0">
            <div id="panel-heading-{{$data['project_board_column_id']}}" class="panel-heading">
                <h3 class="panel-title font13">
                    <span id="panel-span-{{$data['project_board_column_id']}}">
                        {{$data['column_name']}}
                    </span>
                    <span id="panel-input-{{$data['project_board_column_id']}}" style="display:none;color:black">
                        <input type="text" value="{{$data['column_name']}}" id="imp-val-{{$data['project_board_column_id']}}">
                    </span>
                    <span class="panel-action-btn pull-right">
                        <div id="panel-setting-{{$data['project_board_column_id']}}" class="btn-group">
                            <a href="javascript:void(0);" class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-rt">
                                <li>
                                    <a href="javascript:void(0);" onclick="editBoardColumn('{{$data['project_board_column_id']}}');">
                                        Edit
                                    </a>
                                </li>
                                <li><a href="javascript:void(0);" onclick="removeBoardColumn('{{$data['project_board_column_id']}}');">Remove</a></li>
                            </ul>
                        </div>
                        <div id="panel-done-{{$data['project_board_column_id']}}" class="btn-group" style="display:none;">
                            <a href="javascript:void(0);" style="color:#fff;" onclick="updateBoardColumns('{{$data['project_board_column_id']}}')">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0);" style="color:#fff;" onclick="cancleBoardColumns('{{$data['project_board_column_id']}}')">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        </div>

                    </span>
                </h3>
            </div>
            @if(!empty($result))
                <div class="panel-body mb0 p0">

                    <ul class="list-group droptrue mb0 h-100p myCls{{$data['project_board_column_id']}} drop sortable"
                        data-board-column-id="{{$data['project_board_column_id']}}">
                        @foreach($result as $projectTask)

                            @if($projectTask['project_board_column_id'] == $data['project_board_column_id'])

                                <li class="list-group-item font12 draggable draggable-list-item"
                                    id="projectTask_{{$projectTask['project_task_id']}}"
                                    data-order-id="{{$projectTask['task_order_id']}}">


                                    <div class="panel-group mb0" id="accordion" role="tablist"
                                         aria-multiselectable="true">
                                        <div class="panel panel-default board-panel-common">


                                            <div class="panel-heading " role="tab"
                                                 id="headingOne">
                                                <div class="panel-title">
                                                                        <span class="caret-icon">
                                                                        <a id="sub_<?=$projectTask['project_task_id']?>"
                                                                           onclick="editTask(<?=$projectTask['project_task_id']?>)"
                                                                           class="collapsed" role="button"
                                                                           data-toggle="collapse"
                                                                           data-parent="#accordion"
                                                                           href="#collapse<?=$projectTask['project_task_id']?>"
                                                                           aria-expanded="true"
                                                                           aria-controls="collapse<?=$projectTask['project_task_id']?>">
                                                                        </a>
                                                                        </span>
                                                                        <span id="title_<?=$projectTask['project_task_id']?>">
                                                                            {{$projectTask['subject']}}
                                                                        </span>
                                                    <?php
                                                    $fetch = loggedTaskOfUser($projectTask['project_task_id']);
                                                    $timeTaken = findTotalTimeTaken($projectTask['project_task_id']);
                                                    $taskAssignee = calculateTimeTakenForTAsk($projectTask['project_task_id']);
                                                    ?>
                                                    @if(!empty($taskAssignee['project_task_assigne_id']) && isset($taskAssignee['project_task_assigne_id']))
                                                        @if(!empty($fetch['project_task_id'])
                                                        &&  $projectTask['project_task_id']==$fetch['project_task_id'])


                                                            <?php  $value = 'Pause';  $fxn = "clearInterval(timerVar)"; $class = "timer_pause"; ?>
                                                        @else
                                                            <?php $value = 'Play';  $fxn = "";  $class = "timer_start"; ?>
                                                        @endif

                                                        <span class="text-right">
                                                                            <span class="timer-main">
                                      <button class="timer timer_button {{$class}}"
                                              type="button"
                                              value={{$value}} data-project-task-id="{{$projectTask['project_task_id']}}"
                                              onclick="{{$fxn}}"></button>
                                                                                @if(!empty($timeTaken['NoOfDays']) && $value=="Pause")

                                                                                    <span class="time_duration stopwatch clockTimer"
                                                                                          data-timer-id="{{$fetch['project_task_id']}}">{{$timeTaken['NoOfDays']}}</span>
                                                                                @elseif(!empty($timeTaken['NoOfDays']) && $value=="Play")
                                                                                    <span class="time_duration clockTimer">{{$timeTaken['NoOfDays']}}</span>
                                                                                </span>



                                                                        </span>

                                                    @endif
                                                    @endif
                                                    {{-- <div>{{!empty($timeTaken['NoOfDays'])?$timeTaken['NoOfDays']:''}}</div>--}}
                                                </div>
                                            </div>
                                            <div id="collapse<?=$projectTask['project_task_id']?>"
                                                 class="panel-collapse collapse "
                                                 role="tabpanel"
                                                 aria-labelledby="heading<?=$projectTask['project_task_id']?>">

                                                <div class="panel-body board-panel-body-common"
                                                     id="appendDataId<?=$projectTask['project_task_id']?>">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach

                    </ul>
                </div>
            @endif
        </div>
        <input type="hidden" class="newColumnAttrClasId" value="{{$data['project_board_column_id']}}">
        <input type="hidden" class="newColumnAttrClasName" value="{{$data['column_name']}}">
    </div>
