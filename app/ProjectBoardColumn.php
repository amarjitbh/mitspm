<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBoardColumn extends Model
{
    protected $table = 'project_board_column';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    function fetchColumnsName($boardId)
    {
        return $this
            ->where(['project_board_id' => $boardId])
            ->get();
    }


}


