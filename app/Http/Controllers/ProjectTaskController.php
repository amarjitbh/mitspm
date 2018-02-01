<?php

namespace App\Http\Controllers;

use App\Companies;
use App\CompanyUsers;
use App\CountriesTimeZone;
use App\Http\Requests\ProjectTaskRequest;
use App\Http\Requests\TaskCommentRequest;
use App\ProjectBoardColumn;
use App\ProjectTaskAssignees;
use App\ProjectTaskLoggedTime;
use App\TaskComments;
use App\User;
use App\TemplateLog;
use App\UserProject;
use App\TaskActivity;
use App\MessageToBeSend;
use Faker\Provider\Image;
use Faker\Provider\ka_GE\DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mail;
use App\UserProjectColumn;
use Illuminate\Support\Facades\Auth;
use App\ProjectTasks;
use Illuminate\Http\Request;
use League\Flysystem\Config;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectTaskController extends Controller
{


    public function createTask($proId, $bid)
    {

        $data['project_id'] = $proId;
        $data['board_id'] = $bid;
        $BoardColumnId = (new ProjectBoardColumn())->where(['project_board_id' => $bid, 'column_name' => 'backlogs'])->first();
        $data['project_board_column_id'] = $BoardColumnId->project_board_column_id;
        return view('tasks.create-task', $data);
    }

    public function addTask(ProjectTaskRequest $request){



        try {
            //  \DB::beginTransaction();
            $role = $request->session()->get('user_role');
            $checkUsers = $request->input('users');
            $users = explode(",",$checkUsers);

            $subject = $request->input('subject');
            $submitType = $request->input('submitType');
            $description = $request->input('description');
            $project_id = $request->input('project_id');
            $project_board_id = $request->input('project_board_id');
            $project_board_column_id = $request->input('project_board_column_id');
            $priority = $request->input('priority');
            $userId = Auth::user()->id;
            $companyId = $request->session()->get('company_id');
            $companyInfo = (new Companies())->companyUsersData($companyId);

            $taskOrderId = (new ProjectTasks())->fetchUsingColumnId($project_board_column_id);
            foreach ($taskOrderId as $orderId) {

                $update = (new ProjectTasks())
                    ->where('project_task_id', $orderId['project_task_id'])
                    ->where('project_board_column_id', $project_board_column_id)
                    ->increment('task_order_id');
            }
            $image = $request->file('file');
            if(!empty($image)) {
                $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images');
                $image->move($destinationPath, $input['imagename']);
                $filePath = $destinationPath.'/'.$input['imagename'];
                $newPath = public_path('images/thumbnail/'.$input['imagename']);
                //chmod($destinationPath.'/', 0777);
                //$size = 600;
                //cropImage($filePath,$newPath,'100');
                // createThumb($newPath, $filePath, 150, 200);

            }

            $ary = array(

                'subject' => $subject,
                'description' => $description,
                'project_id' => $project_id,
                'project_board_id' => $project_board_id,
                'project_board_column_id' => $project_board_column_id,
                'created_by' => $userId,
                'created_at' => date('Y-m-d h:i:s'),
                'task_order_id' => 1,
                'priority' => $priority,
                'file'              => !empty($input['imagename']) ? $input['imagename'] : '',
            );
            $insId = (new ProjectTasks())->insertGetId($ary);
            $userIsAdmin = (new UserProject())->where(['project_id' => $project_id,'user_id' => $userId])->first(['is_admin'])->toArray();
            $userIsAdmin = $userIsAdmin['is_admin'];

            if ($role == \Config::get('constants.ROLE.USER')) {
                if($userIsAdmin == '1'){
                    if (!empty($users)) {
                        foreach ($users as $uid) {

                            $asign = array(
                                'user_id' => $uid,
                                'project_task_id' => $insId,
                                'assigned_by_user_id' => $userId,
                                'logging_time' => Null,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                            $asnId[] = (new ProjectTaskAssignees())->insertGetId($asign);//pr($asnId);die;
                        }
                    }
                }else{
                    $asign = array(
                        'user_id' => $userId,
                        'project_task_id' => $insId,
                        'assigned_by_user_id' => $userId,
                        'logging_time' => Null,
                        'created_at' => date('Y-m-d h:i:s'),
                    );
                    $asnId[] = (new ProjectTaskAssignees())->insertGetId($asign);
                }
            } else {

                if ($insId) {
                    if (!empty($users)) {
                        foreach ($users as $uid) {

                            $asign = array(

                                'user_id' => $uid,
                                'project_task_id' => $insId,
                                'assigned_by_user_id' => $userId,
                                'logging_time' => Null,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                            $asnId[] = (new ProjectTaskAssignees())->insertGetId($asign);
                        }
                    }
                }
            }



            $assgnUser = (new ProjectTaskAssignees())->getAssignUsersDetail($asnId);
            $column_namedt = (new ProjectBoardColumn())->where(['project_board_column_id' => $project_board_column_id])->first(['column_name']);
            $companyId = $request->session()->get('company_id');
            $users = (new UserProject())->join('users', 'users.id', '=', 'user_projects.user_id')
                ->where(['project_id' => $project_id])->get(['users.id', 'users.name'])->toArray();

            if (empty($insId)) {
                $insId = $submitType;
            }

            if ($column_namedt) {


                $result = array(
                    'success' => 100,
                    'column' => $column_namedt->column_name,
                    'tsk_id' => $insId,
                    'users' => $assgnUser,
                    'proUser' => $users,
                    'priority' => $priority,
                    'authUserid'    => $userId,
                    'priorityConstant' => \Config::get('constants.priorityClass'),
                );

                /*** Send Email  ***/

                $templateLogInfo = (new TemplateLog())
                    ->where([['action_id', '=',config('constants.FUNCTIONALITY_TYPE.TASK.ADD')],['type', '=', 1],])
                    ->first();

                $userName = Auth::user()->name;
                $loginUserId = Auth::user()->id;
                $taskTitle = $request['subject'];
                $projectId = $request['project_id'];
                $message = $templateLogInfo->message;
                $userTaskDes = array("#user_first_name#","#task_name#");
                $userTaskRep   = array($userName,$taskTitle);
                $newTaskDes = str_replace($userTaskDes,$userTaskRep,$message);

                $crAry = ['project_id' => $projectId,'project_task_id' => $insId,
                    'user_id' => $loginUserId,
                    'message' => $newTaskDes,
                    'created_at' => date('Y-m-d :h:i:s')
                ];

                (new TaskActivity())->insert($crAry);
                $userDes =   (new User())->whereIn('id', $users = explode(",",$checkUsers))
                    ->get(['name','email']);


                $finalData = [];

                foreach ($userDes as $user){

                    $user->name;
                    $message = $templateLogInfo->message;
                    $userTaskDes = array("#user_first_name#", "#task_name#");
                    $userTaskRep   = array($userName, $taskTitle);
                    $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
                    $messageBody = '<p style="text-align: left;">Hello ' .$user->name.'</p>'.'<p style="text-align: left;">' . $newTaskDes. '.</p>';

                    $finalData [] = [
                        'message_type' => 2,
                        'subject' => $taskTitle,
                        'message_body' => $messageBody,
                        'email' => $user->email,
                        'from_email'    => $companyInfo->email,
                        'from_company'  => $companyInfo->name,
                        'created_at' => date('Y-m-d :h:i:s')

                    ];
                }

                (new MessageToBeSend())->insert($finalData);
                /*** End  ***/

            } else {
                $result = array(
                    'success' => 0,
                );
            }


            echo json_encode($result);
            //   \DB::commit();
        } catch (Exception $e) {
            //    \DB::rollback();
        }
    }


    public function taskLoggedTime()
    {

        return view('project_tasks.log-task-time', compact('projectTaskId'));
    }

    public function getTaskDetail(Request $request){
        $dateFormat =	$request->session()->get('company_setting_date');
        $timeFormat =    $request->session()->get('company_setting_time');
        $timeZoneId =    $request->session()->get('country_timezone_id');
        $timeZone = (new CountriesTimeZone())->where(['countries_timezone_id' => $timeZoneId])->first(['compare_utc']);

        if (empty((array) $timeZone) && !empty($timeZone->timezone)) {

            $timeZone = $timeZone->compare_utc;
        }

        /***  sesId -> session id ***/
        $userIsAdmin = '0';
        $sesId = \Auth::user()->id;
        $role = $request->session()->get('user_role');
        $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
        $admin = \Config::get('constants.ROLE.ADMIN');
        $superAdminForUsers = '';
        $inputs = $request->all();
        $taskId = $inputs['task_id'];
        $page = !empty($inputs['page'])?$inputs['page']:'';
        !empty($inputs['user_id']) ? $sesId = $inputs['user_id'] : $sesId ;

        $companyId = $request->session()->get('company_id');
        $result = (new ProjectTasks())->where(['project_task_id' => $taskId])->first()->toArray();
        $task_created_date= getLocalTimeZone($result['created_at'],1);

        $userIsAdmin = (new UserProject())->where(['project_id' => $result['project_id'],'user_id' => $sesId])
            ->first(['is_admin']);

        if(is_object($userIsAdmin)){
            $userIsAdmin = $userIsAdmin->toArray();
        }else{
            $userIsAdmin = array($userIsAdmin);
        }
        if(!empty($userIsAdmin['is_admin'])){
            $userIsAdmin = $userIsAdmin['is_admin'];
        }else{

            $userIsAdmin = $userIsAdmin['is_admin'];
        }

        $users = '';
        if($role == $superAdmin) {

            $users = (new UserProject())->getProjectUsers($result['project_id']);

        }else if($role == $admin){

            $users = (new UserProject())->projectAdminNoarmalUserListArray($result['project_id'],$companyId);
        }else{

            if($userIsAdmin == '1') {

                $users = (new UserProject())->projectAdminUserListArray($result['project_id'], $companyId);
                $superAdminForUsers = (new UserProject())->projectAdminUserListGetSuperAdmin($result['project_id'], $companyId);

            }
        }

        $asgnUser = (new ProjectTaskAssignees())
            ->join('users','users.id','=','project_task_assignees.user_id')
            ->where(['project_task_id' => $taskId])
            ->groupBy('user_id')
            ->get(['user_id','users.name'])->toArray();
        $comments = (new TaskComments())->getTaskComments($taskId,$dateFormat,$timeFormat,$timeZone['compare_utc'],$page);

        //pr($comments);die;
        $taskTimmings = (new ProjectTaskLoggedTime())->getTaskTimmings($taskId,$sesId,$timeFormat,$timeZone['compare_utc']);
        //pr($taskTimmings,1);
        $timeAry = [];
        $allTimeData = [];

        foreach($taskTimmings as $taskTimmes){
            //echo $taskTimmes['start_time'];
            $OneTaskTime = getTotalTime($taskTimmes['start_time'],$taskTimmes['end_time']);
            $OneTaskTimeSec = toSeconds($OneTaskTime);
            if($OneTaskTimeSec > 86400){

                $dtF = new \DateTime('@0');
                $dtT = new \DateTime("@$OneTaskTimeSec");
                //echo  $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
                $days = $dtF->diff($dtT)->format('%a');
                $lastTime = '23:59:59';
                //$lastTime = getLocalTimeZone($lastTime, 1);
                $staticStartTime = '00:00:00';

                $timingDate1 = date('Y-m-d', strtotime($taskTimmes['start_time']));
                $endDate1 = date('Y-m-d', strtotime($taskTimmes['end_time']));

                $dateOne =  date('Y-m-d', strtotime($timingDate1));
                for($i=0;$i<= $days; $i++){

                    $startDate =  date('Y-m-d', strtotime($taskTimmes['start_time']));

                    $date =  date('Y-m-d', strtotime('+'.$i.' day', strtotime($startDate)));
                    $date = getLocalTimeZone($date.' 00:00:00', 1);
                    $allTimeData[$date]['date'] = $date;

                    if($i == 0) {
                        //$EndLastTime = getLocalTimeZoneOnDefaultFaomat($startDate.' '.$lastTime, 2);
                        //echo $taskTimmes['start_time'].'=='.$date.' '.$EndLastTime;die;
                        $allTimeData[$startDate]['date'] = $startDate;
                        $allTimeData[$startDate]['dayTotal'][] = getTotalTime($taskTimmes['start_time'], $date.' '.$lastTime);
                        $allTimeData[$startDate]['timeAry'][] = [

                            'time_start_time' => getLocalTimeZone($taskTimmes['start_time'], 2),
                            'time_end_time' => getLocalTimeZone($date . ' ' . $lastTime, 2),
                            'time_total' => getTotalTime($taskTimmes['start_time'], $date.' '.$lastTime),
                        ];

                    }else if($i == $days && $date != $startDate){

                        $allTimeData[$date]['dayTotal'][] = getTotalTime($date.' '.$staticStartTime, $taskTimmes['end_time']);
                        $allTimeData[$date]['timeAry'][] = [

                            'time_start_time' => getLocalTimeZone($date.' '.$staticStartTime, 2),
                            'time_end_time' => getLocalTimeZone($taskTimmes['end_time'], 2),
                            'time_total' => getTotalTime($date.' '.$staticStartTime, $taskTimmes['end_time']),
                        ];
                    }else{

                        $allTimeData[$date]['dayTotal'][] = getTotalTime($date.' '.$staticStartTime, $date . ' ' . $lastTime);
                        $allTimeData[$date]['timeAry'][] = [

                            'time_start_time' => getLocalTimeZone($date.' '.$staticStartTime, 2),
                            'time_end_time' => getLocalTimeZone($date . ' ' . $lastTime, 2),
                            'time_total' => getTotalTime($date.' '.$staticStartTime, $date . ' ' . $lastTime),
                        ];
                    }
                }
                rsort($allTimeData);
            }else {

                //echo $taskTimmes['start_time'].'===='.getLocalTimeZone($taskTimmes['start_time'],2);die;
                $timingDate1 = date('Y-m-d', strtotime($taskTimmes['start_time']));
                $endDate1 = date('Y-m-d', strtotime($taskTimmes['end_time']));
                //echo $timingDate1;die;
                $timingDate = getLocalTimeZone($timingDate1, 1);
                $endDate = getLocalTimeZone($endDate1, 1);
                $allTimeData[$timingDate]['date'] = $timingDate;
                $allTimeData[$timingDate]['dayTotal'][] = getTotalTime($taskTimmes['start_time'], $taskTimmes['end_time']);
                //$allTimeData[$timingDate]['dayTotal'][] = "00:00:00";
                $allTimeData[$timingDate]['timeAry'][] = [

                    'time_start_time' => getLocalTimeZone($taskTimmes['start_time'], 2),
                    'time_end_time' => getLocalTimeZone($taskTimmes['end_time'], 2),
                    'time_total' => getTotalTime($taskTimmes['start_time'], $taskTimmes['end_time']),
                ];
            }
        }//pr($allTimeData,1);
        $startTime  = array_column($taskTimmings,'start_time');
        $endTime    = array_column($taskTimmings,'end_time');
        $totalTaskTimming = '';
        if(!empty($startTime[0]) && !empty($endTime[0])) {
            $startTime = explode(',', $startTime[0]);
            $endTime = explode(',', $endTime[0]);
            $totalTaskTimming = getTotalTimes($startTime, $endTime);
        }
       $projectAndBoardName = (new ProjectTasks())
            ->join('projects','projects.project_id','=','project_tasks.project_id')
            ->join('project_boards','project_boards.project_board_id','=','project_tasks.project_board_id')
            ->where(['project_tasks.project_task_id' => $taskId])
            ->first(['projects.name', 'project_boards.project_board_name']);

        $projectName = $projectAndBoardName->name;
        $boardName = $projectAndBoardName->project_board_name;

        $priorities = \Config::get('constants.priority');
        $result['created_task_date']=$task_created_date;
        if ($result) {
            $ary = array(

                'success'               => '100',
                'data'                  => $result,
                //'task_created'          => ,
                'users'                 => $users,
                'asgnUser'              => $asgnUser,
                'userRole'              => $role,
                'tskComments'           => $comments,
                'sesId'                 => $sesId,
                'isUserAdmin'           => $userIsAdmin,
                'pariorities'           => $priorities,
                'superAdminForUsers'    => $superAdminForUsers,
                'taskTimmings'          => $allTimeData,
                'total_task_timming'    => $totalTaskTimming,
                'user_id'               => $sesId,
                'dateFormat'            => $dateFormat,
                'timeFormat'            => $timeFormat,
                'timeZone'              => $timeZone,

                'project_name'          => $projectName,
                'board_name'            => $boardName,

                'todayDate'             => date($dateFormat),
                'yesterdayDate'         => date($dateFormat,strtotime("-1 days")),

            );
        } else {
            $ary = array(
                'success' => 0,
            );
        }
        echo json_encode($ary);
    }

    public function getUserTaskLogs(Request $request){
        $inputs = $request->all();
        $userId = $inputs['user_id'];
        $taskId = $inputs['task_id'];

        $dateFormat =	$request->session()->get('company_setting_date');
        $timeFormat =    $request->session()->get('company_setting_time');
        $timeZoneId =    $request->session()->get('country_timezone_id');
        $timeZone = (new CountriesTimeZone())->where(['countries_timezone_id' => $timeZoneId])->first(['compare_utc']);
        if (empty((array) $timeZone) && !empty($timeZone->timezone)) {

            $timeZone = $timeZone->compare_utc;
        }
        if(!empty($timeZone)){
            $timeZone = $timeZone['compare_utc'];
        }

        $taskLogs = (new TaskActivity())->getTasksActivities($taskId,$userId,$dateFormat,$timeFormat,$timeZone);
        if(!empty($taskLogs)){

            $ary = [
                'success'               => '100',
                'taskLogs'              => $taskLogs,
            ];
        }else{

            $ary = [
                'success'               => '0',
            ];
        }
        return json_encode($ary);
    }

    public function updateTask(ProjectTaskRequest $request)
    {

        $role = $request->session()->get('user_role');
        $asgnById = Auth::user()->id;
        $taskId = $request->input('taskId');
        $superAdmin = \Config::get('constants.ROLE.SUPERADMIN');
        $users = $request->input('users');
        if($role != $superAdmin){

            $taskSuperAdmin = (new ProjectTaskAssignees())
                ->join('project_tasks','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
                ->join('projects','projects.project_id','=','project_tasks.project_id')
                ->join('company_users','company_users.company_id','=','projects.company_id')
                ->where(['project_task_assignees.project_task_id' => $taskId,'company_users.role' => '1'])
                ->first(['company_users.user_id']);
        }

        if(!empty($taskSuperAdmin->user_id)){


            $taskSuperAdmin = (new ProjectTaskAssignees())
                ->where(['project_task_assignees.project_task_id' => $taskId,'project_task_assignees.user_id' => $taskSuperAdmin->user_id])
                ->first(['user_id']);
        }
        //pr($taskSuperAdmin);die;
        $projectTaskUsers = (new ProjectTaskAssignees())->where(['project_task_assignees.project_task_id' => $taskId])
            ->join('project_task_logged_time','project_task_logged_time.project_task_id','=','project_task_assignees.project_task_id')
            ->where(['project_task_logged_time.end_time' => null])
            ->get(['user_id']);
        //pr($projectTaskUsers);die;
        if(!empty($users)) {
            $projectTaskUsersAry = (array)$projectTaskUsers;
            if (!empty($projectTaskUsersAry)) {
                foreach ($projectTaskUsers as $proTaskUser) {
                    if(!in_array($proTaskUser->user_id ,$users)){

                        $taskInputs = [
                            'check' => 'false',
                            'new_project_task_id' => '0',
                            'project_task_id' => $taskId,
                            'value' => 'Pause',
                        ];
                        $result = (new ProjectBoardsController())->ajaxEndTaskLoggin($request, $taskInputs, $proTaskUser->user_id);
                    }
                }
            }
        }
        $admin = \Config::get('constants.ROLE.ADMIN');
        $project['project_id'] = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id'])->toArray();
        $userIsAdmin = (new UserProject())->where(['project_id' => $project['project_id'],'user_id' => $asgnById])->first(['is_admin'])->toArray();
        $userIsAdmin = $userIsAdmin['is_admin'];

        /*  Email Work  */
        $userList =  (new User())->whereIn('id', $users)
            ->get(['name'])->toArray();
        $newUserList = array_column($userList,'name');
        $usersList = implode(', ', $newUserList);



        $preUser1 = (new ProjectTaskAssignees())->where(['project_task_id' => $taskId])->get(['user_id'])->toArray();
        $preUser = array_column($preUser1,'user_id');
        $diffUsers = array_intersect($users,$preUser);
        $user1 = count($diffUsers);
        $user2 = count($users);

        $projectId = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id']);
        $templateLogInfo = (new TemplateLog())
            ->where([
                ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.EDIT')],
                ['type', '=', 1],
            ])->first();

        $userName = Auth::user()->name;
        $loginUserId = Auth::user()->id;
        $taskTitle = $request['subject'];
        $message = $templateLogInfo->message;
        $userTaskDes = array("#user_first_name#", "#task_name#");
        $userTaskRep = array($userName, $taskTitle);
        $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

        if($user1 != $user2 || count($preUser) != $user2){
            $newMessage = $newTaskDes. ' and now assign these task ' . $usersList;
            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $taskId,
                'message' => $newMessage,
                'user_id' => $loginUserId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        else{
            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $taskId,
                'message' => $newTaskDes,
                'user_id' => $loginUserId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        /*  Email Work  */

        $userDes =   (new User())->whereIn('id', $users)
            ->get(['name','email']);

        $finalData = [];
        foreach ($userDes as $user){
            $user->name;
            $message = $templateLogInfo->message;
            $userTaskDes = array("#user_first_name#", "#task_name#");
            $userTaskRep   = array($userName, $taskTitle);
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
            $messageBody = '<p style="text-align: left;">Hello ' .$user->name.'</p>'.'<p style="text-align: left;">' . $newTaskDes. '.</p>';

            $finalData [] = [
                'message_type' => 2,
                'subject' => $taskTitle,
                'message_body' => $messageBody,
                'email' => $user->email,
                'created_at' => date('Y-m-d :h:i:s')

            ];
        }
        (new MessageToBeSend())->insert($finalData);


        /*  Update Work  */
        if ($role == $superAdmin or $role == $admin or $userIsAdmin == '1') {

            $priority = $request->input('priority');
            $subject = $request->input('subject');
            $description = $request->input('description');
            $userData = (new ProjectTaskAssignees())->where(['project_task_id' => $taskId])
                ->get(['user_id'])->toArray();
            $NotInData = (new ProjectTaskAssignees())->where(['project_task_id' => $taskId])
                ->whereNotIn('user_id', $users)
                ->get(['user_id'])->toArray();

            $ary = array(

                'subject' => $subject,
                'description' => $description,
                'priority' => $priority,
                'updated_at' => date('Y-m-d :h:i:s'),
            );
            (new ProjectTasks())->where(['project_task_id' => $taskId])->update($ary);
            (new ProjectTaskAssignees())->where(['project_task_id' => $taskId])->delete();
            $restore = (new ProjectTaskAssignees())->where(['project_task_id' => $taskId])->withTrashed()
                ->whereIn('user_id', $users)
                ->restore();
            $allUsersData = (new ProjectTaskAssignees())->withTrashed()->where(['project_task_id' => $taskId])->get(['user_id'])->toArray();
            if (!empty($allUsersData)) {

                foreach ($allUsersData as $dtUser) {
                    $userDt[] = $dtUser['user_id'];
                }
            }
            if (!empty($users) && !empty($userDt)) {

                $varl = array_diff($users, $userDt);
                if (!empty($varl)) {
                    if($role == $superAdmin){

                        foreach ($varl as $lver) {

                            $arrry[] = array(

                                'project_task_id' => $taskId,
                                'user_id' => $lver,
                                'assigned_by_user_id' => $asgnById,
                                'logging_time' => null,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                        }
                        (new ProjectTaskAssignees())->insert($arrry);

                    }else{
                        foreach ($varl as $lver) {

                            $arrry[] = array(

                                'project_task_id' => $taskId,
                                'user_id' => $lver,
                                'assigned_by_user_id' => $asgnById,
                                'logging_time' => null,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                        }
                        (new ProjectTaskAssignees())->insert($arrry);


                        if(!empty($taskSuperAdmin->user_id)) {
                            $sarrry[] = array(

                                'project_task_id' => $taskId,
                                'user_id' => $taskSuperAdmin->user_id,
                                'assigned_by_user_id' => $asgnById,
                                'logging_time' => null,
                                'created_at' => date('Y-m-d h:i:s'),
                            );
                            (new ProjectTaskAssignees())->insert($sarrry);
                        }
                    }
                }
            } else {
                if (!empty($users)) {

                    $pro = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id'])->toArray();
                    if (!empty($pro['project_id'])) {
                        $project_id = $pro['project_id'];
                    }
                    if (!empty($project_id)) {
                        foreach ($users as $user_id) {
                            $proData = (new UserProject())->where(['project_id' => $project_id, 'user_id' => $user_id])->get()->toArray();
                            if (!empty($proData)) {

                                $nAry = array(
                                    'project_task_id' => $taskId,
                                    'user_id' => $user_id,
                                    'assigned_by_user_id' => $asgnById,
                                    'logging_time' => null,
                                    'created_at' => date('Y-m-d h:i:s'),
                                );
                                (new ProjectTaskAssignees())->insert($nAry);
                            }
                        }
                    }

                }
            }
            echo 100;
        } else {
            echo 0;
        }
    }

    public function taskComments(TaskCommentRequest $request)
    {

        try {
            \DB::beginTransaction();
            $inputs = \Request::all();
            if (isset($inputs['action']) && $inputs['action'] == 'action-remove-comment') {
                \DB::table('task_comments')->where('task_comment_id',  $inputs['commentId'])->delete();
            } else {


                //$inputs = $request->all();
                //pr($inputs);die;
                $image = $request->file('file');
                if(!empty($image)) {
                    $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('images');
                    $image->move($destinationPath, $input['imagename']);
                    $filePath = $destinationPath.'/'.$input['imagename'];
                    $newPath = public_path('images/thumbnail/'.$input['imagename']);
                    //chmod($destinationPath.'/', 0777);
                    //$size = 600;
                    //cropImage($filePath,$newPath,'100');
                   // createThumb($newPath, $filePath, 150, 200);

                }

                $users = $request->input('users');


                // !empty($users) ? $users :  $users = authUser()->id;
                $comment = $request->input('comment');
                $taskId = $request->input('taskId');

                //$userId = $request->input('userId');
                $userId = \Auth::user()->id;

                $comment_id = $request->input('comment_id');
                $insId = '';

                /*  Comment Works  */

                /* remove Comment task functinality Works  */







                $ary = array(

                    'comment' => $comment,
                    'project_task_id' => $taskId,
                    'posted_by_user_id' => $userId,
                    'file'              => !empty($input['imagename']) ? $input['imagename'] : '',
                    'created_at' => date('Y-m-d H:i:s'),
                );

                if (empty($comment_id)) {

                    $insId = (new TaskComments())->insertGetId($ary);
                } else {
                    (new TaskComments())
                        ->where(['task_comment_id' => $comment_id, 'posted_by_user_id' => $userId])
                        ->update($ary);
                    $insId = $comment_id;
                }
                $users = (new User())->where(['id' => $userId])->first(['id', 'name']);
                $getDate = getLocalTimeZone(date('Y-m-d H:i:s'),3);
                if ($insId) {
                    $result = array(
                        'success' => 100,
                        'comment' => $comment,
                        'commentId' => $insId,
                        'user' => $users,
                        'file'  => !empty($input['imagename']) ? $input['imagename'] : '',
                        'created_at' => $getDate,
                    );

                    /***   Email Works   ****/

                    $projectId = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id','subject']);

                    $templateLogInfo = (new TemplateLog())
                        ->where([
                            ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.COMMENT-TASK')],
                            ['type', '=', 1],
                        ])->first();
                    $userName = Auth::user()->name;
                    $loginUserId = Auth::user()->id;
                    $taskTitle = $projectId->subject;
                    $message = $templateLogInfo->message;
                    $userTaskDes = array("#user_first_name#", "#task_name#");
                    $userTaskRep = array($userName, $taskTitle);
                    $tags = $templateLogInfo->tags;
                    $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
                    (new TaskActivity())->insert([
                        'project_id' => $projectId->project_id,
                        'project_task_id' => $taskId,
                        'message' => $newTaskDes,
                        'user_id' => $loginUserId,
                        'created_at' => date('Y-m-d h:i:s')
                    ]);

                    $userDes = (new ProjectTaskAssignees())
                        ->join('users', 'users.id', '=', 'project_task_assignees.user_id')
                        ->where(['project_task_id' => $taskId,'project_task_assignees.deleted_at' => null])
                        ->get(['users.name', 'users.email']);

                    foreach ($userDes as $user){
                        $user->name;
                        $message = $templateLogInfo->message;
                        $userTaskDes = array("#user_first_name#", "#task_name#");
                        $userTaskRep   = array($userName, $taskTitle);
                        $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
                        $messageBody = '<p style="text-align: left;">Hello ' .$user->name.'</p>'.'<p style="text-align: left;">' . $newTaskDes. '.</p>';

                        $finalData [] = [
                            'message_type' => 2,
                            'subject' => $taskTitle,
                            'message_body' => $messageBody,
                            'email' => $user->email

                        ];
                    }
                    (new MessageToBeSend())->insert($finalData);

                } else {
                    $result = array(
                        'success' => 0,
                    );
                }
                echo json_encode($result);
            }

            \DB::commit();
        } catch (Exception $e) {
            \DB::rollback();
        }
    }
    function removeTask(Request $request){

        try {

            \DB::beginTransaction();
            $taskId = $request->input('taskId');
            $userId = Auth::user()->id;
            $role = $request->session()->get('user_role');
            $constantSuperAdmin = \Config::get('constants.ROLE.SUPERADMIN');
            $constantAdmin = \Config::get('constants.ROLE.ADMIN');
            $constantUser = \Config::get('constants.ROLE.USER');
            $projectId = (new ProjectTasks())->where(['project_task_id' => $taskId])->first(['project_id','subject']);

            if(!empty($taskId)){
                $users_id = (new ProjectTaskAssignees())
                    ->join('project_task_logged_time','project_task_logged_time.project_task_id','=','project_task_assignees.project_task_id')
                    ->where(['project_task_logged_time.project_task_id' => $taskId,'project_task_logged_time.end_time' => null])
                    ->first(['project_task_logged_time.logged_by_user_id']);
                //echo $users_id->logged_by_user_id;die;
                if(!empty($users_id->logged_by_user_id)) {
                    if (!empty($taskId)) {
                        $inputs = [
                            'check' => 'false',
                            'new_project_task_id' => '0',
                            'project_task_id' => $taskId,
                            'value' => 'Pause',
                        ];
                        $result = (new ProjectBoardsController())->ajaxEndTaskLoggin($request, $inputs,$users_id->logged_by_user_id);

                    }
                }
            }

            $templateLogInfo = (new TemplateLog())
                ->where([
                    ['action_id', '=', config('constants.FUNCTIONALITY_TYPE.TASK.DELETE')],
                    ['type', '=', 1],
                ])->first();

            $userName = Auth::user()->name;
            $loginUserId = Auth::user()->id;
            $taskTitle = $projectId->subject;
            $message = $templateLogInfo->message;
            $userTaskDes = array("#user_first_name#", "#task_name#");
            $userTaskRep   = array($userName, $taskTitle);
            $tags = $templateLogInfo->tags;
            $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);

            (new TaskActivity())->insert([
                'project_id' => $projectId->project_id,
                'project_task_id' => $taskId,
                'message' => $newTaskDes,
                'user_id'=> $loginUserId,
                'created_at' => date('Y-m-d h:i:s')
            ]);
            $userDes = (new ProjectTaskAssignees())
                ->join('users','users.id','=','project_task_assignees.user_id')
                ->where(['project_task_id' => $taskId])
                ->get(['users.name','users.email']);

            $finalData = [];
            foreach ($userDes as $user){
                $user->name;
                $message = $templateLogInfo->message;
                $userTaskDes = array("#user_first_name#", "#task_name#");
                $userTaskRep   = array($userName, $taskTitle);
                $newTaskDes = str_replace($userTaskDes, $userTaskRep, $message);
                $messageBody = '<p style="text-align: left;">Hello ' .$user->name.'</p>'.'<p style="text-align: left;">' . $newTaskDes. '.</p>';

                $finalData [] = [
                    'message_type' => 2,
                    'subject' => $taskTitle,
                    'message_body' => $messageBody,
                    'email' => $user->email,
                    'created_at' => date('Y-m-d h:i:s')


                ];
            }

            (new MessageToBeSend())->insert($finalData);

            if (isset($taskId)) {

                $res = (new ProjectTasks())->where(['project_task_id' => $taskId])->delete();

                if ($res) {

                    $ary = [

                        'success' => '100',
                    ];
                } else {

                    $ary = [

                        'success' => '0',
                    ];
                }
                echo json_encode($ary);
            }
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollback();

            $array = array(

                'error' => $e->getMessage(),
            );
            echo json_encode($array);
        }
    }

    public function setDateFormat($OldDate){

        return date("d M,h:ia", strtotime($OldDate));
    }


    public function sendTestEmail(){

        Mail::raw('welcome', function ($message){
            $message->to('surya@codelee.com');
        });
    }


    public function getHistoryLog(Request $request){


        $dateFormat =	$request->session()->get('company_setting_date');
        $timeFormat =    $request->session()->get('company_setting_time');
        $timeZoneId =    $request->session()->get('country_timezone_id');
        $timeZone = (new CountriesTimeZone())->where(['countries_timezone_id' => $timeZoneId])->first(['compare_utc']);
        if (empty((array) $timeZone) && !empty($timeZone->compare_utc)) {

            $timeZone = $timeZone->compare_utc;
        }
        $timeZone = $timeZone['compare_utc'];

        DB::select('SET time_zone = "'.$timeZone.'"');
        $dateFormats = \Config::get('constants.GENERAL_SETTING_DATE_FORMAT_MYSQL');
        $timeFormats = \Config::get('constants.GENERAL_SETTING_TIME_FORMAT_MYSQL');
        $finalDateFormat = $dateFormats[$dateFormat];
        $finalTimeFormat = $timeFormats[$timeFormat];
        $format = $finalDateFormat.' '.$finalTimeFormat;

        $inputs = $request->all();
        $getHistoryLogData = (new TaskActivity())
            ->join('users', 'users.id', '=', 'task_activities.user_id')
            ->where(['task_activities.project_task_id' => $inputs['taskId']])
            ->orderBy('task_activities.task_activity_id', 'DESC')
            ->select('users.name', 'task_activities.message',
                'task_activities.created_at',
                DB::raw('DATE_FORMAT(CONVERT_TZ(task_activities.created_at,"+00:00",@@global.time_zone), "'.$format.'") as date'))
            ->get();
        //pr($getHistoryLogData);die;
        return view('js-helper.create-history-log', compact('getHistoryLogData'));
    }
}
