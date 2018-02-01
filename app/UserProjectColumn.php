<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProjectColumn extends Model
{
    protected $table = 'user_project_columns';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'project_board_column_id', 'user_id', 'created_at', 'updated_at','deleted_at'
    ];
    function deleteChildRecord($projectColumnId)
    {
        $data = $this
            ->where(['project_board_column_id' => $projectColumnId])
            ->get(['project_board_column_id', 'user_project_column_id'])
            ->toArray();

        foreach ($data as $deleteRecord) {
            $this
                ->where('user_project_column_id', $deleteRecord['user_project_column_id'])
                ->update(['deleted_at' => date('Y-m-d h:i:s')]);
        }
    }


    function checkUserisAssignedForSpecificColumn($ProjectBoardColumnId, $userId)
    {

       return $this
            ->whereIn('project_board_column_id', $ProjectBoardColumnId)
            ->where(['user_id' => $userId])
            ->get(['user_project_column_id'])
            ->toArray();
    }

    function deletePermanently($projectColumnId)
    {
        return $this->whereIn('user_project_column_id' ,$projectColumnId)->forceDelete();
    }

    function insertRecord($dataToBeSaved){
            return $this->insert($dataToBeSaved);
    }


}
