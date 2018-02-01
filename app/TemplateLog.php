<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateLog extends Model
{

    protected $table = 'template_logs';

    protected $fillable = [
        'action_id', 'tags', 'type','message'
    ];
}
