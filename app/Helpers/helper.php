<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29/11/16
 * Time: 5:48 PM
 */

function authUser()
{
    return Auth::user();
}
function userRole(){

    return \Session::get('user_role');

}
function pr($data, $exit = false)
{
    echo "<pre>";
    print_r($data);
    if ($exit) {
        die;
    }
}

function dateFormat()
{
    return \Config::get('constants.DATE_FORMAT');
}
function timerDateFormat()
{
    return \Config::get('constants.TIMER_DATE');
}

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int)($log / 8) + 1; // length in bytes
    $bits = (int)$log + 1; // length in bits
    $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];
    }

    return $token;
}

function createToken($userId)
{
    return sha1(uniqid($userId . time(), true)) . sha1(uniqid($userId . time(), true));
}

function loggedTaskOfUser($projectTaskId)
{
    $userId = Auth::user()->id;
    return (new \App\ProjectTaskLoggedTime())
        ->where(['logged_by_user_id' => $userId, 'end_time' => null, 'project_task_id' => $projectTaskId])
        ->orderBy('project_task_logged_time_id', 'desc')
        ->first(['*']);
}


function calculateTimeTakenForTAsk($projectTaskId)
{
    $userId = Auth::user()->id;
    $result = (new \App\ProjectTaskAssignees())->fetchAllLoggedTime($projectTaskId, $userId);

    return $result;
}

//function will calculate no of days, hours & seconds
function calculateNoOfdays($totalSeconds)
{

    $mins = $totalSeconds / 60;
    if ($mins < 1) {
        $showing = '00:00:' . $totalSeconds;

    } else {
        $minsfinal = floor($mins);
        $secondsfinal = $totalSeconds - ($minsfinal * 60);
        $hours = $minsfinal / 60;
        $minsfinal = strlen($minsfinal) == 1 ? '0' . $minsfinal : $minsfinal;
        $secondsfinal = strlen($secondsfinal) == 1 ? '0' . $secondsfinal : $secondsfinal;
        if ($hours < 1) {
            $showing = '00:' . $minsfinal . ":" . $secondsfinal;

        } else {

            $hoursfinal = floor($hours);
            $minssuperfinal = $minsfinal - ($hoursfinal * 60);

            $hoursfinal = strlen($hoursfinal) == 1 ? '0' . $hoursfinal : $hoursfinal;

            $minssuperfinal = strlen($minssuperfinal) == 1 ? '0' . $minssuperfinal : $minssuperfinal;

            /* $days = $hoursfinal / 24;
             if ($days < 1) {
                 $showing =   "0 day(s): " .$hoursfinal . ":" . $minssuperfinal . ":" . $secondsfinal ;
             } else {*/
            //  $daysfinal = floor($days);
            //$hourssuperfinal = $hoursfinal - ($daysfinal * 24);
            $showing = $hoursfinal . ":" . $minssuperfinal . ":" . $secondsfinal;
            /*}*/
        }
    }

    return $showing;
}

//delete if nolonger using
function calculateTotalTimeTaken($projectTaskId)
{
    $userId = Auth::user()->id;
    $result = (new \App\ProjectTaskAssignees())->fetchAllLoggedTime($projectTaskId, $userId);
    return $result;
}


function findTotalTimeTaken($projectTaskId, $userId = null)
{
    if ($userId) {
        $userId = $userId;
    } else {
        $userId = Auth::user()->id;
    }

    $data = (new \App\ProjectTaskLoggedTime())->fetchTaskDuration($projectTaskId, $userId);
    if (count($data) > 0) {
        $totalSeconds = 0;
        foreach ($data as $dateResult) {
            $previousTimeStamp = strtotime($dateResult['start_time']);
            if ($dateResult['end_time'] == null) {
                $lastTimeStamp = strtotime(date(timerDateFormat()));
            } else {
                $lastTimeStamp = strtotime($dateResult['end_time']);
            }
            $totalSeconds += $lastTimeStamp - $previousTimeStamp;

        }

        /*  if ($totalSeconds > 0) {
              $totalSeconds = $totalSeconds;
          } else {
              $totalSeconds = 0;
          }*/
        $totalDuration = calculateNoOfdays($totalSeconds); //fxn created in helper.php
        // $CalculatedDate['DateFormat'] =  secondsToTime($totalSeconds);
        $CalculatedDate['NoOfDays'] = $totalDuration;
    } else {

        //  $CalculatedDate['DateFormat'] = '00:00:00';
        $CalculatedDate['NoOfDays'] = '00:00:00';
    }
    return $CalculatedDate;

}

/***  Admin Helper Functions --> By Amarjit Singh ***/

function adminRoute()
{

    return array(

        'create-board',
        'add-board',
        'create-task',
        'add-task',
        'task-logged-time',
        'invitedUserofProject',
        'post-assign-admin',
        'sendProjectInviteEmail',
        'updateTask', /*** For both auth ****/
        'getTaskDetail',
        'ajaxProjectTaskLoggin',
        'ajax-start-task-loggin',
        'ajax-end-task-loggin',
        'ajax-livetimer',
        'dashboard',
        'user-assigned-task',
        'user-company',
        'project-boards',
        'assigned-user',
        'changePassword',
        'postChangePassword',
        'board-detail',
        'users-board-tasks',
    );
}

function usersRoute()
{
    return array(

        'getTaskDetail',
        'ajaxProjectTaskLoggin',
        'ajax-start-task-loggin',
        'ajax-end-task-loggin',
        'ajax-livetimer',
        'dashboard',
        'user-assigned-task',
        'user-company',
        'project-boards',
        'assigned-user',
        'changePassword',
        'postChangePassword',
        'board-detail',
    );
}


function myRoutes()
{
    if (session('user_role') == \Config::get('constants.ROLE.SUPERADMIN')) {
        return adminRoute();
    } else if (session('user_role') == \Config::get('constants.ROLE.ADMIN')) {
        return adminRoute();
    } else if (session('user_role') == \Config::get('constants.ROLE.USER')) {
        return usersRoute();
    }

}
//getLocalTimeZone('sdf',3);
function getLocalTimeZone($utcDate, $dateTimeType)
{
    $dateFormat =	Session::get('company_setting_date');
    $timeFormat =    Session::get('company_setting_time');

    if($dateTimeType == 1){
      $dateTimeFormat =  $dateFormat;
    }
    else if($dateTimeType == 2){
        $dateTimeFormat = $timeFormat;
    }
    else{
        $dateTimeFormat =  $dateFormat.' '.$timeFormat;
    }

    $currentTimezone =  Session::get('country_timezone_id') ?  Session::get('country_timezone_id') : date_default_timezone_get();

    $dateTimeTypeFormat = $dateTimeFormat;
    $timeZone = (new \App\CountriesTimeZone())->where(['countries_timezone_id'=> $currentTimezone])->first(['timezone']);
    if (!empty($timeZone['timezone'])) {
        $date = new \DateTime($utcDate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timeZone['timezone']));
        return $date->format($dateTimeTypeFormat);
    } else {
        $date = new \DateTime($utcDate, new DateTimeZone('UTC'));
        return $date->format($dateTimeTypeFormat);
    }
}

function changeDateTimeFormat($utcDate, $dateTimeType){
    $dateFormat =	Session::get('company_setting_date');
    $timeFormat =    Session::get('company_setting_time');

    if($dateTimeType == 1){
        $dateTimeFormat =  $dateFormat;
    }
    else if($dateTimeType == 2){
        $dateTimeFormat = $timeFormat;
    }
    else{
        $dateTimeFormat =  $dateFormat.' '.$timeFormat;
    }

    $dateTimeTypeFormat = $dateTimeFormat;

    $date = new \DateTime($utcDate);
    return $date->format($dateTimeTypeFormat);

}

function ColumnName($boardId)
{
    $userId = Auth::user()->id;
    $data = (new \App\ProjectBoards())
        ->leftjoin('project_board_column', 'project_board_column.project_board_id', '=', 'project_boards.project_board_id')
        ->leftjoin('user_project_columns', 'user_project_columns.project_board_column_id', '=', 'project_board_column.project_board_column_id')
        ->where(['project_boards.project_board_id' => $boardId])
        ->where(['user_project_columns.user_id' => $userId])
        ->where(['user_project_columns.deleted_at' => null])
        ->get(['project_board_column.project_board_column_id'])
        ->toArray();

    return array_column($data, 'project_board_column_id');
}


function fetchColumnsName($boardId)
{
    $userId = Auth::user()->id;
    return (new \App\ProjectBoardColumn())
        ->where(['project_board_id' => $boardId])
        ->get(['project_board_column_id', 'column_name', 'project_board_id'])
        ->toArray();
}

function toSeconds($time) {
    //pr($time);die;
    $parts = explode(':', $time);
    return 3600*$parts[0] + 60*$parts[1] + $parts[2];
}

function toTime($seconds) {

    /* $hours = floor($seconds/3600);
     $seconds -= $hours * 3600;
     $minutes = floor($seconds/60);
     $seconds -= $minutes * 60;
     return $hours . ':' . $minutes . ':' . $seconds;*/
    return gmdate("H:i:s", $seconds);
}

function getTotalTime($startTime,$endTime){

    $datetime2 = '';
    $dteDiff = '';
    $newTimes = '';
    $datetime1 = new \DateTime($startTime);
    $datetime2 = new \DateTime($endTime);
    $dteDiff = $datetime1->diff($datetime2);
    $newTimes[] = $dteDiff->format("%H:%i:%s");

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


function getTotalTimes($startTime,$endTime){

    $datetime2 = '';
    $dteDiff = '';
    $newTimes = '';
    foreach($startTime as $id => $st){

        if (!empty($endTime[$id])) {

            $datetime1 = new \DateTime($startTime[$id]);
            $datetime2 = new \DateTime($endTime[$id]);
            $dteDiff = $datetime1->diff($datetime2);
            $newTimes[] = $dteDiff->format("%H:%i:%s");
        }
    }
    $total = '';
    if(!empty($newTimes)) {
        foreach ($newTimes as $t) {
            //pr($t);die;
            if (!empty($t)) {
                $total += toSeconds($t);
            }
        }
        return toTime($total);
    }
}

function isActiveRoute($route, $output = 'active'){

    if (Route::currentRouteName() == $route) {
        return $output;
    }
}

function createThumb($name, $filename, $new_w, $new_h)
{

    $found = 0;
    $system = explode('.', $name);

    $echeck = strtolower(end($system));

    if (preg_match('/jpg|jpeg/', $echeck)) {
        $src_img = imagecreatefromjpeg($name);
        $found = 1;
    }

    if (preg_match('/png/', $echeck)) {

        $src_img = imagecreatefrompng($name);

        $found = 1;
    }

    if (preg_match('/gif/', $echeck)) {
        $src_img = imagecreatefromgif($name);
        $found = 1;
    }

    if ($found) {

        $old_x = imagesx($src_img);
        $old_y = imagesy($src_img);
        $ar = $old_x / $old_y;

        if ($old_x > 400) {
            if ($new_w == $new_h) {
                $thumb_w = $new_w;
                $thumb_h = $new_h;
            } else {
                $thumb_w = $new_w;
                $thumb_h = (int)(($old_y / $old_x) * $new_w);
            }
        } else {
            $thumb_w = $old_x;
            $thumb_h = $old_y;
        }

        $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

        if (preg_match("/png/", $echeck)) {
            imagepng($dst_img, $filename);
        } else if (preg_match('/jpg|jpeg/png', $echeck)) {
            imagejpeg($dst_img, $filename, 100);
        } else if (preg_match("/gif/", $echeck)) {
            imagegif($dst_img, $filename);
        }
        imagedestroy($dst_img);
    }

    imagedestroy($src_img);

}

function cropImage($filename, $name, $size)
{
    list($width, $height) = getimagesize($filename);
    $offset_x = 0;
    $offset_y = 0;
    $new_height = $height / 50;
    $new_width = $width / 50;
    $image = imagecreatefromjpeg($filename);
    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopy($new_image, $image, 0, 0, $offset_x, $offset_y, $width, $height);
    imagejpeg($new_image, $name, 100);
    imagedestroy($new_image);
}


 function getCompanyUserData()
{
    $result = DB::table('company_users')
        ->leftJoin('companies', 'companies.company_id', '=', 'company_users.company_id')
        ->where(['user_id' => \Auth::user()->id])
        ->groupBy('companies.company_id')
        ->get(['companies.company_id'])->toArray();

    return $result;
}

function getCompanyName(){

    $cid = session()->get('company_id');
    $company = DB::table('companies')->where(['company_id' => $cid])->first(['name']);
    return $company->name;

}


function convertintoUTC($utcDate, $dateTimeType)
{

    $dateFormat = 'Y-m-d';
    $timeFormat ='H:i:s';
    if ($dateTimeType == 1) { //date
        $dateTimeFormat = $dateFormat;
    } else if ($dateTimeType == 2) { //time
        $dateTimeFormat = $timeFormat;
    } else {
        $dateTimeFormat = $dateFormat . ' ' . $timeFormat;
    }
    $dateTimeTypeFormat = $dateTimeFormat;
    $date = new \DateTime($utcDate, new DateTimeZone('asia/kolkata'));
    $date->setTimezone(new DateTimeZone('UTC'));
    return $date->format($dateTimeTypeFormat);
}
