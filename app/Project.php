<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'name', 'user_id', 'start_date', 'end_date'
    ];
    protected $primaryKey = 'project_id';

    public function fetchResult($id, $userId)
    {
        return $this
            ->where('projects.project_id', $id)
            ->where(function ($query) use ($userId) {
                $query->Where('projects.created_user_id', '=', $userId)
                    ->orWhere('projects.updated_user_id', '=', $userId);
            })
            ->first();
    }

    public function updateRecord($updateArray, $id){
       return $this
            ->where('project_id', $id)
            ->update($updateArray);
    }

    public function fetchProjectDetail($projectId)
    {
        return $this
            ->where('projects.project_id', $projectId)
            ->first(['company_id']);
    }

    public function fetchAllUsersOfTheProject($projectId){
        return $this
            ->where('projects.project_id',$projectId)
            ->join('projects', 'projects.project_id', '=', 'user_projects.project_id')
          //  ->join('user_projects', 'user_projects.project_id', '=', 'project_invites.project_id')
            ->select()
            ->orderBy('projects.project_id', 'desc')
            ->get();
    }

    public function getUsersProjects($userId,$companyId){

        return $this
                    ->join('user_projects','user_projects.project_id','=','projects.project_id')
                    ->join('project_boards','project_boards.project_id','=','projects.project_id')
                    ->where(['user_projects.user_id' => $userId,'user_projects.deleted_at' => null,'projects.company_id' => $companyId])
                    ->orderBy('project_boards.project_board_id', 'ASC')
                    ->groupBy('projects.project_id')
                    ->get(['projects.name','projects.project_id','project_boards.project_board_name','project_boards.project_board_id']);
    }

    public function getAdminsProjects($companyId){

        return $this
            ->join('project_boards','project_boards.project_id','=','projects.project_id')
            ->groupBy('projects.project_id')
            ->where(['projects.company_id' => $companyId])
            ->get(['projects.name','projects.project_id','project_boards.project_board_name','project_boards.project_board_id']);
    }


    function fetchColumns($boardColumnId,$userId){
        /***  $bid -> board Ids  ***/
        return $this
            ->leftjoin('projects','projects.project_id','=','project_boards.project_id')
            ->leftjoin('project_boards','project_boards.project_board_id','=','project_board_column.project_board_id')
            ->leftjoin('user_project_columns','user_project_columns.project_board_column_id','=','project_board_column.project_board_column_id')
            ->where(['project_boards.project_board_id' => $boardColumnId])
            ->where(['user_project_columns.user_id' => $userId])
            ->get();

    }

    function ProjectsTasksReturns(){

        return $this->hasMany(ProjectTasks::class,'project_id');
    }
}
