<?php

use Illuminate\Database\Seeder;

class SoftwareAdminSeeded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

              DB::table('users')->insert([
                'name'=>'Software Admin',
                'email' => Config::get('constants.SOFTWARE_ADMIN'),
                'password' => Hash::make(Config::get('constants.SOFTWARE_ADMIN_DEFAULT_PASSWORD')),
                'user_type' => '1',
                'created_at' => date('Y-m-d h:i:s')

        ]);

    }
}
