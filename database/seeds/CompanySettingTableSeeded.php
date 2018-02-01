<?php

use Illuminate\Database\Seeder;

class CompanySettingTableSeeded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $companies = DB::table('companies')->select('company_id', 'name')->get();
        foreach ($companies as $company){
            DB::table('company_settings')->insert([[
                    'company_id' => $company->company_id,
                    'meta_key' => \Config::get('constants.SETTING_DATE_FORMAT'),
                    'meta_value' => \Config::get('constants.GENERAL_SETTING_DATE_FORMAT')[0],
                    'created_at' => date('Y-m-d :h:i:s')
            ], [
                    'company_id' => $company->company_id,
                    'meta_key' => \Config::get('constants.SETTING_TIME_FORMAT'),
                    'meta_value' => \Config::get('constants.GENERAL_SETTING_TIME_FORMAT')[0],
                    'created_at' => date('Y-m-d :h:i:s')
            ]]);
        }
    }
}
