<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(CompanyTableSeeded::class);
         //$this->call(countriesTimezone::class);
        $this->call(TemplateLogTableSeeded::class);
        $this->call(CompanySettingTableSeeded::class);

         $this->call(SoftwareAdminSeeded::class);
    }
}
