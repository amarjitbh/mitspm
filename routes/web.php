<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|updateUserSettingTable
*/
Route::any('updateUtcTime', 'UserController@updateUtcTime')->name('updateUtcTime');
Route::any('sendScheduleMessage', 'ProjectTaskController@sendEmailReminder')->name('sendEmail');
Route::any('sendEmailTest', 'ProjectTaskController@sendTestEmail')->name('sendEmailTest');

Route::any('setting-data', 'HomeController@updateUserSettingTable')->name('updateUserSettingTable');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/page/error/404', 'HomeController@errorPage404')->name('error-page-404');

Route::get('/page/error/500', 'HomeController@errorPage500')->name('error-page-500');

Route::get('/migrate', function () {
    \Artisan::call('migrate', ["--no-interaction"=>true ]);

});
Route::get('/seed', function () {
    \Artisan::call('db:seed', ["--no-interaction"=>true ]);
});
Route::post('user-register-post', 'UserController@postRegister')->name('user-register-post');
Route::get('task-reporting-system', 'CronjobController@taskReportingSystem')->name('task-reporting-system');
Route::get('send-project-invite-email/{userId}/{projectId?}', 'ProjectController@sendProjectInviteEmail')->name('sendProjectInviteEmail');

Route::group(['middleware' => ['guest']], function () {
    Route::any('userregister/{userId?}/{projectId?}', 'UserController@getRegister')->name('userregister');
    Route::any('login/{userId?}/{projectId?}', 'UserController@getLogin')->name('login');
    Route::post('user-login-post', 'UserController@postLogin')->name('user-login-post');
    Route::post('getTimeZone', 'UserController@getTimeZone')->name('getTimeZone');
    Route::any('sendEmail', 'ProjectController@sendEmail')->name('sendEmail');
});


Route::group(['middleware' => ['auth']], function () {

    Route::any('getUserTaskLogs', 'ProjectTaskController@getUserTaskLogs')->name('getUserTaskLogs');

    Route::any('uploadTaskDoc', 'ProjectTaskController@uploadTaskDoc')->name('uploadTaskDoc');
    Route::any('userLogout', 'UserController@logout')->name('userLogout');
    Route::any('ajax-sort-drag-task-order', 'ProjectBoardsController@ajaxSortDragTaskOrder')->name('ajax-sort-drag-task-order');
    Route::any('ajaxProjectTaskLoggin', 'ProjectBoardsController@ajaxProjectTaskLoggin')->name('ajaxProjectTaskLoggin');
    Route::any('ajax-start-task-loggin', 'ProjectBoardsController@ajaxStartTaskLoggin')->name('ajax-start-task-loggin');
    Route::any('ajax-end-task-loggin', 'ProjectBoardsController@ajaxEndTaskLoggin')->name('ajax-end-task-loggin');

    /*  Route::any('ajax-sort-task', 'ProjectBoardsController@ajaxSortTaskOrder')->name('ajax-sort-task');*/

    /* Route::get('dashboard', 'UserController@UsersDashboard')->name('dashboard');*/

    Route::get('companies', 'UserController@UsersDashboard')->name('companies');
    Route::get('companies-detail', 'CompanyDetailsController@companyDetails')->name('companies-detail');
    Route::get('boards-and-task-project-label/{board_id?}/{uerId?}', 'ProjectBoardsController@getAllBoardsAndTaskProjectLabel')->name('getAllBoardsAndTaskProjectLabel');

    Route::get('user-assigned-task/{bid?}', 'ProjectController@userAssignedTask')->name('user-assigned-task');
    Route::get('/home', 'HomeController@index');
    Route::get('dashboard', 'UserController@UserCompany')->name('dashboard');
    Route::get('project-boards/{project_id}', 'ProjectBoardsController@projectBoardsList')->name('project-boards');
    Route::get('project/assigned-user', 'ProjectController@projectAssigned')->name('assigned-user');
    Route::get('change-password', 'UserController@changePassword')->name('changePassword');
    Route::post('change-password', 'UserController@postChangePassword')->name('postChangePassword');
    Route::post('update-board-title', 'ProjectBoardsController@updateBoardTitle')->name('update-board-title');


    Route::get('search', 'ProjectBoardsController@search')->name('search');

    Route::get('search-task', 'ProjectBoardsController@searchTask')->name('search-task');

    Route::post('history/log', 'ProjectTaskController@getHistoryLog')->name('get_history_log');

    Route::any('board-detail/{board_id?}/{uerId?}', 'ProjectBoardsController@boardDetail')->name('board-detail');

    //Route::get('users-board-tasks/{user_id}/{bid?}', 'ProjectgetHistoryLogBoardsController@userBoardTask')->name('users-board-tasks');


    Route::post('project-boards/{project_id?}', 'ProjectBoardsController@projectBoardsList')->name('project-boards');
    Route::get('define-session/{company_id}', 'UserController@writeSessionRole')->name('write-session');

    /***  Active for project-Admin  ***/
    Route::get('create-board/{project_id}', 'ProjectBoardsController@createBoard')->name('create-board');
    Route::post('add-board', 'ProjectBoardsController@addBoard')->name('add-board');

    /*** Pankhi ***/
    Route::post('add-task', 'ProjectTaskController@addTask')->name('add-task');
    Route::post('remove-add-board-column', 'ProjectBoardsController@removeAddBoardColumn')->name('remove-add-board-column');
    Route::post('ajax-get-column-task-detail', 'ProjectBoardsController@ajaxGetColumnTaskDetail')->name('ajax-get-column-task-detail');

    Route::resource('projects', 'ProjectController');
    Route::any('users-current-task-time', 'CompanyUsersWorkTime@usersCurrentTaskTime')->name('users-current-task-time');

    Route::any('my-tasks/{board_id}', 'ProjectBoardsController@assignedTasks')->name('my-tasks');

    Route::group(['middleware' => 'AdminSuperadmin'], function () {

        Route::any('settings', 'UserController@settings')->name('settings');
        Route::get('create-task/{project_id?}/{board_id?}', 'ProjectTaskController@createTask')->name('create-task');

        Route::get('project-task/task-logged-time', 'ProjectTaskController@taskLoggedTime')->name('task-logged-time'); // not working on this



        Route::get('project-detail/{projectId}', 'ProjectController@projectDetails')->name('project-detail');
        Route::get('users-work-time/{userId?}', 'CompanyUsersWorkTime@index')->name('users-work-time');
        Route::post('ajax-delete-user-invite', 'ProjectController@ajaxDeleteUserInvite')->name('ajax-delete-user-invite');
        Route::post('ajax-delete-user-project', 'ProjectController@ajaxDeleteUserProject')->name('ajax-delete-user-project');
        Route::get('users-current-task', 'CompanyUsersWorkTime@usersCurrentTask')->name('users-current-task');

    });

    Route::group(['middleware' => 'superadmin'], function () {
        Route::post('project/inviteduser', 'ProjectController@inviteduser')->name('invitedUserofProject');
        Route::get('assign-admin', 'UserController@assignProjectAdmin')->name('assign-admin');
        Route::post('assign-admin', 'UserController@assignProjectAdminPost')->name('post-assign-admin');
        Route::any('updateUtcTime', 'UserController@updateUtcTime')->name('updateUtcTime');
         Route::group(['middleware' => 'admin'], function () {

        });
    });

    Route::group(['middleware' => 'user', 'ManageAccess'], function () {

    });

    /*** Amarjit Singh ***/

    Route::post('getBoardColumn', 'ProjectBoardsController@getBoardColumn')->name('getBoardColumn');
    Route::post('getTaskDetail', 'ProjectTaskController@getTaskDetail')->name('getTaskDetail');
    Route::post('updateBoardColumn', 'ProjectBoardsController@updateBoardColumn')->name('updateBoardColumn');
    Route::post('checkColumnData', 'ProjectBoardsController@checkColumnData')->name('checkColumnData');
    Route::post('moveColumnsTask', 'ProjectBoardsController@moveColumnsTask')->name('moveColumnsTask');
    Route::post('updateTask', 'ProjectTaskController@updateTask')->name('updateTask');
    Route::post('taskComments', 'ProjectTaskController@taskComments')->name('taskComments');
    Route::post('removeTask', 'ProjectTaskController@removeTask')->name('removeTask');
    Route::post('getProjectBoards', 'ProjectBoardsController@getProjectBoards')->name('getProjectBoards');
    Route::post('addNewColumn', 'ProjectBoardsController@addNewColumn')->name('addNewColumn');
    Route::post('createLogOnTask', 'ProjectBoardsController@createLogOnTask')->name('createLogOnTask');
    Route::post('ajax-invite-user-for-project', 'ProjectController@addUserInProject')->name('ajax-invite-user-for-project');
    Route::post('change-user-role', 'ProjectController@changeUserRole')->name('change-user-role');

    Route::post('date/time/format', 'CompanyUsersWorkTime@companyDateTimeFormat')->name('date-time-format');

    Route::group(['middleware' => 'softwareAdmin'], function () {


        Route::get('companies-list', 'SoftwareAdminController@index')->name('companies-list');
    });
});
