<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserSettings;
use App\Project;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return redirect('dashboard');
        return view('home');
    }


    public function errorPage404(){
        return view('errors.404');
    }

    public function errorPage500(){
        return view('errors.500');
    }


    public function updateUserSettingTable(){

        $userSetting = (new UserSettings())
            ->get(['project_id'])->toArray();
        $projectId =  array_column($userSetting,'project_id');

        $projectTableData = (new Project())
            ->join('company_users','company_users.company_id','=','projects.company_id')
            ->join('users','users.id','=','company_users.user_id')
            ->whereNotIn('project_id', $projectId)
            ->where('company_users.role', 1)
            ->get(['projects.project_id','company_users.company_id','company_users.user_id','users.email'])->toArray();


        foreach($projectTableData as $projectData){

            \DB::table('user_settings')->insert([[
                'company_id' => $projectData['company_id'],
                'user_id' => $projectData['user_id'],
                'project_id' => $projectData['project_id'],
                'report' => 1,
                'email' => $projectData['email'],
                'created_at' => date('Y-m-d :h:i:s')
            ]]);
        }

    }
}
