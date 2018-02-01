@foreach(isset($arrayResult) ? $data : $data->ProjectsTasks as $projectTask)
    @if(!empty($boardC->project_board_column_id))
        @if($projectTask->project_board_column_id == $boardC->project_board_column_id)

            <li class="list-group-item font12 draggable draggable-list-item task-class"
                id="projectTask_{{$projectTask->project_task_id}}"
                data-order-id="{{$projectTask->task_order_id}}" task-board-id="{{$projectTask->project_board_id}}">
                <div class="panel-group mb0" id="accordion" role="tablist"
                     aria-multiselectable="true">
                    <div class="panel panel-default board-panel-common">

                        <div class="panel-heading " role="tab"
                             id="headingOne">
                            <div class="panel-title">
                                                                        <span class="caret-icon" id="caretIcon_<?=$projectTask->project_task_id?>">
                                                                        <i class="fa fa-spinner hide fa-spin" id="taskLoaderId_<?php echo $projectTask->project_task_id; ?>" style="font-size:10px"></i>
                                                                            <a id="sub_<?=$projectTask->project_task_id?>"
                                                                           onclick="editTask(<?=$projectTask->project_task_id?>)"
                                                                           class="collapsed" role="button"
                                                                           data-toggle="collapse"
                                                                           data-parent="#accordion"
                                                                           href="#collapse<?=$projectTask->project_task_id?>"
                                                                           aria-expanded="true"
                                                                           aria-controls="collapse<?=$projectTask->project_task_id?>">
                                                                        </a>
                                                                        </span>
                                                                        <span class="overflow-ellipse title-overflow" id="title_<?=$projectTask->project_task_id?>">
                                                                            {{$projectTask->subject}}
                                                                        </span>
                                <?php

                                $fetch = loggedTaskOfUser($projectTask->project_task_id);
                                $timeTaken = findTotalTimeTaken($projectTask->project_task_id);
                                $taskAssignee = calculateTimeTakenForTAsk($projectTask->project_task_id);
                                ?>
                                @if(!empty($taskAssignee->project_task_assigne_id) && isset($taskAssignee->project_task_assigne_id))
                                    @if(!empty($fetch['project_task_id'])
                                    &&  $projectTask->project_task_id==$fetch['project_task_id'])


                                        <?php  $value = 'Pause';  $fxn = "clearInterval(timerVar)"; $class = "timer_pause"; ?>
                                    @else
                                        <?php $value = 'Play';  $fxn = "";  $class = "timer_start"; ?>
                                    @endif

                                    <span class="text-right">
                                                                            <span class="timer-main">
                                                                              <button class="timer timer_button {{$class}}"
                                                                                      type="button"
                                                                                      value="{{$value}}" data-project-task-id="{{$projectTask->project_task_id}}"
                                                                                      data-project-board-id="{{$projectTask->project_board_id}}"
                                                                                      onclick="{{$fxn}}"></button>
                                                                                @if(!empty($timeTaken['NoOfDays']) && $value=="Pause")

                                                                                    <span class="time_duration stopwatch clockTimer"
                                                                                          data-timer-id="{{$fetch['project_task_id']}}">{{$timeTaken['NoOfDays']}}</span>
                                                                                @elseif(!empty($timeTaken['NoOfDays']) && $value=="Play")
                                                                                    <span class="time_duration clockTimer">{{$timeTaken['NoOfDays']}}</span>
                                                                                </span>



                                                                        </span>

                                @endif
                                @else

                                    <span class="text-right">
                                    <span class="timer-main">


                                            <span class="time_duration clockTimer"
                                                  data-timer-id="{{$fetch['project_task_id']}}">{{getTotalTimes(explode(',',$projectTask->start_time),explode(',',$projectTask->end_time))}}</span>

                                            {{--<span class="time_duration clockTimer">{{$timeTaken['NoOfDays']}}</span>--}}
                                                                                </span>
                                    </span>

                                    {{--<span class="time_duration clockTimer">{{getTotalTimes(explode(',',$projectTask->start_time),explode(',',$projectTask->end_time))}}</span>--}}
                                @endif
                            </div>
                        </div>
                        <div id="collapse<?=$projectTask->project_task_id?>"
                             class="panel-collapse collapse "
                             role="tabpanel"
                             aria-labelledby="heading<?=$projectTask->project_task_id?>">

                            <div class="panel-body board-panel-body-common"
                                 id="appendDataId<?=$projectTask->project_task_id?>">

                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endif
    @else


        <li class="list-group-item font12 draggable draggable-list-item"
            id="projectTask_{{$projectTask->project_task_id}}"
            data-order-id="{{$projectTask->task_order_id}}">
            <div class="panel-group mb0" id="accordion" role="tablist"
                 aria-multiselectable="true">
                <div class="panel panel-default board-panel-common">

                    <div class="panel-heading " role="tab"
                         id="headingOne">
                        <div class="panel-title">
                                                                        <span class="caret-icon" id="caretIcon_<?=$projectTask->project_task_id?>">
                                                                          <i class="fa fa-spinner hide fa-spin" id="taskLoaderId_<?php echo $projectTask->project_task_id; ?>" style="font-size:10px"></i>
                                                                        <a id="sub_<?=$projectTask->project_task_id?>"
                                                                           onclick="editTask(<?=$projectTask->project_task_id?>)"
                                                                           class="collapsed" role="button"
                                                                           data-toggle="collapse"
                                                                           data-parent="#accordion"
                                                                           href="#collapse<?=$projectTask->project_task_id?>"
                                                                           aria-expanded="true"
                                                                           aria-controls="collapse<?=$projectTask->project_task_id?>">
                                                                        </a>
                                                                        </span>
                                                                        <span class="overflow-ellipse title-overflow" id="title_<?=$projectTask->project_task_id?>">
                                                                            {{$projectTask->subject}}
                                                                        </span>
                            <?php
                            $fetch = loggedTaskOfUser($projectTask->project_task_id);
                            $timeTaken = findTotalTimeTaken($projectTask->project_task_id);
                            $taskAssignee = calculateTimeTakenForTAsk($projectTask->project_task_id);
                            ?>
                            @if(!empty($taskAssignee->project_task_assigne_id) && isset($taskAssignee->project_task_assigne_id))
                                @if(!empty($fetch['project_task_id'])
                                &&  $projectTask->project_task_id==$fetch['project_task_id'])


                                    <?php  $value = 'Pause';  $fxn = "clearInterval(timerVar)"; $class = "timer_pause"; ?>
                                @else
                                    <?php $value = 'Play';  $fxn = "";  $class = "timer_start"; ?>
                                @endif

                                <span class="text-right">
                                                                            <span class="timer-main">
                                                                              <button class="timer timer_button {{$class}}"
                                                                                      type="button"
                                                                                      value="{{$value}}" data-project-task-id="{{$projectTask->project_task_id}}"
                                                                                      data-project-board-id="{{$projectTask->project_board_id}}"
                                                                                      onclick="{{$fxn}}"></button>
                                                                                @if(!empty($timeTaken['NoOfDays']) && $value=="Pause")
                                                                                    <span class="time_duration stopwatch clockTimer"
                                                                                          data-timer-id="{{$fetch['project_task_id']}}">{{$timeTaken['NoOfDays']}}</span>
                                                                                @elseif(!empty($timeTaken['NoOfDays']) && $value=="Play")
                                                                                    <span class="time_duration clockTimer">{{$timeTaken['NoOfDays']}}</span>
                                                                                </span>



                                                                        </span>

                            @endif
                            @else
                                <span id="playTaskValue"></span>
                                <span class="time_duration clockTimer">{{getTotalTimes(explode(',',$projectTask->start_time),explode(',',$projectTask->end_time))}}</span>
                        @endif
                        {{-- <div>{{!empty($timeTaken['NoOfDays'])?$timeTaken['NoOfDays']:''}}</div>--}}
                    </div>
                </div>
                <div id="collapse<?=$projectTask->project_task_id?>"
                     class="panel-collapse collapse "
                     role="tabpanel"
                     aria-labelledby="heading<?=$projectTask->project_task_id?>">

                    <div class="panel-body board-panel-body-common"
                         id="appendDataId<?=$projectTask->project_task_id?>">

                    </div>
                </div>
            </div>
            </div>
        </li>
    @endif

@endforeach

