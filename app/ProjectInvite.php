<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectInvite extends Model
{

    protected $table = 'project_invites';
    protected $fillable = [
       '*'
    ];
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function fetchProjects($userId, $projectId,$role,$companyId){
        return $this
            ->where(function($query) use($role, $userId) {
                if ($role == \Config::get('constants.ROLE.USER')) {
                    $query->where(['project_invites.invited_by_user_id'=>$userId]);
                }
            })
            ->leftjoin('users', 'users.id', '=', 'project_invites.invited_by_user_id')
            ->leftjoin('projects', 'projects.project_id', '=', 'project_invites.project_id')
            ->where(['project_invites.project_id'=>$projectId,'projects.company_id' => $companyId])
            ->orderBy('project_id', 'desc')
            ->get(['users.email','users.name as username','projects.name','projects.start_date','project_invites.*'])
            ;
           /* ->paginate(\Config::get('constants.PAGINATION_LIMIT'))*/
    }

   /* public function alreadyExists($userId,$projectId){
        return $this
            ->where(['user_id'=>$userId,'project_id'=>$projectId])
            ->first();

    }*/

    public function ProjectExist($email,$projectId){
        return $this
            ->where(['email'=>$email,'project_id'=>$projectId])
            ->first();

    }

    public function UniqueResult($token){
        return $this->where('unique_token','=',$token)->first(['email','is_admin','project_id','project_invite_id']);
    }
}
