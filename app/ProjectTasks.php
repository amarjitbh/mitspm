<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProjectTasks extends Model
{
    protected $table = 'project_tasks';
    protected $primaryKey = 'project_task_id';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'project_task_id','project_id'
    ];


    public function fetchUsingColumnId($projectColumnId)
    {

        return $this
            ->where(['project_board_column_id' => $projectColumnId])
            ->orderBy('task_order_id','asc')
            ->get(['task_order_id', 'project_task_id','project_board_column_id','subject'])

            ->toArray();

    }

    public function updateRecords($whereCond, $updatedArray)
    {
        return (new ProjectTasks())->where($whereCond)->update($updatedArray);
    }

    public function fetchUsingColumnTaskId($projectColumnId, $orderId)
    {

        return $this
            ->where(['project_board_column_id' => $projectColumnId,
                'task_order_id' => $orderId
            ])
            ->first(['task_order_id', 'project_task_id', 'subject'])
            ;

    }
   /* public function fetchAllBetweenRecord($projectColumnId, $moveId, $replacedId)
    {
        return $this
            ->where(['project_board_column_id' => $projectColumnId])
            ->whereBetween('task_order_id', array($moveId,$replacedId))
            ->get(['task_order_id', 'project_task_id', 'subject'])
            ->toArray();
    }
    public function fetchAllIncreRecord($projectColumnId, $moveId, $replacedId)
    {
        return $this
            ->where(['project_board_column_id' => $projectColumnId])
            ->where('task_order_id','>',$replacedId)
            ->where('task_order_id','<=',$moveId)
            ->get(['task_order_id', 'project_task_id', 'subject'])
            ->toArray();
    }*/

    function getUsersTaskTimeDetail($sesCompanyId){

       /* $data = $this->join('projects','projects.project_id','=','project_tasks.project_id')
                ->join('company_users','company_users.company_id','=','projects.company_id')
                ->join('users','users.id','=','company_users.user_id')
                ->join('user_projects','company_users.user_id','=','user_projects.user_id')
                ->groupBy('users.id')
                ->with(['UsersTask' => function($query){
                    //$query->select(['project_task_assignees.logging_time']);
                }])
                ->where(['company_users.company_id' => $sesCompanyId])
                ->select(['project_tasks.subject','project_tasks.project_task_id','users.name'])
                ->get(
                    //'project_task_logged_time.start_time',
                    /*DB::raw('SUM(TIME_TO_SEC(project_task_logged_time.start_time)) as start_time'),
                    'project_task_logged_time.end_time'
                );*/


      /*  $data = (new CompanyUser())
                    ->join('users','users.id','=','company_users.user_id')
                    ->join('projects','projects.company_id','=','company_users.company_id')
                    ->join('project_tasks','project_tasks.project_id','=','projects.project_id')
                    ->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
                    //->groupBy('users.id')
                    ->select(['project_tasks.subject','project_tasks.project_task_id','users.name','project_task_assigne_id'])
                    ->get()->toArray();
        pr($data);die;*/





    }

    function taskLogedTime(){

         return $this->hasMany(ProjectTaskLoggedTime::class,'project_task_id');
    }

    function UsersTask(){

         //return $this->hasMany(ProjectTaskLoggedTime::class,'project_task_id');
        return $this->hasMany(ProjectTaskAssignees::class,'project_task_id');
    }
}