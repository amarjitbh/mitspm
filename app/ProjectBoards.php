<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectBoards extends Model
{

    public $columns = [];
    protected $table = 'project_boards';

    protected $primaryKey = 'project_board_id';

    function getBoardsWithColumns($proId)
    {

        return $this->with(['boardColumnNames' => function ($query) {

            $query->select(['project_board_id', 'column_name', 'created_by_user_id', 'project_board_column_id']);
        }])->where(['project_id' => $proId])->get();

    }
    function getBoards($proId)
    {

        return $this->where(['project_id' => $proId])->first();

    }
    function singleBoardWithColumns($bId)
    {
        $userId = Auth::user()->id;
        /***  $bid -> board Ids  ***/
        return $this
            ->join('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->with(['boardColumnNames' => function ($query) {
                $query->select(['project_board_id', 'column_name', 'created_by_user_id', 'project_board_column_id']);
            }])
            ->with(['ProjectsTasks' => function ($qry) {
                $qry->select(['project_task_id', 'subject', 'project_board_id', 'project_board_column_id', 'task_order_id']);
            }])
            ->where(['project_board_id' => $bId])
            ->first();
    }

    function singleBoardWithColumnsforNormalUser($bId, $uid)
    {
        //echo $bId.'='.$uid;die;
        /***  $bid -> board Ids  ***/
        return $this
            ->leftjoin('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->leftjoin('project_board_column', 'project_board_column.project_board_id', '=', 'project_boards.project_board_id')
            ->leftjoin('project_tasks', 'project_tasks.project_board_column_id', '=', 'project_board_column.project_board_column_id')
            ->leftjoin('project_task_assignees', 'project_task_assignees.project_task_id', '=', 'project_tasks.project_task_id')
            ->where(['project_boards.project_board_id' => $bId, 'project_task_assignees.user_id' => $uid])
            ->first();
    }

    function boardColumns()
    {

        return $this->hasMany(ProjectBoardColumn::class, 'project_board_id')->whereIn('project_board_column_id', $this->columns);
    }

    function ProjectsTasks()
    {

        return $this->hasMany(ProjectTasks::class, 'project_board_id')->orderBy('task_order_id', 'asc')->groupBy('project_task_id');
    }

    function checkforaccess($bid)
    {
        return $this
            ->join('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->join('companies', 'companies.company_id', '=', 'projects.company_id')
            ->where(['project_boards.project_board_id' => $bid])
            ->first(['companies.company_id']);
    }

    function fetchColumnsName($boardID)
    {
        $data = $this
            ->join('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->with(['boardColumns' => function ($query) {
                $query->select(['project_board_id', 'column_name', 'created_by_user_id', 'project_board_column_id']);
            }])
            ->where(['project_board_id' => $boardID])
            ->first()
            ->toArray();
        return $data;
    }

    function  fetchUserProjectColumn($boardID, $userId, $role , $simpleRole = null,$projectAdmin,$checkId=null,$column_id=null,$offset=null)
    {
        $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');

        $data = DB::select("select t1.project_board_column_id,t1.project_board_id as board_id,
            Substring_index(Group_concat(DISTINCT t1.project_task_id ORDER BY
                         t1.project_task_id
                         desc
            SEPARATOR ','), ',', " . $limit." ) as project_task_id
            from project_tasks t1
            inner join (select GROUP_CONCAT(project_task_id) pro_id,project_board_id
            from project_tasks
            where project_tasks.project_board_id = " . $boardID . "
             group by project_board_column_id
             ORDER BY project_board_column_id ASC
             ) t2
            on t1.project_board_id = t2.project_board_id
            group by t1.project_board_column_id
            ");
        $ary = '';
        foreach ($data as $dt){
            $ary [] =[
                'project_board_column_id' => $dt->project_board_column_id,
                'board_id' => $dt->board_id,
                'project_task_id' => $dt->project_task_id,
            ];
        }
        if(empty($ary)){
            $ary =[
                'project_board_column_id' => '',
                'board_id' => '',
                'project_task_id' => '',
            ];
        }
        $project_task_id = array_column($ary, 'project_task_id');
        $project_task_id = explode(',', implode($project_task_id, ','));
        //pr($project_task_id);die;
        //pr($data);die;
        //dd(DB::getQueryLog());
        $data = $this
            ->CommonCondition()
            ->where(['project_boards.project_board_id' => $boardID])
            ->where(function ($query) use ($role, $userId,$checkId,$column_id) {
                if (!empty($column_id)) {

                    $query->where(['user_project_columns.project_board_column_id' => $column_id]);
                }
                $query->where(['user_project_columns.user_id' => $userId]);
                $query->where(['user_project_columns.user_id' => $userId]);
            })
            ->where(['user_project_columns.deleted_at' => null])
            ->get(['project_board_column.project_board_column_id'])
            ->toArray();
        $column = array_column($data, 'project_board_column_id');
        $this->columns = $column;
        $result = $this
            ->CommonCondition()
            ->whereIn('project_board_column.project_board_column_id', $column)
            ->with('boardColumns')
            ->with(['ProjectsTasks' => function($q) use($userId,$role,$simpleRole,$projectAdmin,$checkId,$project_task_id) {
                $q
                    ->where(function($query)use($project_task_id){

                        $query->whereIn('project_tasks.project_task_id', $project_task_id);
                    })
                    ->join('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
                    ->leftjoin('project_task_logged_time','project_task_logged_time.project_task_id','=','project_tasks.project_task_id')
                    ->select(['project_task_assignees.user_id','project_tasks.project_task_id' ,'project_tasks.subject', 'project_tasks.project_board_id',
                        'project_tasks.project_board_column_id', 'project_tasks.task_order_id',
                        'project_tasks.priority','project_task_assignees.deleted_at',
                        DB::Raw('GROUP_CONCAT(DISTINCT project_task_logged_time.start_time ) as start_time'),
                        DB::Raw('GROUP_CONCAT(DISTINCT case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
                    ])
                    ->where(function ($query) use ($role, $userId,$simpleRole,$projectAdmin,$checkId) {

                        if($role == 3 && $projectAdmin == '') {
                            $query->where(['project_task_assignees.deleted_at' => null]);
                        }
                        if(!empty($checkId)){

                            $query->where(['project_task_assignees.user_id' => $checkId,'project_task_assignees.deleted_at' => null]);
                        }else if(!empty($simpleRole) && $projectAdmin == false && $role == \Config::get('constants.ROLE.USER')){

                            //$query->where(['project_task_assignees.user_id' => $userId]);
                        }else{

                            $query->groupBy('user_id');
                        }
                    });
                $q->orderBy('project_tasks.project_task_id','ASC');
            }
            ])
            ->join('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->first();
        return $result;
        //pr($result->toArray());die;
    }

    function fetchUserProjectColumnTask($boardID, $userId, $role , $simpleRole = null,$projectAdmin,$checkId=null,$column_id=null,$offset=null,$mytask=null){

        $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');

        $ProjectsTasks = (new ProjectTasks())
            ->leftjoin('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
            ->leftjoin('project_task_logged_time','project_task_logged_time.project_task_id','=','project_tasks.project_task_id')
            ->select(['project_task_assignees.user_id','project_tasks.project_task_id' ,'project_tasks.subject',
                'project_tasks.project_board_id', 'project_tasks.project_board_column_id',
                'project_tasks.task_order_id', 'project_tasks.priority',
                'project_task_assignees.deleted_at',
                DB::Raw('GROUP_CONCAT(DISTINCT project_task_logged_time.start_time ) as start_time'),
                DB::Raw('GROUP_CONCAT(DISTINCT case when project_task_logged_time.end_time IS NULL then CONVERT_TZ(NOW(), @@session.time_zone, "+00:00") else project_task_logged_time.end_time end ) as end_time'),
            ])
            ->where(function ($query) use ($role, $userId,$simpleRole,$projectAdmin,$checkId,$mytask) {

                if($role == 3 && $projectAdmin == '') {
                    $query->where(['project_task_assignees.deleted_at' => null]);
                }
                if(!empty($checkId)){

                    $query->where(['project_task_assignees.user_id' => $checkId,'project_task_assignees.deleted_at' => null]);
                }else if(!empty($simpleRole) && $projectAdmin == false && $role == \Config::get('constants.ROLE.USER')){

                }else{

                    $query->groupBy('user_id');
                }
            });
        if(!empty($mytask)){

            /*  $ProjectsTasks = $ProjectsTasks->join('user_project_columns',function($join) use($userId){
                  $join->on('user_project_columns.project_board_column_id','=','project_tasks.project_board_column_id');
              });
              $ProjectsTasks = $ProjectsTasks->where(['user_project_columns.user_id' => $userId])*/
            $ProjectsTasks = $ProjectsTasks->where(['project_task_assignees.user_id' => $userId])
                ->where(['project_task_assignees.deleted_at' => null]);
        }
        $ProjectsTasks = $ProjectsTasks->where(['project_tasks.project_board_id' => $boardID,'project_tasks.project_board_column_id' => $column_id])
            ->skip($offset)
            ->take($limit)
            ->orderBy('project_tasks.project_task_id','DESC')
            ->groupBy('project_tasks.project_task_id')
            ->get();
        return $ProjectsTasks;

    }

    public  function fetchUserProjectBoardAndTask($projectId,$offset = null,$boardId = null,$columnId = null,$limit=null){

        //pr($projectId);die;
        //pr($offset);
        //die;
        $userId = Auth::user()->id;
        $projectTaskId = '';
        $project_board_id = [];//pr($offset);die;
        if(empty($offset)) {
            $where = $boardId > 0 ? 'where t1.project_board_id='.$boardId : '';
            $where1 =  empty($boardId)? 'where t1.project_id='.$projectId : '';
            //echo $where;die;
            $limit = \Config::get('constants.BOARD.TASK_PAGINATION_LIMIT');

            $data = DB::select("select t1.project_board_column_id,t1.project_board_id as board_id,
            Substring_index(Group_concat(DISTINCT t1.project_task_id ORDER BY
                         t1.project_board_column_id ASC,t1.project_task_id DESC
            SEPARATOR ','), ',', " . $limit . " ) as project_task_id
            from project_tasks t1
            inner join (select GROUP_CONCAT(project_task_id) pro_id,project_board_id
            from project_tasks
             group by project_board_id
             ) t2
            on t1.project_board_id = t2.project_board_id
            inner join project_task_assignees on t1.project_task_id = project_task_assignees.project_task_id
$where
$where1
            AND project_task_assignees.user_id = '".$userId."'
            AND project_task_assignees.deleted_at IS NULL
            AND t1.deleted_at IS NULL
            group by t1.project_board_id
            ");
           //pr($data);die;
            $ary = '';
            foreach ($data as $dt) {
                $ary [] = [
                    'board_id' => $dt->board_id,
                    'project_task_id' => $dt->project_task_id,
                ];
            }
            if (empty($ary)) {
                $ary = [
                    'board_id' => '',
                    'project_task_id' => '',
                ];
            }
            $project_task_id = array_column($ary, 'project_task_id');
            $project_board_id = array_column($ary, 'board_id');
            $project_task_id = explode(',', implode($project_task_id, ','));
            $projectTaskId =  isset($project_task_id) ? $project_task_id : '';
        }
        if(isset($columnId) && $columnId != ''){
            $projectTaskId = '';
        }
        $result = '';
        //pr($projectTaskId,1);
        $result = $this->with(['projectBoardsAll' => function ($q)use($projectTaskId,$userId,$boardId,$columnId,$offset,$limit) {

            $q


                ->join('project_board_column', 'project_board_column.project_board_column_id','=', 'project_tasks.project_board_column_id')
                ->leftjoin('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id')
                ->where('project_board_column.deleted_at', NULL)
                ->where('project_task_assignees.deleted_at', NULL)
                ->where('project_task_assignees.user_id', $userId)
                ->where(function($query) use ($projectTaskId) {
                    if ($projectTaskId != '') {

                        $query ->whereIn('project_tasks.project_task_id',$projectTaskId);
                    }
                })
                ->where(function($query) use ($columnId) {

                    if ($columnId != '') {
                        $query->where('project_tasks.project_board_column_id', $columnId);
                    }
                })
                ->where(function($query) use ($boardId) {

                    if ($boardId != '') {
                        $query->where('project_tasks.project_board_id', $boardId);
                    }
                });
            if(!empty($offset)) {

                $q =   $q->skip($offset);
                $q =   $q->take($limit);
            }
            $q->groupBy('project_tasks.project_task_id');
            $q->orderBy('project_tasks.project_board_column_id');
            $q->orderBy('project_tasks.project_task_id','DESC')
                ->select('project_tasks.*','project_board_column.column_name','project_board_column.column_color_code','project_board_column.project_board_id','project_task_assignees.deleted_at');
        }])
            ->where(['project_boards.project_id' => $projectId])
            ->where(function($query) use ($project_board_id,$boardId) {

                if (count($project_board_id)) {
                    $query->whereIn('project_boards.project_board_id', $project_board_id);
                }else if(!empty($boardId)){

                    $query->where(['project_boards.project_board_id' => $boardId]);
                }
            })
            ->get(['project_boards.project_board_id','project_boards.project_board_name'])->toArray();

        return $result;
        //pr($result,1);
    }

    function projectBoardsAll(){

        return $this->hasMany(ProjectTasks::class, 'project_board_id', 'project_board_id');
    }

    public function scopeCommonCondition($scope)
    {
        return $scope
            ->leftjoin('project_board_column', 'project_board_column.project_board_id', '=', 'project_boards.project_board_id')
            ->leftjoin('user_project_columns', 'user_project_columns.project_board_column_id', '=', 'project_board_column.project_board_column_id');
    }

    function ProjectsTasksRelation()
    {
        return $this->hasMany(ProjectTasks::class, 'project_board_id', 'project_board_id')->orderBy('task_order_id', 'asc');
    }


    function boardColumnNames()
    {

        return $this->hasMany(ProjectBoardColumn::class, 'project_board_id');
    }

    function columnCreatedForNewAssignedUserForProject($projectId, $userId)
    {
        return $this->with(['boardColumnNames' => function ($query) {

            $query->select(['project_board_id', 'column_name', 'created_by_user_id', 'project_board_column_id']);
        }])->where(['project_id' => $projectId])->get()->toArray();


    }

    function  fetchUserProjectsAndTask($boardID, $userId, $role,$projectAdmin)
    {

        $data = $this
            ->CommonCondition()
            ->where(['project_boards.project_board_id' => $boardID, 'user_project_columns.user_id' => $userId])
            ->where(['user_project_columns.deleted_at' => null])
            ->get(['project_board_column.project_board_column_id'])
            ->toArray();
        $column = array_column($data, 'project_board_column_id');

        $this->columns = $column;
        $result = $this
            ->CommonCondition()
            ->whereIn('project_board_column.project_board_column_id', $column)
            ->with('boardColumns')
            ->with(['ProjectsTasks' => function ($qry) use($userId,$role,$projectAdmin){
                $qry->leftjoin('project_task_assignees','project_task_assignees.project_task_id','=','project_tasks.project_task_id');

                $qry->select(['project_task_assignees.user_id','project_tasks.project_task_id', 'project_tasks.subject', 'project_tasks.project_board_id', 'project_tasks.project_board_column_id', 'project_tasks.task_order_id', 'priority']);
                $qry->where(['project_task_assignees.user_id' => $userId]);
                $qry->where(['project_task_assignees.deleted_at' => null]);
            }])
            ->join('projects', 'projects.project_id', '=', 'project_boards.project_id')
            ->first();
        return $result;
        //pr($result->toArray());
    }

    function getProjectBoard($projectId,$companyId){

        return $this->join('project_board_column','project_board_column.project_board_id','=','project_boards.project_board_id')
            ->where(['project_id' => $projectId])
            ->get(['project_board_column.project_board_column_id']);
    }
}