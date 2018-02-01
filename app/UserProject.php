<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProject extends Model
{

    protected $table = 'user_projects';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function insert()
    {

    }

    public function projectAssigned($userId, $projectId, $role, $companyId)
    {

        return $this
//            ->where(function ($query) use ($role, $userId,$companyId) {
//                if ($role == \Config::get('constants.ROLE.USER')) {
//                    $query->where(['user_projects.user_id' => $userId,'projects.company_id' => $companyId]);
//                }
//            })
            ->join('users', 'users.id', '=', 'user_projects.user_id')
            ->leftjoin('company_users', 'users.id', '=', 'company_users.user_id')
            ->leftjoin('projects', 'projects.project_id', '=', 'user_projects.project_id')
            ->where(['user_projects.project_id' => $projectId])
            ->where(['company_users.company_id' => $companyId])
//            ->where(function($query) use ($role,$userId) {
//                if ($role == 3) {
//                    $query->where('user_projects.user_id', $userId);
//                }
//            })

            ->where(['projects.project_id' => $projectId,'company_users.company_id' => $companyId])
            ->orderBy('user_projects.project_id', 'desc')
            ->groupBy('users.id')
            ->get(['users.email','users.id as user_id','users.name as username','user_projects.project_id' ,'projects.name','company_users.role', 'projects.start_date', 'user_projects.user_project_id', 'user_projects.is_admin']);
        /*->paginate(\Config::get('constants.PAGINATION_LIMIT'))*/
        //pr($data->toArray());die;
    }

    public function usersProject($userId)
    {
        return $this
            ->where('user_projects.user_id', $userId)
            ->select(['user_projects.user_project_id', 'user_projects.is_admin'])
            ->orderBy('user_projects.project_id', 'desc')
            ->get()
            ->toArray();
    }

    public function getProjectUsers($project_id)
    {

        return  $this->join('users', 'users.id', '=', 'user_projects.user_id')
            ->where(['user_projects.project_id' => $project_id])
            ->groupBy('users.id')
            ->get(['users.name', 'users.id'])

            ->toArray();
    }

    public function getNewProjectUSer($insertId)
    {

        return $this->join('users', 'users.id', '=', 'user_projects.user_id')
            ->where(['user_projects.user_project_id' => $insertId])
            ->first(['users.id', 'users.name'])->toArray();
    }

    public function getAdminsUserList($projectId){

            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->where(['project_id' => $projectId])
                ->groupBy('users.id')
                ->get(['users.id', 'users.name']);
    }
    public function projectAdminUserList($projectId,$companyId){

            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->join('company_users','users.id','=','company_users.user_id')
                ->where(['project_id' => $projectId,'company_users.role'=> '3'])
                ->groupBy('users.id')
                ->get(['users.id', 'users.name']);
    }
    public function projectAdminUserListGetSuperAdmin($projectId,$companyId){

            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->join('company_users','users.id','=','company_users.user_id')
                ->where(['project_id' => $projectId,'company_users.role'=> '1'])
                ->groupBy('users.id')
                ->first(['users.id', 'users.name']);
    }
    public function projectAdminNormalUserList($projectId,$companyId){

            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->join('company_users','users.id','=','company_users.user_id')
                ->where(['project_id' => $projectId])
                ->whereIn('company_users.role', [3,2])
                ->groupBy('users.id')
                ->get(['users.id', 'users.name']);
    }
    public function projectAdminUserListArray($projectId,$companyId){

            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->join('company_users','users.id','=','company_users.user_id')
                ->where(['project_id' => $projectId,'company_users.role'=> '3'])
                ->groupBy('users.id')
                ->get(['users.id', 'users.name'])->toArray();
    }
    public function projectAdminNoarmalUserListArray($projectId,$companyId){
            //echo $projectId;die;
            return $this->join('users', 'users.id', '=', 'user_projects.user_id')
                ->join('company_users','users.id','=','company_users.user_id')
                ->where(['project_id' => $projectId])
                ->whereIn('company_users.role', [3,2])
                ->groupBy('users.id')
                ->get(['users.id', 'users.name'])->toArray();
    }
}
