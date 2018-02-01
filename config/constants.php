<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 11/4/2016
 * Time: 1:23 PM
 */

return [
    'PAGINATION_LIMIT' => 10,

    'BOARD' => [

        'TASK_PAGINATION_LIMIT' => 24,
    ],
    'MESSAGE_TYPE' => [
        'SEND_EMAIL' => 'Project user daily report'
    ],
    'USER_TYPE_FOR_PROJECT'=>[
        '0'=>'MEMBER',
        '1'=>'Project-Admin',
    ],
    'ROLE'=>[
        'SUPERADMIN'=>1,
        'ADMIN'=>2 ,
        'USER'=>3
    ],
    'PROJECT_ADMIN' => [
        'ADMIN' => 1,
    ],
    'BOARD_COLUMNS' => [
        'ICEBOX'                => 'icebox',
        'BACKLOGS'              => 'backlogs',
        'INPROGRESS'            => 'inprogress',
        'COMPLETED'             => 'completed by developer',
        'READY_FOR_UAT'         => 'ready for uat',
        'APPROVED_BY_CLIENT'    => 'approved by client',
        'REJECTED_BY_CLIENT'    => 'rejected by client',
    ],
    'COLUMNS_COLOR_CODE' => [

        '1'         => '#87CEFA',
        '2'         => '#00BFFF',
        '3'         => '#1E90FF',
        '4'         => '#6495ED',
        '5'         => '#7B68EE',
        '6'         => '#6A5ACD',
        '7'         => '#4169E1',
    ],
    'SINGLE_COLUMN_COLOR_CODE' => '#4169E1',
    'SUPER_ADMIN'=>'superadmin@gmail.com',
    'SUPER_ADMIN_DEFAULT_PASSWORD'=>'123456',
    'SOFTWARE_ADMIN'=>'softwareadmin@gmail.com',
    'SOFTWARE_ADMIN_DEFAULT_PASSWORD'=>'123456',
    'DEFINE_ROLE_USER_WHEN_USER_INVITE' => '2',
    'APP_NAME'=>'Project Management',
    'COMPANY_ID'=>1,
    'DATE_FORMAT' => 'M-d-y h:i:s',

    'BOARDNAME' => [
        'MAIN_BOARD_NAME' => 'Main Board',
        'MAIN_BOARD_DESC' => 'Default created board for users',
    ],
    'priority' => [

        '1' => 'Highest',
        '2' => 'High',
        '3' => 'Medium',
        '4' => 'Low',
        '5' => 'Lowest',
    ],
    'priorityClass' => [

        '1' => 'bg-highest',
        '2' => 'bg-high',
        '3' => 'bg-medium',
        '4' => 'bg-low',
        '5' => 'bg-lowest',
    ],
    'TIMER_DATE'=>'Y-m-d H:i:s',
    'DATE_FORMAT'=>'Y-m-d H:i:s',

    'FUNCTIONALITY_TYPE' => [
        'TASK'=>[
            'ADD'=>1,
            'EDIT'=>2,
            'DELETE'=>3,
            'START-TASK'=>4,
            'PRIORITY'=>5,
            'COMMENT-TASK'=>6,
            'STOP-TASK'=>7,
            'MOVE-TASK'=>8,

        ],
    ],

    'GENERAL_SETTING_DATE_FORMAT'=>[
        'Y-m-d',
        'Y/m/d',
        'Y F, j',
        'm-Y-d',
        'm/Y/d',
        'F Y, j',
        'd-m-Y',
        'd/m/Y',
        'j m, Y',
    ],
    'GENERAL_SETTING_DATE_FORMAT_MYSQL'=>[
        'Y-m-d'     => '%Y-%m-%d',
        'Y/m/d'     => '%Y/%m/%d',
        'Y F, j'    => '%Y %M, %e',
        'm-Y-d'     => '%m-%Y-%d',
        'm/Y/d'     => '%m/%Y/%d',
        'F Y, j'     => '%M %Y, %e',
        'd-m-Y'     => '%d-%m-%Y',
        'd/m/Y'     => '%d/%m/%Y',
        'j m, Y'     => '%e %m, %Y',
    ],
    'GENERAL_SETTING_TIME_FORMAT'=>[
        'g:i a',
        'H:i',
    ],
    'GENERAL_SETTING_TIME_FORMAT_MYSQL'=>[

        'g:i a' => '%I:%i %p',
        'H:i'   => '%H:%i',
    ],

    'SETTING_DATE_FORMAT'=>'date-format',
    'SETTING_TIME_FORMAT'=>'time-format'


];
