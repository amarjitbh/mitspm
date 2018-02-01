<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskLoggedTime extends Model
{
    protected $table = 'project_task_logged_time';
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    public function recordAlreadyExist($userId)
    {

        return $this
            ->where(['logged_by_user_id' => $userId, 'end_time' => null])
            ->orderBy('project_task_logged_time_id', 'desc')
            ->first(['project_task_logged_time_id', 'start_time', 'project_task_id']);

    }

    public function alreadyWorkingonTask($ProjectBoardColumnId, $userId)
    {

        return $this
            ->join('project_tasks', 'project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id')
            ->where(['logged_by_user_id' => $userId, 'end_time' => null, 'project_tasks.project_board_column_id' => $ProjectBoardColumnId])
            ->orderBy('project_task_logged_time_id', 'desc')
            ->first(['project_task_logged_time_id', 'start_time', 'project_task_logged_time.project_task_id', 'project_tasks.project_board_id', 'project_tasks.project_board_column_id', 'project_tasks.project_id']);

    }

// DELETE below function fetchAllLoggedTaskResult() if we are nolonger using this
    public function fetchAllLoggedTaskResult($projectTaskId, $userId)
    {
        $data = $this
            ->where('logged_by_user_id', '=', $userId)
            ->where('project_task_id', '=', $projectTaskId)
            ->where('end_time', '!=', "null")
            ->get([
                DB::raw("TIMEDIFF(end_time,start_time) AS timediff"),
            ]);

        // adding the time taken for the task in  h:i format
        $minutes = "";
        foreach ($data as $time) {
            list($hour, $minute) = explode(':', $time->timediff);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;
        return sprintf('%02d:%02d', $hours, $minutes);   // returns the time and formatted
    }

    public function fetchTaskWorkingOn($projectTaskId, $userId)
    {
        $data = $this
            ->where('logged_by_user_id', '=', $userId)
            ->where('project_task_id', '=', $projectTaskId)
            ->whereRaw("end_time is null")
            ->orderBy('project_task_logged_time_id', 'desc')
            ->first(['start_time']);
        return $data['start_time'];
    }


    public function fetchTaskDuration($projectTaskId, $userId)
    {
        return $this
            ->where('logged_by_user_id', '=', $userId)
            ->where('project_task_id', '=', $projectTaskId)
            ->get(['end_time', 'start_time'])->ToArray();
    }

    /*public function getUsersTask($tasks){
        $tsk = explode(',',$tasks);//pr($tsk);die;
        return $this->whereIn('project_task_logged_time.project_task_id',$tsk)
            ->join('project_tasks','project_tasks.project_task_id','=','project_task_logged_time.project_task_id')
            ->groupBy('project_task_logged_time.project_task_id')
            ->get(['project_task_logged_time.project_task_id',
                'project_tasks.subject',
                //DB::Raw('GROUP_CONCAT(start_time)'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.end_time) as end_time'),
            ]);
    DATE_FORMAT(CONVERT_TZ(NOW(),'+00:00',@@global.time_zone) ,'".$finalTimeFormat."')
    }*/


     public function getTaskTimmings($projectTaskId,$userId,$timeFormat=null,$timeZone){
        //echo $projectTaskId.'=='.$userId;die;
         //DB::select('SET time_zone = "+05:30"');
        return $this
            ->where(['project_task_id' => $projectTaskId,'logged_by_user_id'=>$userId])
            //->where(['project_task_id' => $projectTaskId])
            ->orderBy('project_task_logged_time.start_time','DESC')
            ->get([
                'start_time',
                DB::raw('case when end_time IS NULL then DATE_FORMAT(CONVERT_TZ(NOW(),"'.$timeZone.'",@@global.time_zone),"%Y-%m-%d %H:%i:%s") else end_time end as end_time'),
                //DB::raw('DATE_FORMAT(CONVERT_TZ(start_time,"+00:00",@@global.time_zone), "%Y-%m-%d %H:%i:%s") as start_time'),
                //DB::raw('case when end_time IS NULL then DATE_FORMAT(CONVERT_TZ(NOW(),"'.$timeZone.'",@@global.time_zone) ,"%Y-%m-%d %H:%i:%s") else DATE_FORMAT(CONVERT_TZ(end_time,"+00:00",@@global.time_zone), "%Y-%m-%d %H:%i:%s") end as end_time'),
            ])->toArray();
    }
}
