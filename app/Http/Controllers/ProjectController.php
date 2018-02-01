<?php

namespace App\Http\Controllers;

use App\MessageToBeSend;
use App\Companies;
use App\CompanyUser;
use App\CompanyUsers;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\ProjectBoards;
use App\ProjectInvite;
use App\ProjectTaskLoggedTime;
use App\User;
use App\UserProject;
use App\UserProjectColumn;
use App\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Hash;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $projectId = isset($input['projectId']) ? $input['projectId'] : '';
        $companyId = $request->session()->get('company_id');
        $userId = \Auth::user()->id;
        $role = $request->session()->get('user_role');
        $data = (new ProjectInvite())->fetchProjects($userId, $projectId, $role,$companyId);//pr($data);die;

        $assignedProject = (new UserProject())->projectAssigned($userId, $projectId, $role,$companyId);//pr($assignedProject);die;
        //pr($assignedProject->toArray());die;

        //$assignedProject = (new UserProject())->projectAssigned($userId, $projectId, $role);//pr($assignedProject);die;
        //pr($assignedProject->toArray());die;
        $roleSession = $role;
        //pr($assignedProject->toArray());die;
        return view('project.index', compact('data', 'assignedProject', 'projectId','roleSession','role'));
    }

    public function projectDetails(Request $request, $projectId)
    {
        if ($projectId) {
            try {
                //  $projectObject = new Project();
                $userId = \Auth::user()->id;
                // $data=$projectObject->fetchAllUsersOfTheProject($projectId);

                $projectObject = new ProjectInvite();
                $userId = \Auth::user()->id;
                $data = $projectObject->fetchProjects($userId);
                $companyUserObject = new CompanyUser();
                $fetchRole = $companyUserObject->fetchResultAccordingToCompany($userId);
                $fetchRole = json_decode($fetchRole, true);
                $userRole = $fetchRole['role'];

                return view('project.index', compact('data', 'userRole'));
            } catch (Exception $e) {
                $request->session()->flash('error', 'No record found');
            }
        } else {
            $request->session()->flash('error', 'No record found');
            return redirect()->back();
        }

    }

    public function create()
    {
        try {
            return view('project.create');
        } catch (Exception $e) {
            print_r($e->getMessage());

        }
    }

    public function store(ProjectRequest $request)
    {
        try {
            //\DB::beginTransaction();
            $userObject = new User();
            $projectInviteObject = new ProjectInvite();
            $projectObject = new Project();
            $userProjectObject = new UserProject();
            $companyUserObject = new CompanyUser();

            $adminEmail = $request->input('adminEmail');
            $adminEmail = preg_replace('/\s*,\s*/', ',', $adminEmail);
            $explodeAdminEmail = explode(',', $adminEmail);
            $userEmail = $request->input('UserEmail');
            $userEmail = preg_replace('/\s*,\s*/', ',', $userEmail);
            $explodeUserEmail = explode(',', $userEmail);
            $explode = array_merge($explodeAdminEmail, $explodeUserEmail);
            $role = $request->session()->get('user_role');

            $userId = \Auth::user()->id;
            $companyId = $request->session()->get('company_id');

            $mainAdmins = (new CompanyUsers())->AssignProjectToMainAdmin($companyId,$role);

            $CompanyUser = (new CompanyUser())->where(['company_id'=>$companyId])->get(['user_id']);
            //pr($CompanyUser);die;
            $adminSuperadmin = array();
            foreach ($mainAdmins as $assignAdmin) {
                $adminSuperadmin[] = $assignAdmin['email'];
            }
            //pr($adminSuperadmin);die;
            $mergeResult1 = array_merge($explode, $adminSuperadmin);
            $mergeResult = array_unique($mergeResult1);
            $projectName = $request->input('name');
            // Project Name && new project created
            $insertResult = array(
                'name' => $projectName,
                'created_user_id' => $userId,
                'company_id' => $companyId,
                'start_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            );
            $lastInsertedId = $projectObject->insertGetId($insertResult);

            // Create-User Settings for new created project -- Mits
            if(!empty($lastInsertedId)){

                $userEmail = (new User())->where(['id' => $userId])->first(['email']);
                $userSettings = [

                    'company_id'    => $companyId,
                    'user_id'       => $userId,
                    'project_id'    => $lastInsertedId,
                    'report'        => '1',
                    'email'         => $userEmail->email,
                    'created_at'          => date('Y-m-d'),
                ];

                (new UserSettings())->insertGetId($userSettings);
            }
            // add default board for the new project
            $insertResultInBoard = array(
                'project_board_name' => Config::get('constants.BOARDNAME.MAIN_BOARD_NAME'),
                'board_name' => Config::get('constants.BOARDNAME.MAIN_BOARD_NAME'),
                'description' => Config::get('constants.BOARDNAME.MAIN_BOARD_DESC'),
                'project_id' => $lastInsertedId,
                'created_by_user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            );
            $insertDefaultBoard = (new ProjectBoards())->insertGetId($insertResultInBoard);

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
                    foreach($CompanyUser as $cuser) {
                        $insertResultInUserBoardColumn = array(
                            'user_id' => $cuser->user_id,
                            'project_board_column_id' => $columnId,
                            'created_at' => date('Y-m-d h:i:s'),
                            'updated_at' => date('Y-m-d h:i:s'),
                        );
                        $insertProjectColumnBoard = (new UserProjectColumn())->insertGetId($insertResultInUserBoardColumn);
                    }
                }
            }

            $companyInfo = (new Companies())->companyUsersData($companyId);

            foreach ($mergeResult as $email) {


                if (in_array($email, $explodeAdminEmail) == true) {
                    $isAdmin = 1;
                    //echo $isAdmin;die;
                }else if (in_array($email, $adminSuperadmin) == true) {

                    $isAdmin = 1;
                } else {
                    $isAdmin = 0;
                }
                $data1 = $userObject->UserAssociatedWithCompany(trim($email),$companyId);
                if(!empty($data1)) {
                    $data = json_decode($data1, true);

                    if (!empty($data) && isset($data['id'])) {


                            $save = array(
                                    'project_id' => $lastInsertedId,
                                    'user_id' => $data['id'],
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'updated_at' => date('Y-m-d h:i:s'),
                                    'is_admin' => $isAdmin
                            );

                            $insertRecord = $userProjectObject->insertGetId($save);

                        if($data['email'] != \Auth::user()->email) {
                            $url = route('login');
                            $emailBody = array(
                                'message_type' => 2,
                                'email' => $data['email'],
                                'message_body' => 'Please login to see Project detail <a href="' . $url . '">Login Here</a>',

                                'subject' => 'Project invite',
                                'from_company' => $companyInfo->name,
                                'from_email' => $companyInfo->email,
                            );
                            (new MessageToBeSend())->insert($emailBody);
                        }
                    } else {
                        if (!empty($email)) {

                            $token = createToken(date('now'));
                            $saving = array(
                                    'project_id' => $lastInsertedId,
                                    'invited_by_user_id' => $userId,
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'updated_at' => date('Y-m-d h:i:s'),
                                    'email' => $email,
                                    'status' => 0,
                                    'is_admin' => $isAdmin,
                                    'unique_token' => $token
                            );
                            $insertRecord1 = $projectInviteObject->insertGetId($saving);

                            $projectId = $lastInsertedId;
                            $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;

                            $emailBody = array(
                                'message_type' => 2,
                                'email' => $email,
                                'message_body' => 'Hi Click below link to register and accept invitation <a href="' . $url . '">Register Here</a><br/><br/>
                            Thanks<br/>Admin<br/>',

                                'subject' => 'Project invite',
                                'from_company' => $companyInfo->name,
                                'from_email' => $companyInfo->email,
                            );
                            (new MessageToBeSend())->insert($emailBody);
                        }
                    }
                }else{


                    $useAsso = $userObject->UserExists(trim($email));
                    $userExe = json_decode($useAsso, true);

                    if(!empty($userExe)){
                        /*  user already in software  */

                        if($email != \Auth::user()->email) {
                            $token = createToken(date('now'));
                            $saving = array(
                                    'project_id' => $lastInsertedId,
                                    'invited_by_user_id' => $userId,
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'updated_at' => date('Y-m-d h:i:s'),
                                    'email' => $email,
                                    'status' => 0,
                                    'is_admin' => $isAdmin,
                                    'unique_token' => $token
                            );
                            $insertRecord1 = $projectInviteObject->insertGetId($saving);

                            $projectId = $lastInsertedId;
                            $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;
                            (new User())->where(['id' => $userExe['id']])->update(['remember_token' => $token]);


                            $emailBody = array(
                                'message_type' => 2,
                                'email' => $email,
                                'message_body' => 'You are Associated with new company please click here and login <a href="' . $url . '">Login Here</a><br/><br/>
                            Thanks<br/>Admin<br/>',

                                'subject' => 'Project invite',
                                'from_company' => $companyInfo->name,
                                'from_email' => $companyInfo->email,
                            );
                            (new MessageToBeSend())->insert($emailBody);
                        }

                    }else{
                        /*  new user requested  */
                        if (!empty($email)) {

                            if($email != \Auth::user()->email) {
                                $token = createToken(date('now'));
                                $saving = array(
                                        'project_id' => $lastInsertedId,
                                        'invited_by_user_id' => $userId,
                                        'created_at' => date('Y-m-d h:i:s'),
                                        'updated_at' => date('Y-m-d h:i:s'),
                                        'email' => $email,
                                        'status' => 0,
                                        'is_admin' => $isAdmin,
                                        'unique_token' => $token
                                );
                                $insertRecord1 = $projectInviteObject->insertGetId($saving);

                                $projectId = $lastInsertedId;
                                $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;

                                $emailBody = array(
                                        'message_type' => 2,
                                        'email' => $email,
                                        'message_body' => 'Hi Click below link to register and accept invitation <a href="' . $url . '">Register Here</a><br/><br/>
                            Thanks<br/>Admin<br/>',

                                        'subject' => 'Project invite',
                                        'from_company' => $companyInfo->name,
                                        'from_email' => $companyInfo->email,
                                );
                                (new MessageToBeSend())->insert($emailBody);
                            }
                        }
                    }
                }

            }
            $request->session()->flash('success', 'Project has been created successfully');
            return redirect()->route('board-detail', $insertDefaultBoard);
            // }
            //\DB::commit();
        }catch (Exception $e) {
            //\DB::rollback();
        }
    }


    // function to send email
    public function sendEmail($email)
    {
        Mail::send('email.email', $email, function ($send) use ($email) {
            $send->from($email['from_email'], $email['from']);
            $res = $send->to($email['email'])->subject($email['subject']);
        });
    }

    public function edit(Request $request, $id)
    {
        $projectObject = new Project();
        $userId = \Auth::user()->id;

        $fetchResult = $projectObject->fetchResult($id, $userId);
        $fetchResult = json_decode($fetchResult, true);

        if (isset($fetchResult['project_id'])) {
            $id = $fetchResult['project_id'];
            return view('project.edit', compact('fetchResult', 'id'));
        } else {
            $request->session()->flash('error', 'Access Denied');
            return redirect()->route('projects.index');
        }

    }

    public function update(Request $request, $id)
    {
        if ($request->input('name')) {
            $updateArray = array(
                'updated_user_id' => authUser()->id,
                'name' => $request->input('name'),
            );
            $update = (new Project())->updateRecord($updateArray, $id);
            if ($update) {
                $request->session()->flash('success', 'Project name has been updated');
                return redirect()->route('dashboard');
            } else {
                $request->session()->flash('error', 'Something went wrong.Please try again');
                return redirect()->route('projects.index');
            }
        } else {
            $request->session()->flash('error', 'Something went wrong.Please try again');
            return redirect()->route('projects.index');
        }


    }

    public function inviteduser(ProjectRequest $request)
    {

        if ($request->ajax()) {
            $userObject = new User();
            $projectInviteObject = new ProjectInvite();
            $projectObject = new Project();
            $userProjectObject = new UserProject();
            $companyUserObject = new CompanyUser();

            $adminEmail = $request->input('adminEmail');
            $page = $request->input('page');

            $explodeAdminEmail = explode(',', $adminEmail);
            $userEmail = $request->input('UserEmail');
            $explodeUserEmail = explode(',', $userEmail);
            $explode = array_merge($explodeAdminEmail, $explodeUserEmail);

            $userId = \Auth::user()->id;
            $company_id = $request->session()->get('company_id');

            $inputs = $request->all();
            $projectId = $inputs['projectId'];
            $resultData = $projectObject->fetchResult($projectId, $userId);
            $projectName = isset($resultData->name) ? $resultData->name : "";


            $fetchCompanyDetail = $company_id;

            foreach ($explode as $email) {
                if ($adminEmail) {
                    if (in_array($email, $explodeAdminEmail)) {
                        $isAdmin = 1;
                    } else {
                        $isAdmin = 0;
                    }
                } else {
                    $isAdmin = 0;
                }
                $data = $userObject->UserExists(trim($email));
                $data = json_decode($data, true);

                if (!empty($data) && isset($data['id'])) {
                    $userEx = (new UserProject())->where(['user_id' => $data['id'], 'project_id' => $projectId])
                        ->first(['user_id']);
                    if (empty($userEx)) {
                        $save = array(
                            'project_id' => $projectId,
                            'user_id' => $data['id'],
                            'created_at' => date('Y-m-d h:i:s'),
                            'updated_at' => date('Y-m-d h:i:s'),
                            'is_admin' => $isAdmin
                        );
                        $insertRecord = $userProjectObject->insertGetId($save);

                        $url = route('login');

                        $emailBody = array(
                            'message_type' => 2,
                            'email' => $data['email'],
                            'message_body' => 'Please login to see Project detail <a href="' . $url . '">Login Here</a>',

                            'subject' => 'Project invite',

                        );
                        (new MessageToBeSend())->insert($emailBody);

                    } else {
                        /*  if user already in database with same project */
                        echo '1';
                        exit;
                    }
                } else {

                    if (!empty($email)) {

                        $token = createToken(date('now'));
                        $save = array(
                            'project_id' => $projectId,
                            'invited_by_user_id' => $userId,
                            'created_at' => date('Y-m-d h:i:s'),
                            'updated_at' => date('Y-m-d h:i:s'),
                            'email' => $email,
                            'status' => 0,
                            'is_admin' => $isAdmin,
                            'unique_token' => $token
                        );
                        $insertRecord = $projectInviteObject->insertGetId($save);
                        $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;

                        $emailBody = array(
                            'message_type' => 2,
                            'email' => $email,
                            'message_body' => 'Hi click below link to register and accept invitation <a href="' . $url . '" ></a>',

                            'subject' => 'Project invite',

                        );
                        (new MessageToBeSend())->insert($emailBody);


                    }
                }
                if (!empty($page)) {

                    if (!empty($insertRecord)) {

                        if (!empty($data['id'])) {

                            $users = (new UserProject())->getNewProjectUSer($insertRecord);
                            if (!empty($users)) {
                                return view('project.users-add-boardInPopup', compact('users'));
                            } else {

                                echo '0';
                            }
                        } else {
                            /*  user Invited via email  */
                            echo 2;
                        }
                    }
                } else {
                    if (!empty($insertRecord)) {

                        $request->session()->flash('success', 'Record created successfully');
                        return redirect()->route('projects.index');
                    }
                }
            }
        }
    }


    public function sendProjectInviteEmail(Request $request,$invitedByUser,$projectId )
    { //echo '<>'.$projectId.'='.$invitedByUser;die;
        $inputs = $request->all();

        $projectInviteResult = (new ProjectInvite())->UniqueResult($inputs['unique_token']);
        $userEmail = (new ProjectInvite())->where(['unique_token' => $inputs['unique_token'],'project_id' => $projectId])->first(['email']);
        if(!empty($userEmail)){
            $userExist = (new User())->where(['email' => $userEmail->email])->first(['email']);
            if(!empty($userExist)){

                return redirect('login/'.$projectId.'/'.$invitedByUser.'?unique_token='.$inputs['unique_token'].'&projectId='.$projectId);
            }else{

                return redirect('userregister/'.$projectId.'/'.$invitedByUser.'?unique_token='.$inputs['unique_token'].'&projectId='.$projectId);
            }
        }else{

            return redirect('userregister/'.$projectId.'/'.$invitedByUser.'?unique_token='.$inputs['unique_token'].'&projectId='.$projectId);
        }

    }


    public function projectAssigned()
    {
        $userId = \Auth::user()->id;
        $fetchResult = (new UserProject())->projectAssigned($userId);
        return view('project.project_assigned', compact('fetchResult'));
    }


    public function ajaxDeleteUserInvite(Request $request)
    {
        if ($request->ajax()) {
            try {
                $inputs = $request->all();
                $delete = (new ProjectInvite())->where(['project_invite_id' => $inputs['id']])->delete();
                if ($delete) {
                    echo '100';
                } else {
                    echo '0';
                }
            } catch (\Exception $e) {

            }
        }


    }

    public function ajaxDeleteUserProject(Request $request)
    {
        if ($request->ajax()) {
            try {
                $inputs = $request->all();
                if(!empty($inputs)){
                    $userProjectId = $inputs['id'];
                    $userID = (new UserProject())->where(['user_project_id' => $userProjectId])->first(['user_id']);
                    $userId = $userID->user_id;
                    $taskId = (new ProjectTaskLoggedTime())
                        ->where(['logged_by_user_id' => $userId,'end_time' => NULL])
                        ->first(['project_task_id']);
                    $tasksTime = (array)$taskId;
                    if(!empty($tasksTime)){
                        $tskInputs = [
                            'check' => 'false',
                            'new_project_task_id' => '0',
                            'project_task_id'       => $taskId->project_task_id,
                            'value' => 'puse',
                        ];
                        $result = (new ProjectBoardsController())->ajaxEndTaskLoggin($request,$tskInputs,$userId);

                    }
                }
                $delete = (new UserProject())->where(['user_project_id' => $inputs['id']])->delete();
                if ($delete) {
                    echo '100';
                } else {
                    echo '0';
                }

            } catch (\Exception $e) {

            }
        }

    }

    public function changeUserRole(Request $request){

        $companyId = $request->session()->get('company_id');
        $companyInfo = (new Companies())->companyUsersData($companyId);
        $inputs = $request->all();
        $userEmail = (new \App\User())->where(['id' => $inputs['user_id']])->first(['email']);
        $update = (new UserProject())->where(['user_id' => $inputs['user_id'], 'project_id' => $inputs['project_id']])
            ->update(['is_admin' => $inputs['role']]);
        $url = route('login');
        if($update){
            $finalData [] = [
                'message_type' => 2,
                'subject' => 'Change user role',
                'message_body' => 'User role has been changed <a href="' . $url . '">Login Here</a>',
                'email' => $userEmail->email,
                'from_email'    => $companyInfo->email,
                'from_company'  => $companyInfo->name,
                'created_at' => date('Y-m-d :h:i:s')

            ];

        (new MessageToBeSend())->insert($finalData);
        }
        $userRole = (new UserProject())->where(['user_id' => $inputs['user_id'], 'project_id' => $inputs['project_id']])
            ->first(['is_admin']);

        return ['success' => true, 'role' => $userRole->is_admin];

    }
    public function addUserInProject(ProjectRequest $request)
    {


        if ($request->ajax()) {
            try {
                $userObject = new User();
                $projectInviteObject = new ProjectInvite();
                $projectObject = new Project();
                $userProjectObject = new UserProject();
                $companyUserObject = new CompanyUser();
                $userEmail = $request->input('email');
                $userEmail = preg_replace('/\s*,\s*/', ',', $userEmail);
                $explodeUserEmail = explode(',', $userEmail);
                $explode = $explodeUserEmail;
                $userId = \Auth::user()->id;
                $userName = \Auth::user()->name;
                $company_id = $request->session()->get('company_id');

                $companyInfo = (new Companies())->companyUsersData($company_id);
                $inputs = $request->all();
                if($inputs['role'] == 0){
                    $role = '0';
                }
                else{
                    $role = '1';
                }
                $projectId = $inputs['projectId'];
                $resultData = $projectObject->fetchResult($projectId, $userId);
                $projectName = isset($resultData->name) ? $resultData->name : "";


                $fetchCompanyDetail = $company_id;


                foreach ($explode as $ind =>  $email) {

                    if ($role == '1') {
                        $isAdmin = 1;
                    } else {
                        $isAdmin = 0;
                    }

                    $data = $userObject->UserAssociatedWithCompany(trim($email),$company_id);
                    $data = json_decode($data, true);

                    if (!empty($data) && isset($data['id'])) {

                        $userEx = (new UserProject())->where(['user_id' => $data['id'], 'project_id' => $projectId])
                            ->first(['user_id', 'is_admin']);

                      /*  $companyId = $companyUserObject->where(['user_id' => $data['id'],'company_id' => $company_id])->first(['company_id']);

                        if(empty($companyId) && empty((array) $companyId)){

                            $userCompany = [
                                'company_id' => $company_id,
                                'user_id' => $data['id'],
                                'role'      => \Config::get('constants.ROLE.USER'),
                                'created_at'    => date('Y-m-d H:i:s'),
                            ];
                            $companyUserObject->insert($userCompany);
                        }*/
                        if (empty($userEx)) {
                            $save = array(
                                'project_id' => $projectId,
                                'user_id' => $data['id'],
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s'),
                                'is_admin' => $isAdmin
                            );
                            $insertRecord = $userProjectObject->insertGetId($save);
                            if ($insertRecord) {
                                $this->insertInUserColumn($projectId, $data['id']);
                            }
                            $url = route('login');


                            $emailBody = array(
                                'message_type' => 2,
                                'email' => $data['email'],
                                'message_body' => 'Please login to see Project detail <a href="' . $url . '">Login Here</a>',

                                'subject' => 'Project invite',
                                'from_company' => $companyInfo->name,
                                'from_email' => $companyInfo->email,
                            );
                            (new MessageToBeSend())->insert($emailBody);


                            //$mail = explode(', ',$inputs['email']);
                            if ($insertRecord) {
                                $ary[] = array(
                                        'success' => '100',
                                        'message' => 'Project board name updated successfully done!',
                                        'result' => $email,
                                        'role' => $role,
                                        'id' => $insertRecord,
                                        'user' => $userName,
                                        'invitation' => 'No pending invitation',
                                        'type'      => 'accepted',
                                );
                            } else {
                                $ary[] = array(
                                    'success' => '0',
                                );
                            }
                        } else {

                            if ($userEx['is_admin'] != $role) {
                                $update = $userProjectObject->where(['user_id' => $userEx['user_id'], 'project_id' => $projectId])
                                    ->update(['is_admin' => $role]);
                                if ($update) {
                                    $ary[] = array(
                                        'success' => '2',
                                    ); // updating status
                                    $url = route('login');


                                    $emailBody = array(
                                        'message_type' => 2,
                                        'email' => $data['email'],
                                        'message_body' => 'User role has been changed <a href="' . $url . '">Login Here</a>',

                                        'subject' => 'Project invite',
                                        'from_company' => $companyInfo->name,
                                        'from_email' => $companyInfo->email,
                                    );
                                    (new MessageToBeSend())->insert($emailBody);
                                } else {
                                    $ary[] = array(
                                        'success' => '0',
                                    );
                                }
                            } else {
                                $ary[] = array(
                                    'success' => '3',
                                );
                            }
                            /*  if user already in database with same project */
                            $this->insertInUserColumn($projectId, $data['id']);

                            // exit;
                        }
                    } else {


                        $useAsso = $userObject->UserExists(trim($email));
                        $userExe = json_decode($useAsso, true);

                        if(!empty($userExe)){
                            /*  user already in software  */

                            $token = createToken(date('now'));
                            $saving = array(
                                'project_id' => $projectId,
                                'invited_by_user_id' => $userId,
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s'),
                                'email' => $email,
                                'status' => 0,
                                'is_admin' => $isAdmin,
                                'unique_token' => $token
                            );
                            $insertRecord1 = $projectInviteObject->insertGetId($saving);

                            $projectId = $projectId;
                            $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;
                            (new User())->where(['id' => $userExe['id']])->update(['remember_token' => $token]);


                            $emailBody = array(
                                'message_type' => 2,
                                'email' => $email,
                                'message_body' => 'You are Associated with new company please click here and login <a href="' . $url . '">Login Here</a><br/><br/>
                            Thanks<br/>Admin<br/>',

                                'subject' => 'Project invite',
                                'from_company' => $companyInfo->name,
                                'from_email' => $companyInfo->email,
                            );
                            (new MessageToBeSend())->insert($emailBody);
                            /*****  Return Result   ****/
                            if ($insertRecord1) {

                                $ary[] = [    'success' => '100',
                                    'message'   => 'New user invite successfully',
                                    'result'    => $email,
                                    'role'      => $role,
                                    'id'        => $insertRecord1,
                                    'user'      => $userName,
                                    'invitation'=> 'pending invitation',
                                    'type'      => 'pending',
                                ];
                            }

                            else {
                                $ary[] = array(
                                    'success' => '0',
                                );
                            }
                            /****  End   ****/
                        }else{
                            /*  new user requested  */
                            if (!empty($email)) {

                                $token = createToken(date('now'));
                                $saving = array(
                                    'project_id' => $projectId,
                                    'invited_by_user_id' => $userId,
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'updated_at' => date('Y-m-d h:i:s'),
                                    'email' => $email,
                                    'status' => 0,
                                    'is_admin' => $isAdmin,
                                    'unique_token' => $token
                                );
                                $insertRecord1 = $projectInviteObject->insertGetId($saving);

                                $projectId = $projectId;
                                $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;

                                $emailBody = array(
                                    'message_type' => 2,
                                    'email' => $email,
                                    'message_body' => 'Hi Click below link to register and accept invitation <a href="' . $url . '">Register Here</a><br/><br/>
                            Thanks<br/>Admin<br/>',

                                    'subject' => 'Project invite',
                                    'from_company' => $companyInfo->name,
                                    'from_email' => $companyInfo->email,
                                );
                                (new MessageToBeSend())->insert($emailBody);
                                if ($insertRecord1) {

                                    $ary[] = [    'success' => '100',
                                        'message' => 'new user invite successfully',
                                        'result' => $email,
                                        'role' => $role,
                                        'id' => $insertRecord1,
                                        'user' => $userName,
                                        'invitation' => 'pending invitation',
                                        'type'      => 'pending',
                                    ];
                                }

                                else {
                                    $ary[] = array(
                                        'success' => '0',
                                    );
                                }
                            }
                        }

                        /****  -----------------------  ****/

                      /*  if (!empty($email)) {

                            $token = createToken(date('now'));
                            $save = array(
                                'project_id' => $projectId,
                                'invited_by_user_id' => $userId,
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s'),
                                'email' => $email,
                                'status' => 0,
                                'is_admin' => $isAdmin,
                                'unique_token' => $token
                            );
                            $insertRecord = $projectInviteObject->insertGetId($save);

                            $url = route('sendProjectInviteEmail', [$userId, $projectId]) . '?unique_token=' . $token;

                            $emailBody = array(
                                'email' => $email,
                                'body' => 'Hi click below link to register and accept invitation <a href="' . $url . '" >click here</a><br/><br/>Thanks <br/>Admin',
                                'subject' => 'Project invite',
                            );
                            $this->sendEmail($emailBody);
                            if ($insertRecord) {

                                $ary[] = [    'success' => '100',
                                            'message' => 'Project board name updated successfully done!',
                                            'result' => $email,
                                            'role' => $role,
                                            'id' => $insertRecord,
                                            'user' => $userName,
                                            'invitation' => 'pending invitation',
                                            'type'      => 'pending',
                                ];
                            }

                            else {
                                $ary[] = array(
                                    'success' => '0',
                                );
                            }

                        }*/
                    }
                }

                return json_encode($ary);

            } catch (\Exception $e) {
                //pr($e->getMessage());
                $request->session()->flash('error', $e->getMessage());
            }
        }
    }


    function insertInUserColumn($projectId, $userId)
    {
        $projectBoards = (new ProjectBoards())->getBoardsWithColumns($projectId)->toArray();
        foreach ($projectBoards as $boards) {
            $columns = array_column($boards['board_column_names'], 'project_board_column_id');
            $exists = (new UserProjectColumn())->checkUserisAssignedForSpecificColumn($columns, $userId);

            if ($exists) {
                $projectColumnId = array_column($exists, 'user_project_column_id');
                $delete = (new UserProjectColumn())->deletePermanently($projectColumnId);
            }
            foreach ($columns as $columnId => $value) {

                $dataToBeSaved = array(
                    'user_id' => $userId,
                    'project_board_column_id' => $value,
                );
                $insertingNewRecord = (new UserProjectColumn())->insertRecord($dataToBeSaved);
            }
        }
    }

}
