<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Companies extends Model{

    protected $table = 'companies';

    public function companyUserProjectsBoards($cid){

        return $this->with(['CompanyUsers' => function ($query) {
            $query->select(['company_id','user_id','role']);
        }])->with(['projectsList' => function ($query) use($cid){
            $query->select(['company_id','name','project_id']);
            $query->where(['projects.company_id' => $cid]);
            //$query->where(['user_projects.deleted_at' => null]);
            $query->groupBy('projects.project_id');
        }])->where(['companies.company_id' => $cid])->first()->toArray();
    }

    public function companyUserProjectsBoardsNormal($cid,$userId){

        return $this->with(['CompanyUsers' => function ($query) {
            $query->select(['company_id','user_id','role']);
        }])->with(['projectsList' => function ($query) use($userId,$cid) {
            $query->leftjoin('user_projects','user_projects.project_id','=','projects.project_id');
            $query->where(['user_projects.user_id'=>$userId,'user_projects.deleted_at' => null]);
            $query->where(['projects.company_id' => $cid]);
            $query->groupBy('projects.project_id');
        }])
        ->where(['companies.company_id' => $cid])
        ->first()->toArray();
    }

    /*public function companyUserProjectsBoards($cid){
->with(['projectsBoards' => function ($query) {
            //$query->join('project_boards','project_boards.project_id','=','projects.project_id');
            //$query->select(['company_id','name','project_id']);
        }])
            return $this->join('company_users','company_users.company_id','=','companies.company_id')
                ->join('projects','projects.company_id','=','company_users.company_id')
                ->join('users','users.id','=','company_users.user_id')
                ->join('project_boards','project_boards.project_id','=','projects.project_id')
                ->where(['companies.company_id' => $cid])
                ->get(['companies.company_id',
                        'companies.name',
                        'company_users.user_id',
                        'company_users.role',
                        'projects.project_id',
                        'projects.name',
                        'project_boards.project_board_id',
                        'project_boards.project_board_name',
                ])->toArray();
    }*/

    public function CompanyUsers(){

        return $this->hasMany(CompanyUsers::class, 'company_id','company_id');
    }

    public function projectsList(){

        return $this->hasMany(Project::class, 'company_id', 'company_id');
    }
    public function projectsBoards(){

        return $this->hasMany(ProjectBoards::class, 'project_id');
    }

    public function taskReports($companyId){

      /*  $data = $this
            ->join('company_users','company_users.company_id','=','companies.company_id')
            ->join('users','users.id','=','company_users.user_id')
            ->join('projects','users.id','=','company_users.user_id')
            ->join('project_tasks','project_tasks.project_id','=','projects.project_id')
            ->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
            ->join('project_task_logged_time','project_task_logged_time.project_task_id','=','project_task_assignees.project_task_id')
            ->groupBy('project_tasks.project_task_id','users.id','companies.company_id')
            ->whereDate('project_task_logged_time.start_time', '>=', date('Y-m-d'))
            //->where(['companies.company_id' => $companyId])
            ->get(['companies.company_id',
                'companies.name as company_name',
                'users.id','users.name as user_name',
                'projects.project_id',
                'projects.name',
                'project_tasks.project_task_id',
                'project_tasks.subject',
                DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time ORDER BY project_task_logged_time.project_task_logged_time_id DESC) as start_time'),
                DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then NOW() else project_task_logged_time.end_time end ) as end_time'),
            ])->toArray();
        pr($data);*/


        $data = $this
            ->join('company_users','company_users.company_id','=','companies.company_id')
            ->join('users','users.id','=','company_users.user_id')
            ->join('projects','projects.company_id','=','companies.company_id')
            ->join('user_settings',function($join){
                $join->on('companies.company_id','=','user_settings.company_id');
                $join->on('users.id','=','user_settings.user_id');
                $join->on('projects.project_id','=','user_settings.project_id');
                $join->where(['user_settings.report' => '1']);
            })
            ->with(['ProjectTasks' => function($query){
                    //$query->join('project_tasks','project_tasks.project_id','=','projects.project_id');
                    $query->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id');
                    //$query->join('project_task_logged_time','project_task_logged_time.project_task_id','=','project_task_assignees.project_task_id');
                    $query->join('project_task_logged_time', function ($join) {
                        $join->on('project_tasks.project_task_id', '=', 'project_task_logged_time.project_task_id');
                        $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');

                    });
                    $query->join('users','users.id','=','project_task_assignees.user_id');
                    $query->groupBy('users.id','project_tasks.project_task_id');
                    $query->whereDate('project_task_logged_time.start_time', '=', date('Y-m-d'));
                    $query->orderBy('project_task_assignees.project_task_assigne_id', 'DESC');
                    $query->select(['project_tasks.project_id',
                        'project_task_assignees.project_task_id',
                        'project_tasks.subject','users.name as user_name','users.id as user_id',
                        DB::Raw('GROUP_CONCAT(project_task_logged_time.start_time) as start_time'),
                        DB::Raw('GROUP_CONCAT(case when project_task_logged_time.end_time IS NULL then NOW() else project_task_logged_time.end_time end ) as end_time'),
                    ]);

                    //$query->get(['project_tasks.project_id']);
            }])
            //->groupBy('user_settings.email')
            ->get(['companies.company_id','companies.name as company_name',
                    'projects.project_id','projects.name as project_name',
                    'user_settings.email as user_email'
            ])->toArray();
        return $data;
    }

    public function companyProjects(){

        return $this->hasMany(Project::class,'company_id');
    }
    public function ProjectTasks(){

        return (new Project())->hasMany(ProjectTasks::class,'project_id');
    }

    public function companyUsersData($companyId){

        return $this->join('company_users','company_users.company_id','=','companies.company_id')
            ->join('users','users.id','=','company_users.user_id')
            ->where(['companies.company_id' => $companyId,'company_users.role' => '1'])
            ->first(['companies.name','users.email']);
    }
}
