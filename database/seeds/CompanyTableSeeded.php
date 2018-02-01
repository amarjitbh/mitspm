<?php

use Illuminate\Database\Seeder;

class CompanyTableSeeded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->truncate();
        DB::table('users')->where(['email'=>Config::get('constants.SUPER_ADMIN')])->delete();
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'Codelee',
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ]);
        $SuperAdminId = DB::table('users')->insertGetId([
            'name'=>'SuperAdmin',
            'email' => Config::get('constants.SUPER_ADMIN'),
            'password' => Hash::make(Config::get('constants.SUPER_ADMIN_DEFAULT_PASSWORD')),
            'country_id'    => '1',
            'country_timezone_id'    => '1',
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ]);

        $CompanyUser = DB::table('company_users')->insertGetId([
            'company_id' => $companyId,
            'user_id' => $SuperAdminId,
            'role' => Config::get('constants.ROLE.SUPERADMIN'),
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ]);
    }
}
