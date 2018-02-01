<?php

namespace App\Http\Controllers;

use App\CompanyUser;
use App\MessageToBeSend;
use App\ProjectTaskAssignees;
use App\ProjectTaskLoggedTime;
use App\ProjectTasks;
use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ProjectUsersReport;
use App\CompanySetting;

class CompanyUsersWorkTime extends Controller
{
    function index1(Request $request){

        $inputs = $request->all();
        $dateOne = '';
        $dateTwo = '';
        !empty($inputs['user_id']) ? $userId = $inputs['user_id'] : $userId = '';
        !empty($inputs['dateOne']) ? $inputs['dateOne'] : date('Y-m-d');
        !empty($inputs['dateTwo']) ? $inputs['dateTwo'] : date('Y-m-d');
        if(!empty($inputs['dateOne'])){
            $dateOne = $inputs['dateOne'];
            $dateOne = date("Y-m-d", strtotime($dateOne));
        }
        if(!empty($inputs['dateTwo'])){
            $dateTwo = $inputs['dateTwo'];
            $dateTwo = date("Y-m-d", strtotime($dateTwo));
        }

        $role = $request->session()->get('user_role');//echo $role;die;
        $sesCompanyId = $request->session()->get('company_id');
        $data = '';
        $singleDataData = '';
        $singleData = '';
        if(empty($userId)) {
            $data = (new ProjectTaskAssignees())->getusersWorkTime($sesCompanyId, $dateOne, $dateTwo, $userId);



            if (!empty((array) $data['userTime'])) {

                $userTime = $data['userTime']->toArray();
            }if (!empty((array) $data['result'])) {

                $data = $data['result']->toArray();
            }




        }else{
            $singleData = (new ProjectTaskAssignees())->getSingleUsersWorkTime($sesCompanyId, $dateOne, $dateTwo, $userId);
            if (!empty((array) $singleData)) {

                $singleDataData = $singleData->toArray();
            }
        }
        $logDAta = (new ProjectTaskAssignees())
            ->join('project_task_logged_time', function($join) use($dateOne,$dateTwo) {
                $join->on('project_task_assignees.user_id', '=', 'project_task_logged_time.logged_by_user_id');
                if(!empty($dateOne) && !empty($dateTwo)) {

                    $join->on('project_task_assignees.project_task_id', '=', 'project_task_logged_time.project_task_id');
                    $join->whereDate('project_task_logged_time.start_time','>=',$dateOne);
                    $join->whereDate('project_task_logged_time.start_time','<=',$dateTwo);
                }
            })
            ->where(function($query) use($dateOne,$dateTwo,$userId){
                if(!empty($userId)) {
                    $query->where(['project_task_assignees.user_id' => $userId]);
                }
            })
            ->groupBy('user_id')
            ->orderBy('project_task_assignees.project_task_assigne_id', 'DESC')
            ->get(['user_id',
                DB::Raw('GROUP_CONCAT(DISTINCT project_task_assignees.logging_time) as loggTimesss'),
                DB::Raw('SEC_TO_TIME( SUM(DISTINCT TIME_TO_SEC( project_task_assignees.logging_time ) ) ) AS timeSum'),
            ])->toArray();
        $newDt = '';
        $usersDt = '';
        $ary = '';
        $finalData = [];
        if(!empty($data)) {
            foreach ($data as $ind => $userData) {

                $startTime = explode(',', $userData['start_time']);
                $endTime = explode(',', $userData['end_time']);
                $logged_by_user_id = explode(',', $userData['logged_by_user_id']);
                $finalData[$userData['id']]['user_id'] = $userData['id'];
                $finalData[$userData['id']]['name'] = $userData['name'];
                $finalData[$userData['id']]['usersTaks'][] = [
                    'user_id' => $userData['id'],
                    'task_id' => $userData['project_task_id'],
                    'subject' => $userData['subject'],
                    'dayTotal' => $this->getTotalTime($startTime, $endTime, $finalData[$userData['id']]['user_id'] = $userData['id'], $logged_by_user_id),
                    'logTime' => $userData['logging_time'],
                ];
            }
        }
        if(!empty($singleDataData)){
            foreach ($singleDataData['data'] as $ind => $userData) {

                $startTime = explode(',', $userData['start_time']);
                $endTime = explode(',', $userData['end_time']);
                $logged_by_user_id = explode(',', $userData['logged_by_user_id']);
                $finalData[$userData['id']]['user_id'] = $userData['id'];
                $finalData[$userData['id']]['name'] = $userData['name'];
                $finalData[$userData['id']]['usersTaks'][] = [
                    'user_id' => $userData['id'],
                    'task_id' => $userData['project_task_id'],
                    'subject' => $userData['subject'],
                    'dayTotal' => $this->getTotalTime($startTime, $endTime, $finalData[$userData['id']]['user_id'] = $userData['id'], $logged_by_user_id),
                    'logTime' => $userData['logging_time'],
                ];
            }
        }
        !empty($inputs['user_id']) ? $limit = '' : $limit = '5';

        if(!empty($inputs['dateOne'])){
            $dateOne = $inputs['dateOne'];
            $dateOne = date("F d,Y", strtotime($dateOne));
        }
        if(!empty($inputs['dateTwo'])){
            $dateTwo = $inputs['dateTwo'];
            $dateTwo = date("F d,Y", strtotime($dateTwo));
        }
        return view('users-work-time.users-work-time',compact('finalData','singleData','usersDt','newData','dateOne','dateTwo','userId','role','logDAta'));
    }

    function index(Request $request){
        //echo getLocalTimeZone(date('Y-m-d H:i:s'),2);die;
        $inputs = $request->all();
        $dateOne = '';
        $dateTwo = '';
        !empty($inputs['user_id']) ? $userId = $inputs['user_id'] : $userId = '';
        !empty($inputs['dateOne']) ? $inputs['dateOne'] :$inputs['dateOne'] = date('Y-m-d');
        !empty($inputs['dateTwo']) ? $inputs['dateTwo'] :$inputs['dateTwo'] =  date('Y-m-d');

        if(!empty($inputs['dateOne'])){
            $dateOne = $inputs['dateOne'];
            $dateOne = date("Y-m-d", strtotime($dateOne));
        }
        if(!empty($inputs['dateTwo'])){
            $dateTwo = $inputs['dateTwo'];
            $dateTwo = date("Y-m-d", strtotime($dateTwo));

        }

        $role = $request->session()->get('user_role');//echo $role;die;
        $sesCompanyId = $request->session()->get('company_id');
        $data = '';
        $singleDataData = '';
        $singleData = '';
        $newData = '';
        $data = (new ProjectTaskAssignees())->getusersWorkTime($sesCompanyId, $dateOne, $dateTwo, $userId);


        if (!empty((array) $data['userTime']) && !empty($data['userTime'])) {

            $userTimeData = $data['userTime']->toArray();
        }if (!empty((array) $data['result']) && !empty($data['result'])) {
            $newData = $data['result'];
            $data = $data['result']->toArray();

        }
        /*pr($userTimeData);
        pr($data);die;*/
        $newDt = '';
        $usersDt = '';


        $finalTime = [];


        if(!empty($userTimeData)) {
            foreach ($userTimeData as $ind => $userTime) {

                $startTime = explode(',', $userTime['start_time']);
                $endTime = explode(',', $userTime['end_time']);
                $finalTime[$userTime['id']]['user_id'] = $userTime['id'];
                $finalTime[$userTime['id']]['userTime'] =  $this->getTotalTime($startTime, $endTime);
                $finalTime[$userTime['id']]['timeSum'] = !empty($userTime['timeSum'])? $userTime['timeSum'] : '';

            }
        }
        //pr($finalTime);
        //pr($data);die;
        $ary = '';
        $finalData = [];
        if(!empty($data['data']) && !empty($inputs['user_id'])) {
            foreach ($data['data'] as $ind => $userData) {

                $startTime = explode(',', $userData['start_time']);
                $endTime = explode(',', $userData['end_time']);
                $logged_by_user_id = explode(',', $userData['logged_by_user_id']);
                $finalData[$userData['id']]['user_id'] = $userData['id'];
                $finalData[$userData['id']]['name'] = $userData['name'];
                $finalData[$userData['id']]['usersTaks'][] = [
                    'user_id' => $userData['id'],
                    'task_id' => $userData['project_task_id'],
                    'subject' => $userData['subject'],
                    'dayTotal' => $this->getTotalTime($startTime, $endTime, $finalData[$userData['id']]['user_id'] = $userData['id'], $logged_by_user_id),
                    'logTime' => $userData['logging_time'],
                ];
            }
        }else{
            if(!empty($data) && empty($inputs['user_id'])) {
                foreach ($data as $ind => $userData) {
                    if(!empty($userData)) {

                        $startTime = explode(',', $userData['start_time']);
                        $endTime = explode(',', $userData['end_time']);
                        $logged_by_user_id = explode(',', $userData['logged_by_user_id']);
                        $finalData[$userData['id']]['user_id'] = $userData['id'];
                        $finalData[$userData['id']]['name'] = $userData['name'];
                        $finalData[$userData['id']]['usersTaks'][] = [
                            'user_id' => $userData['id'],
                            'task_id' => $userData['project_task_id'],
                            'subject' => $userData['subject'],
                            'dayTotal' => $this->getTotalTime($startTime, $endTime, $finalData[$userData['id']]['user_id'] = $userData['id'], $logged_by_user_id),
                            'logTime' => $userData['logging_time'],
                        ];
                    }
                }
            }
        }
        !empty($inputs['user_id']) ? $limit = '' : $limit = '5';

        if(!empty($inputs['dateOne'])){
            $dateOne = $inputs['dateOne'];
            $dateOne = date("F d,Y", strtotime($dateOne));
        }
        if(!empty($inputs['dateTwo'])){
            $dateTwo = $inputs['dateTwo'];
            $dateTwo = date("F d,Y", strtotime($dateTwo));
        }
        //pr($finalTime);
        //pr($finalData);die;
        return view('users-work-time.users-work-time',compact('finalData','usersDt','finalTime','newData','dateOne','dateTwo','userId','role','logDAta'));
    }
    function getTotalTime($startTime,$endTime,$mainUserId=null,$userId=null){

        $datetime2 = '';
        $dteDiff = '';
        $newTimes = '';
        foreach($startTime as $id => $st){
            if(!empty($mainUserId) && !empty($userId[$id])) {
                if ($mainUserId == $userId[$id]) {
                    if (!empty($endTime[$id])) {
                        //echo $startTime[$id].'=='.$endTime[$id].'<br />';
                        $datetime1 = new \DateTime($startTime[$id]);
                        $datetime2 = new \DateTime($endTime[$id]);
                        $dteDiff = $datetime1->diff($datetime2);
                        $newTimes[] = $dteDiff->format("%H:%I:%S");
                    }
                }
            }else{

                if (!empty($endTime[$id])) {
                    //echo $startTime[$id].'=='.$endTime[$id].'<br />';
                    $datetime1 = new \DateTime($startTime[$id]);
                    $datetime2 = new \DateTime($endTime[$id]);
                    $dteDiff = $datetime1->diff($datetime2);
                    $newTimes[] = $dteDiff->format("%H:%I:%S");
                }
            }
        }
        $total = '';
        if(!empty($newTimes)) {
            foreach ($newTimes as $t) {

                if (!empty($t)) {
                    $total += toSeconds($t);
                }
            }
            return toTime($total);
        }
    }


    function usersCurrentTask(Request $request){

        $role = $request->session()->get('user_role');//echo $role;die;
        $sesCompanyId = $request->session()->get('company_id');
        $result = (new CompanyUser())->getCurrentTaskData($sesCompanyId);
        if (!empty((array) $result)) {

            $data = $result->toArray();
        }else{
            $data = [];
        }
        return view('users-work-time.users-current-task',compact('data'));
    }

    public function usersCurrentTaskTime(Request $request){



        $inputs =  $request->all();
        $reportsDate = isset($inputs['date']) ? $inputs['date'] : date('Y-m-d');
        $projectId = isset($inputs['project_id']) ? $inputs['project_id'] : '';
        $user_id = \Auth::user()->id;
        $sesCompanyId = $request->session()->get('company_id');

        $todayTasks= (new ProjectTasks())
                ->join('project_task_assignees','project_task_assignees.project_task_id', '=', 'project_tasks.project_task_id')
                ->join('company_users','company_users.user_id', '=', 'project_task_assignees.user_id')
                ->join('project_task_logged_time','project_task_logged_time.project_task_id', '=', 'project_tasks.project_task_id')
                ->join('projects','projects.project_id', '=', 'project_tasks.project_id')
                ->select(DB::raw('group_concat(project_task_logged_time.start_time) as start_time'),
                    DB::Raw('GROUP_CONCAT(DISTINCT case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
                    'project_tasks.project_task_id','project_tasks.subject','project_task_assignees.user_id as user_id',
                    'project_task_assignees.logging_time','company_users.role','projects.name', 'projects.project_id','project_tasks.project_task_id','project_task_logged_time.logged_by_user_id','projects.company_id')
                ->where('project_task_assignees.user_id', $user_id)
                ->where('project_task_logged_time.logged_by_user_id', $user_id)
                ->where('projects.company_id', $sesCompanyId)
                ->where(function($query) use ($projectId) {
                    if ($projectId != '') {
                        $query->where('projects.project_id', $projectId);
                    }
                })
                ->whereDate('project_task_logged_time.start_time', '=', $reportsDate)
                ->groupBy('project_tasks.project_task_id')
                ->orderBy('projects.project_id')
                ->get();
        //pr($todayTasks->toArray());
        //die;
        $final = [];
        foreach($todayTasks as $todayTask) {

            $startTime = explode(',',$todayTask['start_time']);
            $endTime   = explode(',',$todayTask['end_time']);
            $final[$todayTask['project_id']]['project_name'] = $todayTask['name'];
            $final[$todayTask['project_id']]['project_id'] = $todayTask['project_id'];
            $final[$todayTask['project_id']]['title'] = 'Functionality';
            $final[$todayTask['project_id']]['time'] = 'Time';
            $totalTime = getTotalTimes($startTime,$endTime);

            $final[$todayTask['project_id']]['total_task_time'] = $totalTime;

            $final[$todayTask['project_id']]['tasks'][] = [
                'task_name' => $todayTask['subject'],
                'total_time' => $totalTime,
                'task_id' => $todayTask['project_task_id'],
                'user_id' => $todayTask['user_id'],
            ];
        }
        if(isset($inputs['action']) && $inputs['action'] == 'action-send-report'){


            $projectUsersReports =  (new ProjectUsersReport())->insert([

                'project_id' => $inputs['project_id'],
                'user_id'=> $user_id,
                'report_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d h:i:s')
            ]);

            if($projectUsersReports != ''){

                $projectUserEmail = \DB::table('user_settings')
                    ->where(['company_id' => $sesCompanyId,'project_id' => $inputs['project_id'],'report' => '1'])
                    ->first(['email']);

                $comments = isset($inputs['comment']) ? $inputs['comment'] : '';
                $view = view('users-work-time.user-send-report-email',compact('todayTasks','final','comments'));
                $contents = $view->render();

                (new MessageToBeSend())->insert([

                    'message_type' => 2,
                    'subject' => \Config::get('constants.MESSAGE_TYPE.SEND_EMAIL'),
                    'message_body' => $view,
                    'email' => $projectUserEmail->email,
                    'is_send' => '1',
                    'created_at' => date('Y-m-d :h:i:s')
                ]);

                return ['success' => true];

            }

        }
        return view('users-work-time.user-send-report',compact('todayTasks','final'));

    }

//    public  function companyDateTimeFormat(Request $request){
//
//        $inputs = $request->all();
//        $dateFormate = isset($inputs['date']) ? $inputs['date'] :'';
//        $timeFormate = isset($inputs['time']) ? $inputs['time'] :'';
//        $sesCompanyId = $request->session()->get('company_id');
//        (new CompanySetting())->insert([
//            'company_id' => $sesCompanyId,
//            'meta_key' => \Config::get('constants.SETTING_DATE_FORMAT'),
//            'meta_value' => $dateFormate,
//            'created_at' => date('Y-m-d :h:i:s')
//        ]);
//        (new CompanySetting())->insert([
//            'company_id' => $sesCompanyId,
//            'meta_key' => \Config::get('constants.SETTING_TIME_FORMAT'),
//            'meta_value' => $timeFormate,
//            'created_at' => date('Y-m-d :h:i:s')
//        ]);
//
//        return ['success' => true,'date' => $dateFormate, 'time' => $timeFormate];
//
//
//    }


}
