<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompanyUser extends Model
{
    public function fetchResultAccordingToCompany($userId){
    return $this
        ->where('user_id','=',$userId)
        ->first();
    }
    public function getCurrentTaskData($sesCompanyId){




        return $this->join('users','users.id','=','company_users.user_id')
            ->join('projects','projects.company_id','=','company_users.company_id')
            ->join('project_tasks','project_tasks.project_id','=','projects.project_id')
            //->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
            ->join('project_task_assignees', function($join)
            {
                $join->on('project_task_assignees.project_task_id','=','project_tasks.project_task_id');
                $join->on('project_task_assignees.user_id','=','users.id');
            })
            ->join('project_task_logged_time', function($join)
            {
                $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id')
                    ->whereDate('project_task_logged_time.start_time','=',date('Y-m-d'))
                    ->whereNull('project_task_logged_time.end_time');
            })
            ->groupBy('users.id','project_tasks.project_task_id')
            ->orderBy('project_tasks.project_task_id','DESC')
            ->where(['company_users.company_id'=>$sesCompanyId])
            ->get(['users.name','users.id',
                'project_task_assignees.project_task_id as task_id',
                'project_tasks.subject',
                'project_task_logged_time.start_time as current_start_time',
                DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.end_time) as end_time'),
                'project_task_assignees.logging_time'
            ]);
    }


}
