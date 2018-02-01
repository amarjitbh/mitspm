<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Session\Session;

class TaskComments extends Model{

    protected $table = 'task_comments';

    public function getTaskComments($tid,$dateFormat=null,$timeFormat=null,$timeZone,$page=null){
        /*** $tid as Task ID ***/
        //DB::select("SET time_zone = '+5:30'");

        DB::select('SET time_zone = "'.$timeZone.'"');

        $dateFormats = Config::get('constants.GENERAL_SETTING_DATE_FORMAT_MYSQL');
        $timeFormats = Config::get('constants.GENERAL_SETTING_TIME_FORMAT_MYSQL');
        $finalDateFormat = $dateFormats[$dateFormat];
        $finalTimeFormat = $timeFormats[$timeFormat];
        $format = $finalDateFormat.' '.$finalTimeFormat;
       // pr($format);die;
        $data =  $this->join('users','users.id','=','task_comments.posted_by_user_id')
                ->where(['project_task_id'=>$tid]);
        if(!empty($page)) {
            $data = $data->orderBy('task_comments.task_comment_id', 'ASC');
        }else{
            $data = $data->orderBy('task_comments.task_comment_id', 'DESC');
        }

                $data = $data->get(['users.id','users.name','task_comments.comment','task_comments.posted_by_user_id','task_comments.task_comment_id','task_comments.file',
                    //DB::raw('DATE_FORMAT(task_comments.created_at, "%d %b,%l:%i%p") as date'),
                    DB::raw('DATE_FORMAT(CONVERT_TZ(task_comments.created_at,"+00:00",@@global.time_zone), "'.$format.'") as date')])->toArray();
        return $data;
    }
}
