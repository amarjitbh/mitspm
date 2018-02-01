<?php

namespace App\Http\Controllers;

use App\Companies;
use App\CompanyUser;
use App\CompanyUsers;
use App\Http\Requests\addBoards;
use App\Project;
use App\ProjectBoardColumn;
use App\ProjectTaskAssignees;
use App\ProjectTaskLoggedTime;
use App\ProjectTasks;
use App\User;
use App\MessageToBeSend;
use App\TemplateLog;
use App\TaskActivity;
use App\UserProject;
use App\UserProjectColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\ProjectBoards;
use Mockery\CountValidator\Exception;

class ProjectBoardsController extends Controller
{

    public function createBoard(Request $request,$id)
    {
        /*** Create Boards Fro Projects ***/

        $data['user_role'] = $request->session()->get('user_role');
        $data['projectId'] = $id;
        $data['boardColumns'] = \Config::get('constants.BOARD_COLUMNS');
        return view('boards.create-boards', $data);
    }

    public function updateBoardTitle(Request $request){

        try{
            $inputs =  $request->all();

            $updateBoard = (new ProjectBoards())
                ->where('project_board_id', $inputs['board_id'])
                ->update(['project_board_name' => $inputs['board_name']]);
            if($updateBoard){
                return['success' => true, 'message' => 'User has been invited successfully!', 'result' => $inputs['board_name'], 'id' => $inputs['board_id']];
            }
            else{
                return redirect()->back()->withErrors('Oops! something went wrong.');
            }

        } catch (\Exception $e) {


        }
    }

    public function addBoard(addBoards $request)
    {
        try {
            /*** ADD - Project-Boards  ***/
            //$constant = \Config::get('constants.BOARD_COLUMNS');

            $userId = Auth::user()->id;
            $project_id = $request->input('project_id');
            $boardName = $request->input('project_board_name');
            $description = $request->input('description');
            $sesCompanyId = $request->session()->get('company_id');
            $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
            $admin = \Config::get('constants.ROLE.ADMIN');
            $colorCode = \Config::get('constants.COLUMNS_COLOR_CODE');
            $singleColumnColorCode = \Config::get('constants.SINGLE_COLUMN_COLOR_CODE');

            $impColumns = $request->input('columns');
            //pr($columns,1);
            /*$impColumns = explode(',',$columns);
            pr($impColumns,1);*/
            if (empty($impColumns) or !isset($impColumns)) {

                $ary = array(
                    'success' => 1,
                );
                echo json_encode($ary);
                exit;
            }
            $ary = array(

                'project_id' => $project_id,
                'project_board_name' => $boardName,
                'board_name' => $boardName,
                'description' => $description,
                'created_by_user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s'),
            );
            $projectBoards = new projectBoards();
            $boardID = DB::table('project_boards')->insertGetId($ary);
            if (!empty($impColumns)) {
                foreach ($impColumns as $ind => $const) {
                    if (!empty($const)) {
                        $arry = array(

                            'project_board_id' => $boardID,
                            'column_name' => $const,
                            'column_color_code' => !empty($colorCode[$ind]) ? $colorCode[$ind] : $singleColumnColorCode,
                            'created_by_user_id' => $userId,
                            'created_at' => date('Y-m-d h:i:s'),
                        );
                        $colID = DB::table('project_board_column')->insertGetId($arry);
                        //$projectUsers = (new CompanyUser())->where(['project_id' => $project_id])->get(['user_id']);
                        $projectUsers = (new CompanyUser())
                            ->join('user_projects','user_projects.user_id','=','company_users.user_id')
                            ->where(['company_users.company_id' => $sesCompanyId,'user_projects.project_id' => $project_id])->get(['company_users.user_id as user_id','user_projects.user_id as user_id']);

                        foreach($projectUsers as $proUsers){

                            //pr($proUsers);die;
                            $insertResultInUserBoardColumn = array(
                                'user_id' => $proUsers->user_id,
                                'project_board_column_id' => $colID,
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s'),
                            );
                            $boardColumnId = (new UserProjectColumn())->insertGetId($insertResultInUserBoardColumn);
                        }
                    }
                }
            }

            if (!empty($boardID) && !empty($colID)) {

                $request->session()->flash('success', 'New board created successfully ');
                return redirect('board-detail/' . $boardID);
            } else {


                $request->session()->flash('success', 'Please try again');
                return redirect('create-board/' . $project_id);
            }
        } catch (\Exception $e) {

            // pr($e->getMessage());
        }
    }

    public function projectBoardsList($proId)
    {
        $result['proId'] = $proId;
        $result['data'] = (new ProjectBoards())->getBoardsWithColumns($proId);
        return view('boards.boards-list', $result);
    }

    public function search(Request $request){

        $inputs = \Request::all();
        $putSearch = (!empty($inputs['term']) ? trim($inputs['term']) : '');
        $searchBoardId = (!empty($inputs['search_board_id']) ? trim($inputs['search_board_id']) : '');
        $searchProjectId = (!empty($inputs['search_project_id']) ? trim($inputs['search_project_id']) : '');
        $results = array();
        $userId = Auth::user()->id;
        $sesCompanyId = $request->session()->get('company_id');
        $queries = DB::table('project_tasks')
                    ->join('projects', 'projects.project_id', '=', 'project_tasks.project_id')
                    ->join('user_projects', 'user_projects.project_id', '=', 'project_tasks.project_id')
                    ->where('project_tasks.subject', 'LIKE', '%' . $putSearch . '%')
                    ->where('projects.company_id' , $sesCompanyId)
                    ->where('user_projects.user_id' , $userId)
                    ->where(function($query) use ($searchProjectId) {

                        if ($searchProjectId != '') {
                            $query->where('project_tasks.project_id', $searchProjectId);
                        }
                    })
                    ->where(function($query) use ($searchBoardId) {

                        if ($searchBoardId != '') {
                            $query->where('project_tasks.project_board_id', $searchBoardId);
                        }
                    })
                    ->select('project_tasks.subject', 'project_tasks.project_board_id', 'project_tasks.project_id','user_projects.user_id')
                    ->groupBy('project_tasks.project_task_id')
                    ->take(10)->get();

        if(!empty($queries)) {
            foreach ($queries as $query) {

                $results[] = ['board_id' => $query->project_board_id, 'project_id' => $query->project_id, 'value' => $query->subject];
            }
        }

        if(count($results))
            return \Response::json($results);
        else
            return ['value'=>'No Matches Found','id'=>''];


    }

    /*  public function boardDetail(Request $request, $bid ,$userId = null)
   {
       try {$checkId = '';
           if(empty($userId)) {
               $userId = Auth::user()->id;
           }else{
               $checkId = $userId;
           }
           $role = $request->session()->get('user_role');//echo $role;die;
           $srole = (new CompanyUser())->where(['user_id' => $userId])->first(['role']);
           /* This role use for the getting user data */
    /*$simpleRole = $srole->role;*/
    //echo $role;die;*/

    public function boardDetail(Request $request, $bid ,$userId = null)
    {   $inputs = $request->all();

        try {
            $checkId = '';
            /* all users variable define for select all users or all tasks in board column details page  */
            $allUsers = '';
            if(empty($userId)) {
                $allUsers = 'all-users';
                $result['allUsers'] = $allUsers;
                $userId = Auth::user()->id;
            }else{
                $checkId = $userId;
            }
            $role = $request->session()->get('user_role');//echo $role;die;
            $srole = (new CompanyUser())->where(['user_id' => $userId])->first(['role']);
            /* This role use for the getting user data */
            $simpleRole = $srole->role;
            $sesCompanyId = $request->session()->get('company_id');

            $result['userId'] = $userId;
            $result['boardId'] = $bid;
            $checkBoard = (new ProjectBoards())->checkforaccess($bid);

            $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
            $admin = \Config::get('constants.ROLE.ADMIN');
            $companyId = $request->session()->get('company_id');
            $projectAdmin = \Config::get('constants.ROLE.USER');

            if (!empty($checkBoard->company_id) && $checkBoard->company_id == $sesCompanyId) {

                $project = (new ProjectBoards())->where(['project_board_id' => $bid])->first(['project_id'])->toArray();
                //$userIsAdmin = (new UserProject())->where(['project_id' => $project['project_id'],'user_id' => Auth::user()->id])->first(['is_admin'])->toArray();
                $userIsAdmin = (new UserProject())->where(['project_id' => $project['project_id'], 'user_id' => Auth::user()->id])->first(['is_admin']);
                //pr($userIsAdmin);die;
                $userIsAdminAry = (array)$userIsAdmin;
                if (!empty($userIsAdminAry)) {
                    if (is_object($userIsAdmin)) {

                        $userIsAdmin = $userIsAdmin->toArray();
                    }
                    $company = (new Project())->where(['project_id' => $project['project_id']])->first(['company_id'])->toArray();
                    if ($role == \Config::get('constants.ROLE.USER')) {
                        $result['projects'] = (new Project())->getUsersProjects($userId,$sesCompanyId);
                    } else {
                        $result['projects'] = (new Project())->getAdminsProjects($sesCompanyId);
                    }
                    if (isset($inputs['action']) && $inputs['action'] == 'board-with-task' ) {

                        $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');
                        $page = isset($inputs['page']) && $inputs['page'] > 0 ? $inputs['page'] : 0;
                        $columnId = isset($inputs['column_id']) && $inputs['column_id'] != '' ? $inputs['column_id'] : '';
                        $offset = $page * $limit;
                        $ProjectsTasks = (new ProjectBoards())->fetchUserProjectColumnTask($bid, $userId, $role, $simpleRole, $userIsAdmin['is_admin'], $checkId,$inputs['column_id'],$offset);
                        //pr($ProjectsTasks);die;
                        $result['data']  = $ProjectsTasks;
                       // pr($result['data']);die;
                        $arrayResult = 1;
                        return view('boards.board-column-task', $result, compact('arrayResult'));
                    }else{

                        $result['data'] = (new ProjectBoards())->fetchUserProjectColumn($bid, $userId, $role, $simpleRole, $userIsAdmin['is_admin'], $checkId);
                    }
                    $result['ColumnName'] = (new ProjectBoardColumn())->fetchColumnsName($bid);
                    if (!empty((array)$result['data'])) {

                        $result['boards'] = (new ProjectBoards())->where(['project_id' => $result['data']->project_id])
                            ->get()->toArray();
                    }
                    $role = $request->session()->get('user_role');
                    $result['usersList'] = '';
                    if ($role == $superAdmin) {

                        $result['usersList'] = (new UserProject())->getAdminsUserList($project['project_id']);

                    } else if ($role == $admin) {

                        $result['usersList'] = (new UserProject())->projectAdminNormalUserList($project['project_id'], $companyId);
                    } else {

                        $result['UserIsAdmin'] = $userIsAdmin['is_admin'];
                        if ($userIsAdmin['is_admin'] == '1') {

                            $result['usersList'] = (new UserProject())->projectAdminUserList($project['project_id'], $companyId);
                            $result['userSuperAdmin'] = (new UserProject())->projectAdminUserListGetSuperAdmin($project['project_id'], $companyId);
                        }
                    }
                    //pr($result['data']->toArray(),1);
                    $result['user_role'] = $role;
                    return view('boards.boards-columns', $result);
                } else {

                    $request->session()->flash('error', 'Unauthorised Access');
                    return redirect('dashboard');
                }
            } else {

                $request->session()->flash('error', 'Unauthorised Access');
                return redirect('dashboard');
            }

        } catch (\Exception $e) {
            //  dd($e->getMessage());
            $request->session()->flash('error', 'Sorry something went wrong. Please try again.');
        }
    }


    public function ajaxStartTaskLoggin(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::user()->id;
            $inputs = $request->all();
            $projectBoard = '';
            $userProjectBoard = '';
            /* if(!empty($inputs['projectBoard'])){
                 $projectBoard =  $inputs['projectBoard'];
             }if(!empty($projectBoard)) {
                 $userProjectBoard = (new ProjectTasks())->where(['project_board_id' => $projectBoard, 'project_task_id' => $inputs['project_task_id']])
                     ->get(['project_task_id']);
             }*/
            $alreadyWorkingOnTask = (new ProjectTaskLoggedTime())->recordAlreadyExist($userId);
            //pr();die;
            //pr($userProjectBoard);die;
            //pr($inputs);die;
            $projectId = (new ProjectTasks())->where(['project_task_id' => $inputs['project_task_id']])->first(['project_id','subject']);

            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.START-TASK')],
                    ['type', '=', 1],
                ])->first();

            $userName = Auth::user()->name;
            $taskTitle = $projectId->subject;
            $message = $templateLogInfo->message;
            $userTaskDes = array("#user_first_name#", "#task_name#");
            $userTaskRep = array($userName, $taskTitle);
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);



            if (!empty($inputs['alreadyAssigned']) && $inputs['alreadyAssigned'] == 1) {

                $updating = (new ProjectTaskLoggedTime())
                    ->where('project_task_logged_time_id', $alreadyWorkingOnTask->project_task_logged_time_id)
                    ->update(['end_time' => date(timerDateFormat())]);

                $saved = array(
                    'logged_by_user_id' => $userId,
                    'project_task_id' => $inputs['project_task_id'],
                    'start_time' => date(timerDateFormat()),
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                );

                $inserting = (new ProjectTaskLoggedTime())->insertGetId($saved);
                if ($inserting) {
                    $calculatedTime = findTotalTimeTaken($inputs['project_task_id']); //fxn created in helper
                    //$response['time'] = $calculatedTime['DateFormat'];
                    $response['time'] = $calculatedTime['NoOfDays'];
                    $response['response'] = 3;
                    // $response['test'] = $projectTaskId;
                    $response['message'] = 'success';

                } else {
                    //$response['test'] = $projectTaskId;
                    $response['response'] = 0;
                    $response['message'] = 'oops!! something went wrong.';

                }
            } else {
                if ($inputs['check'] == 'true') {
                    $projectTaskId = $inputs['new_project_task_id'];
                } else {
                    $projectTaskId = $inputs['project_task_id'];
                }

                if (empty($alreadyWorkingOnTask->project_task_logged_time_id)
                    && !isset($alreadyWorkingOnTask->project_task_logged_time_id)
                ) {
                    $saved = array(
                        'logged_by_user_id' => $userId,
                        'project_task_id' => $projectTaskId,
                        'start_time' => date(timerDateFormat()),
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s'),
                    );

                    $inserting = (new ProjectTaskLoggedTime())->insertGetId($saved);


                    if ($inserting) {
                        if(empty($inputs['new_project_task_id'])) {

                            (new TaskActivity())->insert([
                                'project_id' => $projectId->project_id,
                                'project_task_id' => $inputs['project_task_id'],
                                'message' => $newTaskDes,
                                'user_id' => $userId,
                                'created_at' => date('Y-m-d h:i:s')
                            ]);
                        }
                        $calculatedTime = findTotalTimeTaken($projectTaskId); //fxn created in helper
                        //$response['time'] = $calculatedTime['DateFormat'];
                        $response['time'] = $calculatedTime['NoOfDays'];
                        $response['response'] = 1;
                        // $response['test'] = $projectTaskId;
                        $response['message'] = 'success';

                    } else {
                        //$response['test'] = $projectTaskId;
                        $response['response'] = 0;
                        $response['message'] = 'oops!! something went wrong.';

                    }
                } else {
                    $response['response'] = 2;
                    // $response['test'] = $projectTaskId;
                    $response['message'] = 'You are already working on another task.You sure you want to pause earlier task?';

                }
            }
            //pr($inputs);
            return $response;
        }
    }


    public function createLogOnTask(Request $request){

        $userId = Auth::user()->id;
        $inputs = $request->all();
        $userName = Auth::user()->name;
        $userTaskDes = array("#user_first_name#", "#task_name#");

        $projectId = (new ProjectTasks())->where(['project_task_id' => $inputs['project_task_id']])->first(['project_id','subject']);
        $taskTitle = $projectId->subject;
        $userTaskRep = array($userName, $taskTitle);
        //pr($inputs);die;
        if($inputs['status'] == 'start') {

            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.START-TASK')],
                    ['type', '=', 1],
                ])->first();

            $message = $templateLogInfo->message;
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $inputs['project_task_id'],
                'message' => $newTaskDes,
                'user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }else{


            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.STOP-TASK')],
                    ['type', '=', 1],
                ])->first();

            $message = $templateLogInfo->message;
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $inputs['project_task_id'],
                'message' => $newTaskDes,
                'user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
    }

    public function ajaxProjectTaskLoggin(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::user()->id;
            $inputs = $request->all();

            $update = (new ProjectTasks())->where('project_task_id', $inputs['project_task_id'])
                ->update(['project_board_column_id' => $inputs['board_column_id']]);

            if ($update) {
                echo 'success';
            } else {
                echo 'error';
            }

        }
    }

    public function ajaxEndTaskLoggin(Request $request,$myInputs = null,$users=null)
    {

        !empty($users) ? $userId = $users : $userId = Auth::user()->id;
        $inputs = $request->all();
        if(!empty($myInputs)){
            $inputs = $myInputs;
        }else{
            $inputs = $inputs;
        }
        $alreadyWorkingOnTask = (new ProjectTaskLoggedTime())->recordAlreadyExist($userId);
        //pr($alreadyWorkingOnTask);die;
        $projectId = (new ProjectTasks())->where(['project_task_id' => $inputs['project_task_id']])->first(['project_id','subject']);


        $userName = Auth::user()->name;
        $taskTitle = $projectId->subject;
        $userTaskDes = array("#user_first_name#", "#task_name#");
        $userTaskRep = array($userName, $taskTitle);
        $projectTaskId = $inputs['new_project_task_id'];
        //pr($inputs);die;
        if ($inputs['check'] == 'true') {

            //pr($inputs,1);
            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.START-TASK')],
                    ['type', '=', 1],
                ])->first();

            $message = $templateLogInfo->message;
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $inputs['project_task_id'],
                'message' => $newTaskDes,
                'user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
            if(!empty($inputs['new_project_task_id']) && $inputs['new_project_task_id'] != 0 ){
                $projectId = (new ProjectTasks())->where(['project_task_id' => $inputs['new_project_task_id']])->first(['project_id','subject']);

                $taskTitle = $projectId->subject;
                $userTaskDes = array("#user_first_name#", "#task_name#");
                $templateLogInfo = (new TemplateLog())
                    ->where([
                        ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.STOP-TASK')],
                        ['type', '=', 1],
                    ])->first();

                $message = $templateLogInfo->message;
                $tags = $templateLogInfo->tags;
                $userTaskRep = array($userName, $taskTitle);
                $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
                (new TaskActivity())->insert([
                    'project_id' => $projectId->project_id,
                    'project_task_id' => $inputs['new_project_task_id'],
                    'message' => $newTaskDes,
                    'user_id' => $userId,
                    'created_at' => date('Y-m-d h:i:s')
                ]);

            }

        } else {

            $projectTaskId = $inputs['project_task_id'];
            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.STOP-TASK')],
                    ['type', '=', 1],
                ])->first();

            $message = $templateLogInfo->message;
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $inputs['project_task_id'],
                'message' => $newTaskDes,
                'user_id' => $userId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        $data = (new \App\ProjectTaskLoggedTime())->fetchTaskDuration($projectTaskId, $userId);
        $totalSeconds = 0;
        foreach ($data as $dateResult) {

            $previousTimeStamp = strtotime($dateResult['start_time']);
            if ($dateResult['end_time'] == null) {
                $lastTimeStamp = strtotime('now');
            } else {
                $lastTimeStamp = strtotime($dateResult['end_time']);
            }
            $totalSeconds += $lastTimeStamp - $previousTimeStamp;
        }

        $totalDuration = calculateNoOfdays($totalSeconds); //fxn created in helper.php



        if (!empty($totalDuration)) {
            $updating = DB::table('project_task_assignees')
                ->where('project_task_id', $projectTaskId)
                ->where('user_id', $userId)
                ->update(['logging_time' => $totalDuration,
                    'time_taken_in_seconds' => $totalSeconds
                ]); //updating total time taken by user


            if (!empty($alreadyWorkingOnTask->project_task_logged_time_id) && isset($alreadyWorkingOnTask->project_task_logged_time_id)) {

                $update = (new ProjectTaskLoggedTime())
                    ->where('project_task_logged_time_id', $alreadyWorkingOnTask->project_task_logged_time_id)
                    ->where('project_task_id', $projectTaskId)
                    ->where('logged_by_user_id', $userId)
                    ->update(['end_time' => date(timerDateFormat())]);

                if ($update) {
                    $calculatedTime = findTotalTimeTaken($inputs['project_task_id']); //fxn created in helper
                    $response['time'] = $calculatedTime['NoOfDays'];
                    $response['response'] = 1;
                    $response['message'] = 'success';
                } else {
                    if(!empty($alreadyWorkingOnTask->project_task_id)){

                        $taskTitle = (new ProjectTasks())->where(['project_task_id' => $alreadyWorkingOnTask->project_task_id])->first(['subject']);
                        $templateLogInfo = (new TemplateLog())
                            ->where([
                                ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.STOP-TASK')],
                                ['type', '=', 1],
                            ])->first();
                        $taskTitle = $taskTitle->subject;
                        $userTaskRep = array($userName, $taskTitle);
                        $message = $templateLogInfo->message;
                        $tags = $templateLogInfo->tags;
                        $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

                        (new TaskActivity())->insert([
                            'project_id' => $projectId->project_id,
                            'project_task_id' => $alreadyWorkingOnTask->project_task_id,
                            'message' => $newTaskDes,
                            'user_id' => $userId,
                            'created_at' => date('Y-m-d h:i:s')
                        ]);

                    }
                    $response['response'] = 0;
                    $response['message'] = 'Are you sure you want to start this task? You are already working on another task of another Board?';
                }
            } else {
                $response['response'] = 2;
                $response['message'] = 'You donot have any task to end.';
            }
        }
        return $response;
        //}
    }

    public function getProjectBoards(Request $request)
    {

        $projectId = $request->input('projectId');

        $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
        $admin = \Config::get('constants.ROLE.ADMIN');
        $role = $request->session()->get('user_role');
        $userIsAdmin = (new UserProject())->where(['project_id' => $projectId, 'user_id' => Auth::user()->id, 'deleted_at' => null])->first(['is_admin']);
        $boards = (new ProjectBoards())
            ->where(['project_id' => $projectId])->get(['project_board_id', 'project_board_name'])->toArray();

        if (!empty($boards)) {
            return view('users.users-company-project-boards-ajax', compact('boards','role','userIsAdmin'));
        } else {

            return view('users.users-company-project-boards-ajax', compact('boards','role','userIsAdmin'));
        }
    }

    public function updateBoardColumn(addBoards $request)
    {

        try {

            $columnName = $request->input('columnName');
            $columnId = $request->input('columnId');
            //echo $columnName;die;
            $ary = array(

                'column_name' => $columnName,
                'updated_at' => date('Y-m-d h:i:s'),
            );
            (new ProjectBoardColumn())->where(['project_board_column_id' => $columnId])->update($ary);
            echo '100';
        } catch (\Exception $e) {

            echo '0';
        }
    }

    public function checkColumnData(Request $request)
    {

        try {

            $columnId = $request->input('columnId');

            $tasks = (new ProjectTasks())->where(['project_board_column_id' => $columnId])->get()->toArray();
            if (empty($tasks)) {

                (new ProjectBoardColumn())->where(['project_board_column_id' => $columnId])->delete();
                (new UserProjectColumn())->deleteChildRecord($columnId);
            }

            if (!empty($tasks)) {

                $data = (new ProjectBoardColumn())->where(['project_board_column_id' => $columnId])->first(['project_board_id']);
                $boards = (new ProjectBoardColumn())->where(['project_board_id' => $data->project_board_id])->get();
                if (count($boards) == 1) {
                    echo '10';
                    exit;
                }
                if (!empty($data)) {

                    return view('boards.board-columns-change-other', compact('boards', 'columnId', 'data'));
                }
            } else {

                echo '0';
            }
        } catch (\Exception $e) {
            echo '0';
        }
    }

    function moveColumnsTask(Request $request)
    {
        try {

            $removeColumnId = $request->input('removeColumn');
            $moveToColumn = $request->input('radioColumns');
            $boardID = $request->input('boardID');
            if (isset($moveToColumn) && isset($removeColumnId) && isset($boardID)) {

                $ary = array(
                    'project_board_column_id' => $moveToColumn,
                    'updated_at' => date('Y-m-d h:i:s'),
                );
                (new ProjectTasks())->where(['project_board_column_id' => $removeColumnId])->update($ary);

                (new ProjectBoardColumn())->where(['project_board_column_id' => $removeColumnId])->delete();
                (new UserProjectColumn())->deleteChildRecord($removeColumnId);
                $request->session()->flash('success', 'Task moved & Column deleted successfully');

            }
            return redirect('board-detail/' . $boardID);
        } catch (Exception $e) {

            $request->session()->flash('error', $e->getMessage());
            return redirect('board-detail/' . $boardID);
        }
    }


    function addNewColumn(Request $request)
    {
        try {

            $boardId = $request->input('boardId');
            $columnName = $request->input('columnName');
            $singleColumnColorCode = \Config::get('constants.SINGLE_COLUMN_COLOR_CODE');
            $userId = Auth::user()->id;

            $projectUsers = (new ProjectBoards())
                ->join('user_projects','user_projects.project_id','=','project_boards.project_id')
                ->where(['project_boards.project_board_id'=> $boardId])
                ->get(['user_projects.user_id']);
            $usersArray = [];
            $projectUsersAry = (array)$projectUsers;
            if(!empty($projectUsersAry)){
                $usersArray = $projectUsers->toArray();
            }
            $arry = array(

                'project_board_id'     => $boardId,
                'column_name'          => $columnName,
                'column_color_code'    => $singleColumnColorCode,
                'created_by_user_id'   => $userId,
                'created_at'           => date('Y-m-d h:i:s'),
            );
            $insId = (new ProjectBoardColumn())->insertGetId($arry);
            if(!empty($usersArray)) {
                foreach ($usersArray as $users) {
                    $insertResultInUserBoardColumn[] = array(
                        'user_id' => $users['user_id'],
                        'project_board_column_id' => $insId,
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s'),
                    );
                }

                $insertDefaultBoard = (new UserProjectColumn())->insert($insertResultInUserBoardColumn);
            }else{

                $insertResultInUserBoardColumn = array(
                    'user_id' => $userId,
                    'project_board_column_id' => $insId,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                );
                $insertDefaultBoard = (new UserProjectColumn())->insertGetId($insertResultInUserBoardColumn);
            }
            if ($insId) {
                $data = (new ProjectBoardColumn())->where(['project_board_column_id' => $insId])->first();
                $returnHTML = view('boards.new-board-column-design', compact('data'))->render();
                return response()->json(array('success' => true, 'html'=>$returnHTML,'last_id' => $insId));
            } else {

                echo '0';
            }

        } catch (\Exception $e) {

            pr($e->getMessage());
        }
    }


    public function ajaxSortDragTaskOrder(Request $request)
    {

        if ($request->ajax()) {
            $inputs = $request->all();
            $ProjectBoardColumnId = $inputs['board_column_id'];
            $taskId = isset($inputs['project_task_id_single']) ? $inputs['project_task_id_single'] : '';
            $ProjectBoardColumnIdFrom = isset($inputs['board_column_id_from']) ? $inputs['board_column_id_from'] : '';
            $ProjectTaskId = isset($inputs['project_task_id']) ? $inputs['project_task_id'] : '';
            $ProjectTaskIdImplode = implode(',', $ProjectTaskId);

            $projectId = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id','subject']);
            $columnNamefrom = (new ProjectBoardColumn())->where(['project_board_column_id' => $ProjectBoardColumnIdFrom])->first(['column_name']);
            $columnNameTo = (new ProjectBoardColumn())->where(['project_board_column_id' => $ProjectBoardColumnId])->first(['column_name']);
            $companyId = $request->session()->get('company_id');
            $companyInfo = (new Companies())->companyUsersData($companyId);
            if (count($ProjectTaskId) > 0) {
                $i = 1;
                $customQuery = "";

                foreach ($ProjectTaskId as $taskId) {
                    if ($i <= count($ProjectTaskId)) {
                        $customQuery .= " WHEN project_task_id = " . $taskId . " THEN " . $i . " ";
                    }
                    $i++;
                }
                $sqlQuery = "UPDATE `project_tasks` SET  `task_order_id`= CASE " . $customQuery . " END WHERE `project_board_column_id` =" . $ProjectBoardColumnId;

                $updateRawStatment = DB::statement($sqlQuery);


                $templateLogInfo = (new TemplateLog())
                    ->where([
                        ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.MOVE-TASK')],
                        ['type', '=', 1],
                    ])->first();

                $userName = Auth::user()->name;
                $loginUserId = Auth::user()->id;
                $taskTitle = $projectId->subject;
                $message = $templateLogInfo->message;
                $userTaskDes = array("#user_first_name#", "#task_name#", "#from#", "#to#");
                $userTaskRep = array($userName, $taskTitle, $columnNamefrom['column_name'], $columnNameTo['column_name']);
                $tags = $templateLogInfo->tags;
                $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

                if($ProjectBoardColumnId !=  $ProjectBoardColumnIdFrom) {

                    (new TaskActivity())->insert([
                        'project_id' => $projectId->project_id,
                        'project_task_id' => $taskId,
                        'message' => $newTaskDes,
                        'user_id' => $loginUserId,
                        'created_at' => date('Y-m-d h:i:s')
                    ]);

//                    $userDes = (new ProjectTaskAssignees())
//                        ->join('users', 'users.id', '=', 'project_task_assignees.user_id')
//                        ->where(['project_task_id' => $taskId,'project_task_assignees.deleted_at' => null])
//                        ->get(['users.name', 'users.email']);
//
//                    $finalData = [];
//                    foreach ($userDes as $user) {
//                        $user->name;
//                        $message = $templateLogInfo->message;
//                        $userTaskDes = array("#user_first_name#", "#task_name#", "#from#", "#to#");
//                        $userTaskRep = array($userName, $taskTitle);
//                        $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
//                        $messageBody = '<p style="text-align: left;">Hello ' . $user->name . '</p>' . '<p style="text-align: left;">' . $newTaskDes . ':</p>';
//
//                        $finalData [] = [
//                            'message_type' => 2,
//                            'subject' => $taskTitle,
//                            'message_body' => $messageBody,
//                            'email' => $user->email,
//                            'from_email' => $companyInfo->email,
//                            'from_company'  => $companyInfo->name,
//                            'created_at' => date('Y-m-d h:i:s')
//
//
//                        ];
//                    }
//
//
//                    (new MessageToBeSend())->insert($finalData);
                }

            }


        }
    }

    public function removeAddBoardColumn(Request $request)
    {
        if ($request->ajax()) {
            try {
                $inputs = $request->all();
                $userId = Auth::user()->id;
                $ProjectBoardColumnId = $inputs['board_column_id'];
                $status = $inputs['status'];
                $array = array(
                    'project_board_column_id' => $ProjectBoardColumnId,
                    'user_id' => $userId
                );

                if ($status == 'hide') {
                    $insert = (new UserProjectColumn())->withTrashed()
                        ->where('project_board_column_id', $ProjectBoardColumnId)
                        ->where('user_id', $userId)
                        ->restore();

                    if ($insert) {
                        $response = array('status' => 1);
                    } else {
                        $response = array('status' => 3);
                    }

                } else {
                    $checkUserAlreadyWorking = (new ProjectTaskLoggedTime())->alreadyWorkingonTask($ProjectBoardColumnId, $userId);
                    if (count($checkUserAlreadyWorking) > 0) {
                        $response = array('status' => 4);
                    } else {
                        $delete = (new UserProjectColumn())->where($array)->delete();
                        if ($delete) {
                            $response = array('status' => 2);
                        } else {
                            $response = array('status' => 3);
                        }
                    }
                }
                return $response;

            } catch (Exception $e) {

            }

        }
    }

    public function ajaxGetColumnTaskDetail(Request $request)
    {
        if ($request->ajax()) {
            try {
                $inputs = $request->all();
                $userId = Auth::user()->id;
                $ProjectBoardColumnId = $inputs['board_column_id'];
                $columnName = $inputs['columnName'];

                $array = array(
                    'project_board_column_id' => $ProjectBoardColumnId,
                    'user_id' => $userId
                );
                $data = array('column_name' => $columnName,
                    'project_board_column_id' => $ProjectBoardColumnId,
                );
                $result = (new ProjectTasks())->fetchUsingColumnId($ProjectBoardColumnId);

                return view('boards.new-board-column-task', compact('data', 'result'));
            } catch (Exception $e) {

            }

        }
    }



    public function assignedTasks(Request $request, $bid)
    {
        try {
            $inputs = $request->all();
            $userId = Auth::user()->id;
            $sesCompanyId = $request->session()->get('company_id');
            $result['userId'] = $userId;
            $result['boardId'] = $bid;
            $role = $request->session()->get('user_role');
            $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
            $admin = \Config::get('constants.ROLE.ADMIN');
            $companyId = $request->session()->get('company_id');
            $checkBoard = (new ProjectBoards())->checkforaccess($bid);
            $result['user_role'] = $role;

            if (!empty($checkBoard->company_id) && $checkBoard->company_id == $sesCompanyId) {

                $project = (new ProjectBoards())->where(['project_board_id' => $bid])->first(['project_id'])->toArray();
                $userIsAdmin = (new UserProject())->where(['project_id' => $project['project_id'], 'user_id' => Auth::user()->id])->first(['is_admin']);
                $userIsAdminAry = (array)$userIsAdmin;
                if (!empty($userIsAdminAry)) {
                    if(is_object($userIsAdmin)){

                        $userIsAdmin->toArray();
                    }
                    $result['projects'] = (new Project())->getUsersProjects($userId,$sesCompanyId);

                    if (isset($inputs['action']) && $inputs['action'] == 'board-with-task' ) {

                        $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');
                        $page = isset($inputs['page']) && $inputs['page'] > 0 ? $inputs['page'] : 0;
                        $columnId = isset($inputs['column_id']) && $inputs['column_id'] != '' ? $inputs['column_id'] : '';
                        $offset = $page * $limit;
                        $ProjectsTasks = (new ProjectBoards())->fetchUserProjectColumnTask($bid, $userId, $role, '', $userIsAdmin['is_admin'], '',$inputs['column_id'],$offset,$mytask='mytask');
                        //pr($ProjectsTasks);die;
                        $result['data']  = $ProjectsTasks;

                        //pr($result['data']);die;
                        $arrayResult = 1;
                        return view('boards.board-column-task', $result, compact('arrayResult'));
                    }else {

                        $result['data'] = (new ProjectBoards())->fetchUserProjectsAndTask($bid, $userId, $role, $userIsAdmin['is_admin']);
                    }
                    $result['ColumnName'] = (new ProjectBoardColumn())->fetchColumnsName($bid);
                    //pr($result['data']);die;
                    if (!empty((array)$result['data'])) {

                        $result['boards'] = (new ProjectBoards())
                            ->where(['project_id' => $result['data']->project_id])
                            ->get()
                            ->toArray();

                        $companyId = $request->session()->get('company_id');

                        $role = $request->session()->get('user_role');
                        $result['usersList'] = '';
                        if ($role == $superAdmin) {

                            $result['usersList'] = (new UserProject())->getAdminsUserList($project['project_id']);

                        } else if ($role == $admin) {

                            $result['usersList'] = (new UserProject())->projectAdminNormalUserList($project['project_id'], $companyId);
                        } else {

                            $result['UserIsAdmin'] = $userIsAdmin['is_admin'];
                            //pr($userIsAdmin['is_admin']);die;
                            if ($userIsAdmin['is_admin'] == '1') {

                                $result['usersList'] = (new UserProject())->projectAdminUserList($project['project_id'], $companyId);
                            }
                        }
                    } else {
                        $role = $request->session()->get('user_role');
                        $result['usersList'] = '';
                        if ($role == $superAdmin) {

                            $result['usersList'] = (new UserProject())->getAdminsUserList($project['project_id']);

                        } else {

                            $result['UserIsAdmin'] = $userIsAdmin['is_admin'];
                            //pr($userIsAdmin['is_admin']);die;
                            if ($userIsAdmin['is_admin'] == '1' or $role == $admin) {

                                $result['usersList'] = (new UserProject())->projectAdminUserList($project['project_id'], $companyId);
                            } else {
                                $result['usersList'] = (new UserProject())->projectAdminUserList($project['project_id'], $companyId);
                            }
                        }
                    }
                    //pr($result['data'],1);
                    return view('boards.boards-columns', $result);
                } else {

                    $request->session()->flash('error', 'Unauthorised Access');
                    return redirect('dashboard');
                }
            }else {

                $request->session()->flash('error', 'Unauthorised Access');
                return redirect('dashboard');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            //$request->session()->flash('error', 'Sorry something went wrong. Please try again.');
        }
    }

    public function userBoardTask($boardId,$userId){

        $result['data'] = (new ProjectBoards())->fetchUserProjectColumn($boardId, $userId, '3');

    }

    public  function getAllBoardsAndTaskProjectLabel(Request $request, $bid ,$userId = null){

           $projectId =  $bid;
            $checkId = '';
            if(empty($userId)) {
                $userId = Auth::user()->id;
            }else{
                $checkId = $userId;
            }
            $role = $request->session()->get('user_role');//echo $role;die;
            $srole = (new CompanyUser())->where(['user_id' => $userId])->first(['role']);
            $simpleRole = $srole->role;

            $sesCompanyId = $request->session()->get('company_id');

            $result['userId'] = $userId;
            $result['boardId'] = $bid;

            $userProjectId = (new ProjectBoards())->where(['project_id' => $bid])->first(['project_board_id']);
            $checkBoard = (new ProjectBoards())->checkforaccess($userProjectId->project_board_id);


            $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
            $admin = \Config::get('constants.ROLE.ADMIN');
            $companyId = $request->session()->get('company_id');
            $projectAdmin = \Config::get('constants.ROLE.USER');
            if (!empty($checkBoard->company_id) && $checkBoard->company_id == $sesCompanyId) {

                $project = (new ProjectBoards())->where(['project_board_id' => $userProjectId->project_board_id])->first(['project_id'])->toArray();
                $userIsAdmin = (new UserProject())->where(['project_id' => $project['project_id'], 'user_id' => Auth::user()->id])->first(['is_admin']);



                $userIsAdminAry = (array)$userIsAdmin;
                if (!empty($userIsAdminAry)) {
                    if (is_object($userIsAdmin)) {

                        $userIsAdmin = $userIsAdmin->toArray();
                    }
                    $company = (new Project())->where(['project_id' => $project['project_id']])->first(['company_id'])->toArray();
                    if ($role == \Config::get('constants.ROLE.USER')) {
                        $result['projects'] = (new Project())->getUsersProjects($userId,$sesCompanyId);
                    } else {
                        $result['projects'] = (new Project())->getAdminsProjects($sesCompanyId);
                    }

                     $result['data'] = (new ProjectBoards())
                        ->fetchUserProjectColumn($userProjectId->project_board_id, $userId, $role, $simpleRole, $userIsAdmin['is_admin'], $checkId);
                    $result['ColumnName'] = (new ProjectBoardColumn())->fetchColumnsName($bid);
                    if (!empty((array)$result['data'])) {

                        $result['boards'] = (new ProjectBoards())->where(['project_id' => $result['data']->project_id])
                            ->get()->toArray();
                    }

                    $role = $request->session()->get('user_role');
                    $result['usersList'] = '';
                    if ($role == $superAdmin) {

                        $result['usersList'] = (new UserProject())->getAdminsUserList($project['project_id']);

                    } else if ($role == $admin) {

                        $result['usersList'] = (new UserProject())->projectAdminNormalUserList($project['project_id'], $companyId);
                    } else {

                        $result['UserIsAdmin'] = $userIsAdmin['is_admin'];
                        if ($userIsAdmin['is_admin'] == '1') {
                            $result['usersList'] = (new UserProject())->projectAdminUserList($project['project_id'], $companyId);
                            $result['userSuperAdmin'] = (new UserProject())->projectAdminUserListGetSuperAdmin($project['project_id'], $companyId);
                        }
                    }
                    $result['user_role'] = $role;
                    $inputs = $request->all();

                    if (isset($inputs['action']) && $inputs['action'] == 'board-with-task' ) {
                        $boardId=  $inputs['project_board_id'];
                        $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');
                        $page = isset($inputs['page']) && $inputs['page'] > 0 ? $inputs['page'] : 0;
                        $columnId = isset($inputs['column_id']) && $inputs['column_id'] != '' ? $inputs['column_id'] : '';
                        $offset = $page * $limit;

                       if($columnId == 'all'){

                           $offset = 0;
                           $columnId = '';
                       }else{
                        $offset = $page * $limit;
                        }
                        $boardC['project_boards_all'] = (new ProjectBoards())->fetchUserProjectBoardAndTask($projectId,$offset,$boardId,$columnId,$limit);

                         $boardC['project_boards_all'] = isset($boardC['project_boards_all'][0]['project_boards_all']) ?  $boardC['project_boards_all'][0]['project_boards_all'] : [];
                        // pr($boardC['project_boards_all']);
                        //die;
                        //pr($boardC['project_boards_all']);die;
                        $view = 'board-task-data';
                        return view('boards.' . $view, compact('boardC'));
                    }
                    else{

                        $result['boardWithTasks']= (new ProjectBoards())->fetchUserProjectBoardAndTask($projectId);

                        //pr($result['boardWithTasks'],1);
                        return view('boards.board-task', $result)->with('project_id', $projectId);
                    }
                } else {
                    $request->session()->flash('error', 'Unauthorised Access');
                    return redirect('dashboard');
                }
            } else {
                $request->session()->flash('error', 'Unauthorised Access');
                return redirect('dashboard');
            }



    }

    public function getBoardColumn(Request $request){
        $inputs = $request->all();
        $boardId = $inputs['boardId'];
        $userProjectId = (new ProjectBoards())->where(['project_board_id' => $inputs['boardId']])->first(['project_id']);

        $getAllColumns = (new ProjectBoardColumn())
            ->select('column_name','project_board_column_id','project_board_id')
            ->where('project_board_id','=',$inputs['boardId'])
            ->where('deleted_at','=', NULL)
            ->get();
        return view('boards.get-column', compact('getAllColumns','userProjectId','boardId'));
    }

    public function searchTask(Request $request){
        $inputs = $request->all();
        $userId = Auth::user()->id;
        $searchProjectId = (!empty($inputs['project_id']) ? trim($inputs['project_id']) : '');
        $searchBoardId = (!empty($inputs['board_id']) ? trim($inputs['board_id']) : '');
        $search = (!empty($inputs['keyword']) ? trim($inputs['keyword']) : '');
        $sesCompanyId = $request->session()->get('company_id');


        $projects = (new Project())
            ->join('user_settings',function($join){
                $join->on('user_settings.project_id','=','projects.project_id');
                $join->on('user_settings.company_id','=','projects.company_id');
            })
            ->where(['projects.company_id' => $sesCompanyId,'user_settings.user_id' => $userId])->get();

        $searchTasks = DB::table('project_tasks')
            ->join('projects', 'projects.project_id', '=', 'project_tasks.project_id')
            ->join('user_projects', 'user_projects.project_id', '=', 'project_tasks.project_id')
            ->join('project_boards', 'project_boards.project_id', '=', 'project_tasks.project_id')
            ->where('project_tasks.subject', 'LIKE', '%' . $search . '%')
            ->where('user_projects.user_id' , $userId)
            ->where('projects.company_id' , $sesCompanyId)
            ->where(function($query) use ($searchProjectId) {

                if ($searchProjectId != '') {
                    $query->where('project_tasks.project_id', $searchProjectId);
                }
            })
            ->where(function($query) use ($searchBoardId) {

                if ($searchBoardId != '') {
                    $query->where('project_tasks.project_board_id', $searchBoardId);
                }
            })
            ->orderBy('project_tasks.created_at', 'DESC')
            ->select('project_tasks.subject', 'project_tasks.project_board_id', 'project_tasks.project_id','projects.name','project_boards.project_board_name','project_tasks.created_at','project_tasks.project_task_id','user_projects.user_id')
            ->groupBy('project_tasks.project_task_id')
            ->paginate(8);
        $projectId = isset($inputs['project_id']) ? $inputs['project_id'] : '';
        $boardId = isset($inputs['board_id']) ? $inputs['board_id'] : '';
        $keyword = isset($inputs['keyword']) ? $inputs['keyword'] : '';
       // pr($searchTasks, 1);
        //die;
        return view('layouts.search_task', compact('searchTasks','projects','search','projectId','boardId','keyword'));

    }

}