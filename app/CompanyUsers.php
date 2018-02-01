<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CompanyUsers extends Model
{

    protected $table = 'company_users';

    public function getCompanyUserData()
    {
        //echo \Auth::user()->id;die;
        $result = DB::table('company_users')
            ->leftJoin('companies', 'companies.company_id', '=', 'company_users.company_id')
            //->where('user_id', '=', \Auth::user()->id)
            //->where(['company_users.role' => Config::get('constants.ROLE.SUPERADMIN'),'user_id' => \Auth::user()->id])
            ->where(['user_id' => \Auth::user()->id])
            ->groupBy('companies.company_id')
            ->get();

        return $result;
    }


    public function getCompanyUsersProjects($id)
    {
        return (new CompanyUsers())
            ->join('projects', 'projects.company_id', '=', 'company_users.company_id')
            ->join('companies', 'companies.company_id', '=', 'company_users.company_id')
            ->join('users', 'users.id', '=', 'company_users.user_id')
            ->where(['company_users.company_id' => $id])
            ->get(['company_users.company_id',
                'company_users.role',
                'company_users.user_id',
                'projects.project_id',
                \DB::raw("companies.name as company"),
                \DB::raw("projects.name as project"),
                \DB::raw("users.name as user"),
            ])->toArray();
    }

    /* public function getCompanyUserProjects($id,$userId,$role){
         return (new CompanyUsers())

             ->join('projects','projects.company_id','=','company_users.company_id')
             ->join('companies','companies.company_id','=','company_users.company_id')
             ->join('users','users.id','=','company_users.user_id')
             ->join('user_projects','projects.project_id','=','user_projects.project_id')
             ->where(['company_users.company_id' => $id])
             ->where(['company_users.user_id' => $userId])
             ->where(function ($query) use ($role,$userId) {
                 if ($role == '3') {

                     $query->where(['user_projects.user_id' => $userId]);
                 }})
             ->select(['company_users.company_id',
                 'company_users.role',
                 'company_users.user_id',
                 'projects.project_id',
                 \DB::raw("companies.name as company"),
                 \DB::raw("projects.name as project"),
                 \DB::raw("users.name as user"),
             ])
             //->get()->toArray();
             ->paginate(\Config::get('constants.PAGINATION_LIMIT'));
     }*/

    /* public function companyUserProjectsBoards($cid,$userId,$roleSes){

         return $this->with(['companiesList' => function ($query) {
                     $query->select(['company_id','name']);
                 }])->get()->toArray();
     }*/

    /*public function companiesList(){

        return $this->hasMany(Companies::class,'company_id');
    }*/
    public function projectsList()
    {

        return $this->hasMany(Project::class, 'company_id');
    }

    public function boardsList()
    {

        return $this->hasMany(ProjectBoards::class, 'project_id');
    }

    public function fetchUserRole($companyId)
    {
        //echo $companyId.'='.\Auth::user()->id;die;
        return $this
            ->where('user_id', '=', \Auth::user()->id)
            ->where('company_id', '=', $companyId)
            ->first(['role']);
    }

    public function getCompanyUsers($cid)
    {

        return $this->join('users', 'users.id', '=', 'company_users.user_id')
            ->where(['company_users.company_id' => $cid])->get()->toArray();
    }

    public function AssignProjectToMainAdmin($companyId,$role=null)
    {
        return $this
            ->join('users', 'users.id', '=', 'company_users.user_id')
            ->Where(['company_users.company_id' => $companyId,'company_users.role' => Config::get('constants.ROLE.SUPERADMIN')])
            ->orWhere(function($query)use($role){
                if($role == Config::get('constants.ROLE.ADMIN')){

                    $query->Where(['company_users.role' => Config::get('constants.ROLE.ADMIN')]);
                }
            })
            ->groupBy('users.id')
            ->get(['users.email'])
            ->toArray();
    }
}
