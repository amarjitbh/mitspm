@if($boardC['project_boards_all'] != '')
    <?php $column = '';
    $columnCount = 0;
            $colors = [
                'bg-color-1',
                'bg-color-2',
                'bg-color-3',
                'bg-color-4',
                'bg-color-5'
            ];
            //pr($boardC);exit;
    ?>
    @foreach($boardC['project_boards_all'] as $index => $projectTask)



            <?php
             //   pr($boardC,1);
                   // pr($projectTask);exit;
        if ($index == 0) {
            $column = isset($projectTask['column_name']) ? $projectTask['column_name'] : '';

        }
                    if(!empty($projectTask['column_name'])){
        if ($column != $projectTask['column_name']) {
            $columnCount += 1;

        }
                    }
        if ($columnCount >= 4) {
            $columnCount = 4;

        }
            $column = isset($projectTask['column_name']) ? $projectTask['column_name'] : '';

        ?>

        <li class="list-group-item font12 draggable draggable-list-item ui-sortable-handle" id="projectTask_{{$projectTask['project_task_id']}}" data-order-id="1">
            <div class="panel-group mb0" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default board-panel-common">

                    <div class="panel-heading " role="tab" id="headingOne">
                        <div class="panel-title">
                        <span class="caret-icon">
                            <i class="fa fa-spinner hide fa-spin" id="taskLoaderId_<?php echo $projectTask['project_task_id']; ?>" style="font-size:10px"></i>
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
                        <span class="overflow-ellipse title-overflow load-more" id="title_{{$projectTask['project_task_id']}}">
                            {{ $projectTask['subject']}}
                        </span>
                        <span class="text-right board-task-status">
                          <span style="background-color: {{$projectTask['column_color_code']}}" class="overflow-ellipse  board-task-status-label {{$colors[$columnCount]}}">{{ $projectTask['column_name']}}</span>
                        </span>
                            <?php
                            $fetch = loggedTaskOfUser($projectTask['project_task_id']);
                            $timeTaken = findTotalTimeTaken($projectTask['project_task_id']);
                            $taskAssignee = calculateTimeTakenForTAsk($projectTask['project_task_id']);
                            ?>
                            @if(!empty($taskAssignee->project_task_assigne_id) && isset($taskAssignee->project_task_assigne_id))
                                @if(!empty($fetch['project_task_id'])
                                &&  $projectTask['project_task_id']==$fetch['project_task_id'])


                                    <?php  $value = 'Pause';  $fxn = "clearInterval(timerVar)"; $class = "timer_pause"; ?>
                                @else
                                    <?php $value = 'Play';  $fxn = "";  $class = "timer_start"; ?>
                                @endif

                                <span class="text-right timer-block-main">
                                <span class="timer-main">
                                  <button class="timer timer_button {{$class}}"
                                          type="button"
                                          value="{{$value}}" data-project-task-id="{{$projectTask['project_task_id']}}"
                                          data-project-board-id="{{$projectTask['project_task_id']}}"
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
    @endforeach

@else
    <div class="task-logs">
        <p class="font12 mb0">No Record Found</p>
    </div>
@endif
