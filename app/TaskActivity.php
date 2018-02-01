<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TaskActivity extends Model
{
    protected $table = 'task_activities';

    protected $fillable = [
        'project_id','project_task_id','message'
    ];

    public function getTasksActivities($projectTaskId,$userId,$dateFormat,$timeFormat,$timeZone){

        DB::select('SET time_zone = "'.$timeZone.'"');
        $dateFormats = Config::get('constants.GENERAL_SETTING_DATE_FORMAT_MYSQL');
        $timeFormats = Config::get('constants.GENERAL_SETTING_TIME_FORMAT_MYSQL');
        $finalDateFormat = $dateFormats[$dateFormat];
        $finalTimeFormat = $timeFormats[$timeFormat];
        $format = $finalDateFormat.' '.$finalTimeFormat;

        return $this
            ->join('users','users.id','=','task_activities.user_id')
            ->where(['task_activities.project_task_id' => $projectTaskId])
            ->orderBy('task_activities.created_at','DESC')
            ->get(['users.name','task_activities.message','task_activities.created_at',
                DB::raw('DATE_FORMAT(CONVERT_TZ(task_activities.created_at,"+00:00",@@global.time_zone), "'.$format.'") as date')])->toArray();

    }
}
