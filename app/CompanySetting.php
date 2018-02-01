<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
            'company_id',
            'meta_key',
            'meta_value',
    ];
    protected $primaryKey = 'company_setting_id';
    protected $table = 'company_settings';
}
