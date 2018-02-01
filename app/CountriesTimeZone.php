<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountriesTimeZone extends Model
{
    protected $table = 'countries_timezone';

    public function getTimezone($countryId){

        return $this->where(['country_id' =>$countryId])
            ->get()->toArray();
    }
}
