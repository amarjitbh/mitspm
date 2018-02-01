<?php

namespace App\Http\Controllers;

use App\Companies;
use App\MessageToBeSend;
use Illuminate\Http\Request;

class CronjobController extends Controller
{

    public function taskReportingSystem(Request $request){

        $companyId = $request->session()->get('company_id');
        //$results = (new ProjectTaskAssignees())->taskReports();
        $results = (new Companies())->taskReports($companyId);
        //pr($results);
        $finalResult = '';
        $messageToBeSend = '';
        foreach($results as $result){

            $finalResult[$result['user_email']]['email'] = $result['user_email'];
            $finalResult[$result['user_email']]['company_id'] = $result['company_id'];
            $finalResult[$result['user_email']]['company_name'] = $result['company_name'];
            $finalResult[$result['user_email']]['projects'][$result['project_id']]['project_id']=  $result['project_id'];
            $finalResult[$result['user_email']]['projects'][$result['project_id']]['project_name']=  $result['project_name'];
            foreach($result['project_tasks'] as $tasks){

                $startTime = explode(',', $tasks['start_time']);
                $endTime = explode(',', $tasks['end_time']);

                $finalResult[$result['user_email']]['projects'][$result['project_id']]['users'][$tasks['user_id']]['user_id'] = $tasks['user_id'];
                $finalResult[$result['user_email']]['projects'][$result['project_id']]['users'][$tasks['user_id']]['user'] = $tasks['user_name'];
                $finalResult[$result['user_email']]['projects'][$result['project_id']]['users'][$tasks['user_id']]['tasks'][] = [
                        'project_task_id' => $tasks['project_task_id'],
                        'subject' => $tasks['subject'],
                        'task_time' => getTotalTimes($startTime,$endTime),
                ];
            }
        }

        foreach($finalResult as $final){

            $view = view('email.auto-reporting-system',compact('final'))->render();
            $messageToBeSend[] = [

                    'message_body'  => $view,
                    'email' => $final['email'],
                    'is_send'   => 0,
                    'created_at'    => date('Y-m-d H:i:s'),
            ];
        }
        (new MessageToBeSend())->insert($messageToBeSend);
    }


    public function sendEmailReminder()
    {
        $messages = (new MessageToBeSend())
            ->where('is_send', 0)
            ->get(['message_type', 'subject', 'message_body', 'email', 'message_to_be_send_id']);
        $smsToBeSendId = [];
        foreach($messages as $message) {

            try {
                \Mail::send('email.reminder', array('content' => $message->message_body), function($email) use ($message)
                {
                    $email->to($message->email)->subject($message->subject);
                });
            } catch(\Exception $e) {

            }
            $smsToBeSendId[] = $message->message_to_be_send_id;
        }
        if (count($smsToBeSendId)) {
            (new MessageToBeSend())->whereIn('message_to_be_send_id', $smsToBeSendId)->update(['is_send' => 1]);
        }
    }
}
