<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class ProjectTaskAssignees extends Model
{
    public $taskIds = [];
    protected $table = 'project_task_assignees';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function fetchAllLoggedTime($projectTaskId,$userId)
    {
        return $this
            ->where('user_id', '=', $userId)
            ->where('project_task_id', '=', $projectTaskId)
            ->first(['project_task_assigne_id', 'logging_time','time_taken_in_seconds']);

    }

    function getAssignUsersDetail($asnId){
        $aId = implode($asnId,',');

        return $this->join('users','users.id','=','project_task_assignees.user_id')
            ->whereIn('project_task_assignees.project_task_assigne_id',$asnId)
            ->get(['id','name'])->toArray();

    }


    function projectTasks(){

        return $this->hasMany(ProjectTasks::class, 'project_task_id')->whereIn('project_task_id',$this->taskIds);
        //return $this->hasMany(ProjectBoardColumn::class, 'project_board_id')->whereIn('project_board_column_id', $this->columns);
    }
    public function getUsersTasks($tasks,$user_id,$limit=''){

        $tsk = explode(',',$tasks);
        $data = $this->whereIn('project_task_assignees.project_task_id',$tsk)
            ->where(['project_task_assignees.user_id' => $user_id])
            ->join('project_tasks','project_tasks.project_task_id','=','project_task_assignees.project_task_id');
            if(!empty($limit)){

                $data = $data->limit($limit);
            }
        $data =$data->get(['project_task_assignees.project_task_id',
                'project_tasks.subject',
                DB::Raw('project_task_assignees.logging_time'),
            ]);
        return $data;
         //pr($data);die;

    }

    public function getusersWorkTime($sesCompanyId,$dateOne,$dateTwo,$userId){
        //echo $sesCompanyId;die;
        $result = '';
        $userTime = '';
        $whereDate = '';
        if(!empty($dateOne) && !empty($dateTwo)) {

            $whereDate = "AND date(`project_task_logged_time`.`start_time`) >= '".$dateOne."' and date(`project_task_logged_time`.`start_time`) <= '".$dateTwo."'" ;
        }
        $whereUserID = '';
        if(!empty($userId)){
            $whereUserID = 'where t1.user_id = "'.$userId.'"';
        }
        //echo $whereDate;die;
        $data = DB::select("select t1.project_task_assigne_id,
                          t1.user_id,
                          Substring_index(Group_concat(DISTINCT t1.project_task_id ORDER BY t1.project_task_id
                                                       DESC
                                                       SEPARATOR ','), ',', 5) as project_task_id,
                          Group_concat(DISTINCT t1.project_task_id ORDER BY t1.project_task_id
                                                       DESC ) as project_task_ids
                        from project_task_assignees t1
                        inner join
                        (
                          select GROUP_CONCAT(project_task_id) pro_id, user_id
                          from project_task_assignees
                          group by user_id
                        ) t2
                          on t1.user_id = t2.user_id
                        INNER JOIN `project_task_logged_time`

                                          on `t1`.`user_id` =
                                              `project_task_logged_time`.`logged_by_user_id`
                        AND t1.project_task_id = project_task_logged_time.project_task_id
                        INNER JOIN company_users ON company_users.user_id = t1.user_id
                        AND company_users.company_id = ".$sesCompanyId."
                        INNER JOIN project_tasks ON project_tasks.project_task_id = t1.project_task_id
                        INNER JOIN projects ON projects.project_id = project_tasks.project_id
                        AND projects.company_id = ".$sesCompanyId."
                        ".$whereDate."
                        ".$whereUserID."
                        group by user_id
                     ");
        //pr($data);die;
        $taskId = array_column($data,'project_task_id');
        $projectTaskIds = array_column($data,'project_task_ids');
        $taskUSers = array_column($data,'user_id');//pr($taskUSers);
        $taskId = explode(',', implode($taskId,','));//pr($taskId);die;

        $projectTaskIds = explode(',', implode($projectTaskIds,','));;
        $where = '';
        $whereOne = '';
        foreach($data as $ind => $dt){


                if ($ind <= 0) {
                    $whereOne = "project_task_assignees.user_id = '" . $dt->user_id . "' AND project_task_assignees.project_task_id IN (" . $dt->project_task_ids . ")";
                } else {

                    $whereOne .= "OR project_task_assignees.user_id = '" . $dt->user_id . "' AND project_task_assignees.project_task_id IN (" . $dt->project_task_ids . ")";
                }

                if ($ind <= 0) {
                    $where = "project_task_assignees.user_id = '" . $dt->user_id . "' AND project_task_assignees.project_task_id IN (" . $dt->project_task_id . ")";
                } else {

                    $where .= "OR project_task_assignees.user_id = '" . $dt->user_id . "' AND project_task_assignees.project_task_id IN (" . $dt->project_task_id . ")";
                }


        }
        if(!empty($data)) {
            $result = $this
                ->leftjoin('users', 'users.id', '=', 'project_task_assignees.user_id')
                ->leftjoin('project_tasks', 'project_tasks.project_task_id', '=', 'project_task_assignees.project_task_id')
                ->join('projects', 'project_tasks.project_id', '=', 'projects.project_id')
                ->join('company_users', function ($join) {
                    $join->on('company_users.user_id', '=', 'users.id');
                    $join->on('company_users.company_id', '=', 'projects.company_id');
                })
                ->join('project_task_logged_time', function ($join) {
                    $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                    $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');

                })
                ->where(function ($query) use ($dateOne, $dateTwo, $userId, $where) {

                    if (!empty($dateOne) && !empty($dateTwo)) {

                        /*$query->whereDate('project_task_logged_time.start_time', '>=', "2017-07-28");
                        $query->whereDate('project_task_logged_time.start_time', '<=', "2017-07-28");*/


                        $query->whereDate('project_task_logged_time.start_time', '>=', $dateOne);
                        $query->whereDate('project_task_logged_time.start_time', '<=', $dateTwo);
                    }
                })
                ->where(function ($query) use ($dateOne, $dateTwo, $userId, $where) {
                    if (!empty($userId)) {
                        $query->where(['project_task_assignees.user_id' => $userId]);
                    } else {
                        if (!empty($where)) {
                            $query->whereRaw($where);
                        }
                    }
                })
                ->groupBy('users.id', 'project_tasks.project_task_id')
                ->orderBy('project_task_assignees.project_task_assigne_id', 'DESC');
            if (empty($userId)) {
                $result = $result->get(['users.id', 'users.name', 'project_task_assignees.project_task_id', 'project_tasks.subject',
                    DB::Raw('GROUP_CONCAT(case when project_task_logged_time.start_time IS NULL then NOW() else project_task_logged_time.start_time end ) as start_time'),
                    DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
                    DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                    'project_task_assignees.logging_time',
                ]);
            } else {
                $result = $result->select(['users.id', 'users.name', 'project_task_assignees.project_task_id', 'project_tasks.subject',
                    DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                    DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
                    DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                    'project_task_assignees.logging_time',
                ])->paginate('5');
            }


            $userTime = $this
                ->leftjoin('users', 'users.id', '=', 'project_task_assignees.user_id')
                ->leftjoin('project_tasks', 'project_tasks.project_task_id', '=', 'project_task_assignees.project_task_id')
                ->join('projects', 'project_tasks.project_id', '=', 'projects.project_id')
                ->join('company_users', function ($join) {
                    $join->on('company_users.user_id', '=', 'users.id');
                    $join->on('company_users.company_id', '=', 'projects.company_id');
                })
                ->join('project_task_logged_time', function ($join) {
                    $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                    $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');

                })
                ->where(function ($query) use ($dateOne, $dateTwo, $userId, $where) {

                    if (!empty($dateOne) && !empty($dateTwo)) {

                        $query->whereDate('project_task_logged_time.start_time', '>=', $dateOne);
                        $query->whereDate('project_task_logged_time.start_time', '<=', $dateTwo);

                        /*$query->whereDate('project_task_logged_time.start_time', '>=', "2017-07-28");
                        $query->whereDate('project_task_logged_time.start_time', '<=', "2017-07-28");*/
                    }
                })
                ->where(function ($query) use ($dateOne, $dateTwo, $userId, $whereOne) {
                    if (!empty($userId)) {
                        $query->where(['project_task_assignees.user_id' => $userId]);
                    } else {
                        if (!empty($whereOne)) {
                            $query->whereRaw($whereOne);
                        }
                    }
                })
                ->groupBy('users.id')
                //->groupBy('users.id','project_tasks.project_task_id')
                ->orderBy('project_task_assignees.project_task_assigne_id', 'DESC');
            if (!empty($dateOne) && !empty($dateTwo)) {

                $userTime = $userTime->get(['users.id',
                    DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                    DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
                    //DB::Raw('SEC_TO_TIME( SUM(DISTINCT TIME_TO_SEC( project_task_assignees.logging_time ) ) ) AS timeSum'),
                    //DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                    //'project_task_assignees.logging_time',
                ]);
            } else {
                $userTime = $userTime->get(['users.id',
                    DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                    DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00"); else project_task_logged_time.end_time end ) as end_time'),
                    DB::Raw('SEC_TO_TIME( SUM(DISTINCT TIME_TO_SEC( project_task_assignees.logging_time ) ) ) AS timeSum'),
                    //DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                    //'project_task_assignees.logging_time',
                ]);
            }
        }
        //pr($result->toArray());die;
        //pr($userTime->toArray());die;
        return [

            'result' => $result,
            'userTime' => $userTime,
        ];
    }

    function getSingleUsersWorkTime($sesCompanyId,$dateOne,$dateTwo,$userId){

        return $this
            ->leftjoin('users','users.id','=','project_task_assignees.user_id')
            ->leftjoin('project_tasks','project_tasks.project_task_id','=','project_task_assignees.project_task_id')
            ->join('project_task_logged_time', function($join)
            {
                $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');
            })
            ->where(function($query) use($dateOne,$dateTwo,$userId){
                if(!empty($userId)) {
                    $query->where(['project_task_assignees.user_id' => $userId]);
                }
                if(!empty($dateOne) && !empty($dateTwo)) {

                    $query->whereDate('project_task_logged_time.start_time','>=',$dateOne);
                    $query->whereDate('project_task_logged_time.start_time','<=',$dateTwo);
                }
            })
            ->groupBy('users.id','project_tasks.project_task_id')
            ->orderBy('project_task_assignees.project_task_assigne_id', 'DESC')
            ->select(['users.id','users.name','project_task_assignees.project_task_id','project_tasks.subject',
                DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time ORDER BY project_task_logged_time.project_task_logged_time_id DESC) as start_time'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.end_time ORDER BY project_task_logged_time.project_task_logged_time_id DESC) as end_time'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                DB::Raw('GROUP_CONCAT(project_task_assignees.logging_time) as loggTimes'),
                'project_task_assignees.logging_time'
            ])->paginate('5');
    }

    public function ProjectsTasks(){

        return (new Project())->hasMany(ProjectTasks::class,'project_id');
    }

    function taskReports(){

        $data = (new Project())
                    ->join('companies','projects.company_id','=','companies.company_id')
                    ->join('user_settings','user_settings.project_id','=','projects.project_id')
                    //->with('ProjectsTasks')
                          //$query->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id');
                    //}])
                    ->get(['projects.company_id','companies.name as company_name','projects.name','projects.project_id','user_settings.email'])
                    ->toArray();
        pr($data);die;

        $result = $this
            ->leftjoin('users', 'users.id', '=', 'project_task_assignees.user_id')
            ->leftjoin('project_tasks', 'project_tasks.project_task_id', '=', 'project_task_assignees.project_task_id')
            ->join('projects', 'project_tasks.project_id', '=', 'projects.project_id')
            ->join('company_users', function ($join) {
                $join->on('company_users.user_id', '=', 'users.id');
                $join->on('company_users.company_id', '=', 'projects.company_id');
            })
            ->join('companies','company_users.company_id','=','companies.company_id')
            ->join('project_task_logged_time', function ($join) {
                $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');

            })
            ->join('user_settings',function($join){
                $join->on('companies.company_id','=','user_settings.company_id');
                $join->on('users.id','=','user_settings.user_id');
                $join->on('projects.project_id','=','user_settings.project_id');
                $join->where(['user_settings.report' => '1']);
            })
            ->whereDate('project_task_logged_time.start_time', '=', date('Y-m-d'))
            ->groupBy('users.id','project_tasks.project_task_id')
            ->orderBy('project_task_assignees.project_task_assigne_id', 'DESC')
            ->get(['company_users.company_id','companies.name as company_name','user_settings.email',
                'users.id as user_id', 'users.name',
                'projects.project_id','projects.name as project_name',
                'project_task_assignees.project_task_id',
                'project_tasks.subject',
                DB::Raw('GROUP_CONCAT(case when project_task_logged_time.start_time IS NULL then NOW() else project_task_logged_time.start_time end ) as start_time'),
                DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then NOW() else project_task_logged_time.end_time end ) as end_time'),
                DB::Raw('GROUP_CONCAT(project_task_logged_time.logged_by_user_id) as logged_by_user_id'),
                'project_task_assignees.logging_time',
            ])->toArray();
        return $result;
    }

}
