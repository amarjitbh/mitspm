<?php

use Illuminate\Database\Seeder;

class UserSettingTableSeeded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $companies = DB::table('companies')->select('company_id', 'name')->get();
//        foreach ($companies as $company){
//            DB::table('company_settings')->insert([[
//                    'company_id' => $company->company_id,
//                    'user_id' => \Config::get('constants.SETTING_DATE_FORMAT'),
//                    'project_id' => \Config::get('constants.GENERAL_SETTING_DATE_FORMAT')[0],
//                    'report' => \Config::get('constants.GENERAL_SETTING_DATE_FORMAT')[0],
//                    'email' => \Config::get('constants.GENERAL_SETTING_DATE_FORMAT')[0],
//                    'created_at' => date('Y-m-d :h:i:s')
//            ]]);
//        }
    }
}
