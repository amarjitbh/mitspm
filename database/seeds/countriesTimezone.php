<?php

use Illuminate\Database\Seeder;

class countriesTimezone extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $countriesIndia = DB::table('countries')->insertGetId([
            'name' => 'India',
            'created_at' => date('Y-m-d h:i:s'),
        ]);
        DB::table('countries_timezone')->insert([
            'country_id' => $countriesIndia,
            'timezone' => 'Asia/Kolkata',
            'compare_utc'   => '+5:30',
            'created_at' => date('Y-m-d h:i:s'),
        ]);

        $countriesUsa = DB::table('countries')->insertGetId([
            'name' => 'America',
            'created_at' => date('Y-m-d h:i:s'),
        ]);
        $countriesAus = DB::table('countries')->insertGetId([
            'name' => 'Australia',
            'created_at' => date('Y-m-d h:i:s'),
        ]);



        DB::table('countries_timezone')->insert([
            'country_id' => $countriesUsa,
            'timezone' => 'America/Anguilla',
            'compare_utc'   => '-04:00',
            'created_at' => date('Y-m-d h:i:s'),
        ]);
        DB::table('countries_timezone')->insert([
            'country_id' => $countriesAus,
            'timezone' => 'Australia/Sydney',
            'compare_utc'   => '+10:00',
            'created_at' => date('Y-m-d h:i:s'),
        ]);
    }
}
