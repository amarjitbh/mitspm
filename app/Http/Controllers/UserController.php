<?php

namespace App\Http\Controllers;

use App\Companies;
use App\CompanyUser;
use App\Countries;
use App\CountriesTimeZone;
use App\Http\Requests\RegisterUser;
use App\Http\Requests\UserLogin;
use App\MessageToBeSend;
use App\ProjectInvite;
use App\ProjectTaskAssignees;
use App\ProjectTaskLoggedTime;
use App\UserProject;
use App\UserProjectColumn;
use App\ProjectBoards;
use App\UserSettings;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\CompanyUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Mockery\CountValidator\Exception;
use App\CompanySetting;

class UserController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            if (!empty($userId)) {

                $taskId = (new ProjectTaskLoggedTime())
                        ->where(['logged_by_user_id' => $userId, 'end_time' => NULL])
                        ->first(['project_task_id']);
                $tasksTime = (array)$taskId;
                if (!empty($tasksTime)) {//pr($taskId->project_task_id);die;
                    $inputs = [
                            'check' => 'false',
                            'new_project_task_id' => '0',
                            'project_task_id' => $taskId->project_task_id,
                            'value' => 'Pause',
                    ];
                    $result = (new ProjectBoardsController())->ajaxEndTaskLoggin($request, $inputs);
                    //pr($result);die;
                }
            }
            Auth::logout();
            $request->session()->put('company_id', 0);
            $request->session()->put('country_timezone_id', 0);
            Session::flush();
            return redirect()->route('login');
        }catch(Exception $e){

            pr($e->getMessage());
        }
    }

    function UsersDashboard(Request $request)
    {
        /*** companies List  ***/
        $companyUser = new CompanyUsers();
        $data['CompaniesList'] = $companyUser->getCompanyUserData(\Auth::user()->id);
        if(count($data['CompaniesList']) == 1 && !empty($data['CompaniesList'])){

            $request->session()->set('company_id', $data['CompaniesList'][0]->company_id);
            $request->session()->set('user_role', $data['CompaniesList'][0]->role);

            /*  set user session according to user id and companyid  */

            $dateTimes = (new CompanySetting())->take(2)->where('company_id', $data['CompaniesList'][0]->company_id)
                ->orderBy('company_setting_id', 'desc')->get(['meta_value'])->toArray();

            $metaValueDate = isset($dateTimes[1]['meta_value']) ? $dateTimes[1]['meta_value'] : '';
            $metaValueTime = isset($dateTimes[0]['meta_value']) ? $dateTimes[0]['meta_value'] : '';

            $usersCountryTimezone = (new \App\User())->where(['id' => Auth::user()->id])->first(['country_timezone_id']);
            $request->session()->set('company_setting_date', $metaValueDate);
            $request->session()->set('company_setting_time', $metaValueTime);
            $request->session()->set('country_timezone_id', $usersCountryTimezone->country_timezone_id);

            return redirect()->route('dashboard');
        }else{

            return view('users.companies-list', $data);
        }
    }

    public function writeSessionRole(Request $request, $id)
    {
        $request->session()->set('company_id', $id);
        $fetchUserRole = (new CompanyUsers())->fetchUserRole($id);
        $request->session()->set('user_role', $fetchUserRole->role); // assigning role in session
        /* set session according to company id  */
        $dateTimes = (new CompanySetting())->take(2)->where('company_id',$id)
            ->orderBy('company_setting_id', 'desc')->get(['meta_value'])->toArray();

        $metaValueDate = isset($dateTimes[1]['meta_value']) ? $dateTimes[1]['meta_value'] : '';
        $metaValueTime = isset($dateTimes[0]['meta_value']) ? $dateTimes[0]['meta_value'] : '';
        $usersCountryTimezone = (new \App\User())->where(['id' => Auth::user()->id])->first(['country_timezone_id']);
        $request->session()->set('company_setting_date', $metaValueDate);
        $request->session()->set('company_setting_time', $metaValueTime);
        $request->session()->set('country_timezone_id', $usersCountryTimezone->country_timezone_id);

        $cid = $request->session()->get('company_id');
        $company = (new Companies())->where(['company_id' => $cid])->first(['name']);
        $company = $company->name;
        /* end  */

        /*  if ($fetchUserRole->role == Config::get('constants.ROLE.ADMIN')
              || $fetchUserRole->role == Config::get('constants.ROLE.SUPERADMIN')
          ) {*/
        return redirect()->route('dashboard');
        /* } else {
             return redirect()->route('assigned-user');
         }*/
    }

    function UserCompany(Request $request)
    {
        /***  Admin Dashboard  ***/
        $data = '';

        $userId = \Auth::user()->id;
        //$request->session()->set('company_id', $id);
        $roleUSer = \Config::get('constants.ROLE.USER');
        $admin = \Config::get('constants.ROLE.ADMIN');
        $data['boardId'] = '';
        $userId = \Auth::user()->id;
        $cid = $request->session()->get('company_id');
        $fetchUserRole = (new CompanyUsers())->fetchUserRole($cid);
        if(is_object($fetchUserRole)) {

            $request->session()->set('user_role', $fetchUserRole->role); // assigning role in session
            $roleSes = $request->session()->get('user_role');
            $data['user_role'] = $roleSes;
            if ($roleSes == $roleUSer || $roleSes == $admin) {
                $data['company'] = (new Companies())->companyUserProjectsBoardsNormal($cid, $userId);
            } else {

                $data['company'] = (new Companies())->companyUserProjectsBoards($cid, $userId);
            }
            return view('users.users-companies-projects', $data);
        }else{

            return redirect('companies');
        }
    }


    public function changePassword()
    {
        return view('users.change-password');
    }

    public function postChangePassword(Request $request)
    {


        $validatorRules = [
            'old_password' => 'required|min:6|max:12',
            'password' => 'required|min:6|max:12',
            'confirm_password' => 'bail|required|min:6|max:12|same:password',
        ];

        $this->validate($request, $validatorRules);

        if (Auth::attempt(['email' => Auth::user()->email, 'password' => $request->input('old_password')])) {
            $user = Auth::user();
            $user->password = Hash::make($request->input('password'));
            $updatePassword = $user->save();
            if($updatePassword){

                $finalData [] = [
                    'message_type' => 2,
                    'subject' => 'Change password',
                    'message_body' => 'Change password successfully done',
                    'email' => Auth::user()->email,
                    'created_at' => date('Y-m-d :h:i:s')

                ];

                (new MessageToBeSend())->insert($finalData);
            }
            $request->session()->flash('success', 'Password updated successfully.');
            return redirect()->back();
        } else {
            return redirect()->route('changePassword')->withErrors(['Please provide valid old password.']);
        }

        return view('users.change-password');
    }

    public function assignProjectAdmin()
    {

        $userId = Auth::user()->id;
        $fetchProjects = (new UserProject())->projectAssigned($userId);

        return view('users.assign-admin', compact('fetchProjects'));
    }

    public function assignProjectAdminPost(Request $request)
    {
        $input = $request->all();

        if (count($input['assign_admin']) > 0) {
            foreach ($input['assign_admin'] as $checkedId) {
                $update = (new UserProject())->where('user_project_id', $checkedId)
                    ->update(['is_admin' => 1]);
            }
            $request->session()->flash('success', 'User(s) is mark as Admin for selected project.');
            return redirect()->route('assign-admin');
        } else {
            $request->session()->flash('error', 'Please select checkbox to mark user as "Admin" of the Project');
            return redirect()->route('assign-admin');
        }
    }

    public function getTimeZone(Request $request)
    {
        //try {
            $countryId = $request->input('country_id');

            $timeZone = (new CountriesTimeZone())->getTimezone($countryId);

            if (!empty($timeZone)) {
                return view('users.get-time-zone', compact('timeZone'));
            } else {

                echo '0';
            }

    }

    function getRegister(Request $request,$projectId=null,$invitedByUser=null)
    {
        try {
            $token = "";
            $inputs = $request->all();
            $companyInfo = '';
            $company = '';
            if (!empty($inputs) && isset($inputs['unique_token'])) {
                $token = $inputs['unique_token'];
                //echo $token;die;
                $data['projectInviteResult'] = (new ProjectInvite())->UniqueResult($token);
                if(!empty($data['projectInviteResult']->project_id)) {
                    $companyInfo = (new Project())->where(['project_id' => $data['projectInviteResult']->project_id])->first(['company_id']);
                    $companyInfo = $companyInfo->company_id;
                    $company = (new Companies())->where(['company_id' => $companyInfo])->first(['name']);
                    $company = $company->name;
                }
            }
            $data['parameter'] = array('projectId' => $projectId,
                'invitedByUser' => $invitedByUser,
                'unique_token' => $token,
                'company_name' => $company,
                'company_id' => $companyInfo
            );
            $data['countries'] = (new Countries())->get();
            //pr($data['projectInviteResult']);die;
            return view('users.register', $data);
        }catch (Exception $e){
            pr($e->getMessage());
        }
    }

    function postRegister(RegisterUser $request)
    {
        try {

            //DB::beginTransaction();
            $inputs = $request->all();
            //pr($inputs);die;
            $name = $request->input('name');
            $password = $request->input('password');
            $email = $request->input('email');
            $countries = $request->input('countries');
            $timezone = $request->input('timezone');
            $projectId = $request->input('projectId');
            $inviteduserId = $request->input('inviteduserId');
            $project_invite_id = $request->input('project_invite_id');//echo $project_invite_id;die;

            $ary = array(
                'name' => ucfirst($name),
                'password' => Hash::make($password),
                'email' => $email,
                'country_id' => !empty($countries) ? $countries : '',
                'country_timezone_id' => $timezone,
                'phone'         => $inputs['phone'],
                'created_at' => date('Y-m-d h:i:s'),
            );
            $userId = (new User())->insertGetId($ary);


            $companyId = '';
            if(empty($project_invite_id)) {

                $companyInfo = [

                    'name' => $inputs['company'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $companyId = (new Companies())->insertGetId($companyInfo);
            }

            $dataToBeSaved = array(
                'company_id' => !empty($companyId) ? $companyId : $inputs['company_id'],
                'user_id' => $userId,
                'role' => !empty($project_invite_id) ? Config::get('constants.ROLE.USER') :Config::get('constants.ROLE.SUPERADMIN') ,
                'created_at' => date('Y-m-d h:i:s'),
            );
            (new CompanyUsers())->insert($dataToBeSaved);

            (new CompanySetting())->insert([[
                'company_id' => !empty($companyId) ? $companyId : $inputs['company_id'],
                'meta_key' => \Config::get('constants.SETTING_DATE_FORMAT'),
                'meta_value' => \Config::get('constants.GENERAL_SETTING_DATE_FORMAT')[0],
                'created_at' => date('Y-m-d :h:i:s')
            ], [
                'company_id' => !empty($companyId) ? $companyId : $inputs['company_id'],
                'meta_key' => \Config::get('constants.SETTING_TIME_FORMAT'),
                'meta_value' => \Config::get('constants.GENERAL_SETTING_TIME_FORMAT')[0],
                'created_at' => date('Y-m-d :h:i:s')
            ]]);


            if (!empty($request->input('projectId'))) {

                $result = (new ProjectInvite())->ProjectExist($email, $request->input('projectId'));

                $insertResult = array(
                    'project_id' => $projectId,
                    'user_id' => $userId,
                    'is_admin' => $result->is_admin,
                    'created_at' => date('Y-m-d h:i:s'),
                );
                (new UserProject())->insertGetId($insertResult);
                if(!empty($project_invite_id) && !empty($userId)) {

                    (new ProjectInvite())->where(['project_invite_id' => $project_invite_id])->delete();
                }
                /*
                $insertDefaultBoard = (new ProjectBoards())->getBoards($projectId);
                if (!empty($insertDefaultBoard)) {
                    $insertDefaultBoard = $insertDefaultBoard->project_board_id;
                    // add default column for the new project
                    $defaultColumn = Config::get('constants.BOARD_COLUMNS');

                    foreach ($defaultColumn as $const) {
                        if (!empty($const)) {
                            $arry = array(
                                'project_board_id' => $insertDefaultBoard,
                                'column_name' => $const,
                                'created_by_user_id' => $userId,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                            $columnId = DB::table('project_board_column')->insertGetId($arry);
                            $insertResultInUserBoardColumn = array(
                                'user_id' => $userId,
                                'project_board_column_id' => $columnId,
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s'),
                            );
                            $insertProjectColumnBoard = (new UserProjectColumn())->insertGetId($insertResultInUserBoardColumn);

                        }
                    }
                }*/
                $userProjectBoards = (new ProjectBoards())
                                    ->join('project_board_column','project_board_column.project_board_id','=','project_boards.project_board_id')
                                    ->where(['project_id' => $projectId])
                                    ->get(['project_board_column.project_board_column_id']);

                if(is_object($userProjectBoards)){

                    foreach($userProjectBoards as $userProBoards){

                        $userProjectColumns[] = [

                            'user_id' => $userId,
                            'project_board_column_id' => $userProBoards->project_board_column_id,
                            'created_at' => date('Y-m-d h:i:s'),
                        ];
                    }
                    $userProjectColumnsId = (new UserProjectColumn())->insert($userProjectColumns);
                }
            }


            $emailBody = array(
                'email' => $email,
                'body' => 'Welcome to our company',

                'subject' => 'User Registaion',
                'from'  => 'Codelee',
                'from_email'    => $email,
            );
            $this->sendEmail($emailBody);
            $body = array(
                'body' => 'Hi ' . ucwords($name) . ',<br>Welcome user. You are registered successfully.Please <a href="' . route('login') . '">Click Here</a> to login <br>Thanks, Admin',
            );

            /* Mail::send('email.email', $body, function ($send) use ($email) {
                // $send->from(\Config::get('constants.DEFAULT_EMAIL_STATUS.EMAIL'), \Config::get('constants.DEFAULT_EMAIL_STATUS.NAME'));
                $res = $send->to($email)->subject('Registration email.');
            });*/
            //DB::commit();
            if(!empty($inputs['company_id'])){
                $request->session()->flash('success', 'User successfully register');
            }else {
                $request->session()->flash('success', 'Company and user register successfully');
            }
            return redirect('login');

        } catch (\Exception $e) {
            //DB::rollBack();
            //pr($e->getMessage());
            $request->session()->flash('error', $e->getMessage());
            return redirect('getRegister');
        }

    }
    public function sendEmail($email)
    {
        \Mail::send('email.email', $email, function ($send) use ($email) {
            $send->from($email['from_email'], $email['from']);
            $res = $send->to($email['email'])->subject($email['subject']);
        });
    }
    public function getLogin(Request $request,$projectId=null,$invitedByUser=null)
    {
        $token = "";
        $inputs = $request->all();
        $companyInfo = '';
        $company = '';
        if (!empty($inputs) && isset($inputs['unique_token'])) {
            $token = $inputs['unique_token'];
            //echo $token;die;
            $data['projectInviteResult'] = (new ProjectInvite())->UniqueResult($token);
            //pr($data['projectInviteResult']->toArray());die;
            if(!empty($data['projectInviteResult']->project_id)) {
                $companyInfo = (new Project())->where(['project_id' => $data['projectInviteResult']->project_id])->first(['company_id']);
                $companyInfo = $companyInfo->company_id;
                $company = (new Companies())->where(['company_id' => $companyInfo])->first(['name']);
                $company = $company->name;
            }

            $data['parameter'] = array(

                'projectId'     => $projectId,
                'invitedByUser' => $invitedByUser,
                'unique_token'  => $token,
                'company_name'  => $company,
                'company_id'    => $companyInfo,
                'role'          => $data['projectInviteResult']->is_admin,
                'email'         => $data['projectInviteResult']->email,
            );
        }
        return view('users.login',compact('data'));
    }

    public function postLogin(UserLogin $request)
    {

        $requestUrl = \Request::all();
        $inputs = $request->all();
        $isUserValid = $this->isValidUser($request->input('email'), $request->input('password'));

        if ($isUserValid) {

            $usersCountryTimezone = (new \App\User())->where(['id' => Auth::user()->id])->first(['country_timezone_id']);
            $request->session()->set('country_timezone_id', $usersCountryTimezone->country_timezone_id);

            if(!empty($inputs['unique_token']) && !empty($inputs['project_id']) && !empty($inputs['company_id'])){
                /****   User Register With new company  ****/
                try {

                    $userId = (new \App\User())->where(['email' => $inputs['email']])->first(['id','user_type']);
                    (new ProjectInvite())->where(['unique_token' => $inputs['unique_token'], 'project_id' => $inputs['project_id']])->delete();
                    //pr($userId);die;
                    $companyUser = [

                        'company_id' => $inputs['company_id'],
                        'user_id' => $userId->id,
                        'role' => Config::get('constants.ROLE.USER'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    $companyUserId = (new CompanyUser())->insertGetId($companyUser);
                    if (!empty($companyUserId)) {

                        $userProject = [
                            'user_id' => $userId->id,
                            'project_id' => $inputs['project_id'],
                            'is_admin' => $inputs['role'],
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        $userProjectId = (new UserProject())->insertGetId($userProject);


                        $userProjectBoards = (new ProjectBoards())->getProjectBoard($inputs['project_id'],$inputs['company_id']);

                        if(is_object($userProjectBoards)){

                            foreach($userProjectBoards as $userProBoards){

                                $userProjectColumns[] = [

                                    'user_id' => $userId->id,
                                    'project_board_column_id' => $userProBoards->project_board_column_id,
                                    'created_at' => date('Y-m-d h:i:s'),
                                ];
                            }
                            $userProjectColumnsId = (new UserProjectColumn())->insert($userProjectColumns);
                        }
                        $request->session()->flash('success', 'you are successfully register with new company');
                        return redirect()->intended('companies');
                    }


                }

                catch (Exception $e){

                    return redirect()->back()->withInput()->withErrors(['Sorry there are something wrong please try again']);
                }

            }else{

                $users = (new \App\User())->where(['id' => Auth::user()->id])->first(['user_type']);

                if($users->user_type == 1){

                    return redirect()->intended('companies-list');
                }else if (!empty($requestUrl['url'])) {

                    return redirect($requestUrl['url']);
                }else{

                    return redirect()->intended('companies');
                }
            }
        }


        return redirect()->back()->withInput()->withErrors(['Invalid email or password.']);
    }

    protected function isValidUser($email, $password)
    {

        return Auth::attempt(['email' => $email, 'password' => $password]);
    }


    public function settings(Request $request){

        $userId = Auth::user()->id;
        $inputs = $request->all();
        $sesCompanyId = $request->session()->get('company_id');
        if (isset($inputs['action']) && $inputs['action'] == 'company-date-time-formate' ) {

            $dateFormate = isset($inputs['date']) ? $inputs['date'] :'';
            $timeFormate = isset($inputs['time']) ? $inputs['time'] :'';
            (new CompanySetting())->insert([
                'company_id' => $sesCompanyId,
                'meta_key' => \Config::get('constants.SETTING_DATE_FORMAT'),
                'meta_value' => $dateFormate,
                'created_at' => date('Y-m-d :h:i:s')
            ]);
            (new CompanySetting())->insert([
                'company_id' => $sesCompanyId,
                'meta_key' => \Config::get('constants.SETTING_TIME_FORMAT'),
                'meta_value' => $timeFormate,
                'created_at' => date('Y-m-d :h:i:s')
            ]);

            $dateTimes = (new CompanySetting())->take(2)->where('company_id', $sesCompanyId)
                ->orderBy('company_setting_id', 'desc')->get(['meta_value'])->toArray();
            $metaValueDate = isset($dateTimes[1]['meta_value']) ? $dateTimes[1]['meta_value'] : '';
            $metaValueTime = isset($dateTimes[0]['meta_value']) ? $dateTimes[0]['meta_value'] : '';
            $request->session()->set('company_setting_date', $metaValueDate);
            $request->session()->set('company_setting_time', $metaValueTime);

            return ['success' => true];
        }


        if($request->ajax()){
            try{

                $inputs = $request->all();
                $company_id = $inputs['company_id'];
                $project_id = $inputs['project_id'];

                $check = (new UserSettings())->where(['company_id' => $company_id,'user_id' => $userId,'project_id' => $project_id])
                                ->first(['user_seeting_id']);
                //pr($check);die;
                if(empty($check)){

                    $sett = [

                        'company_id'    => $inputs['company_id'],
                        'user_id'       => $userId,
                        'project_id'    => $inputs['project_id'],
                        'report'        => $inputs['report'],
                        'email'         => $inputs['email'],
                        'created_at'          => date('Y-m-d'),
                    ];
                    $insId = (new UserSettings())->insertGetId($sett);
                    if(!empty($insId)){
                        return '100';
                    }

                }else{

                    $sett = [

                        'company_id'    => $inputs['company_id'],
                        'user_id'       => $userId,
                        'project_id'    => $inputs['project_id'],
                        'report'        => $inputs['report'],
                        'email'         => $inputs['email'],
                        'created_at'          => date('Y-m-d'),
                    ];

                    (new UserSettings())->where(['user_seeting_id' => $check->user_seeting_id])->update($sett);
                    return '100';
                }
            } catch (Exception $e){
                pr($e->getMessage());
            }
        }else {

            $dateTimes = (new CompanySetting())->take(2)
                ->where('company_id', $sesCompanyId)
                ->orderBy('company_setting_id', 'desc')
                ->get(['meta_value'])->toArray();

            $companyId = $request->session()->get('company_id');
            $projects = (new Project())
                ->leftjoin('user_settings',function($join){
                    $join->on('user_settings.project_id','=','projects.project_id');
                    $join->on('user_settings.company_id','=','projects.company_id');
                })
                ->where(['projects.company_id' => $companyId,'user_settings.user_id' => $userId])->get();
            //pr($projects->toArray());die;

            return view('settings.user-settings', compact('projects','dateTimes'));
        }


    }


    public function updateUtcTime()
    {
        $result = DB::select("Select start_time,end_time,project_task_logged_time_id from project_task_logged_time");
        foreach ($result as $resultData) {

            $startTime = $resultData->start_time;
            $endTime = $resultData->end_time;
            $pastDate= strtotime('31-08-2017');
            if ((date('Y-m-d', $pastDate) > date('Y-m-d', strtotime($startTime))) && (date('Y-m-d',$pastDate) > date('Y-m-d', strtotime($endTime)))) {
            //if ((date('Y-m-d', '1504224000') > date('Y-m-d', strtotime($startTime))) && (date('Y-m-d', '1504051200') > date('Y-m-d', strtotime($endTime)))) {

                $utcStartTimeStamp = convertintoUTC($startTime, 3);
                $utcEndTimeStamp = convertintoUTC($endTime, 3);

                DB::SELECT("update `project_task_logged_time` SET `start_time` = '" . $utcStartTimeStamp . "',`end_time` ='" . $utcEndTimeStamp . "' where `project_task_logged_time_id` =" . $resultData->project_task_logged_time_id);
                echo 'done';
                echo '<br>';
            } else {
                echo 'no need to update';
            }
        }
    }
}
