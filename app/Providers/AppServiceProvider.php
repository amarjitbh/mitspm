<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       /* \Validator::extend('alpha_spaces', function ($attribute, $value) {
            return preg_match('/^[\pL\s-.]+$/u', $value);
        });*/

        Validator::extend("emails", function($attribute, $value, $parameters) {
            $rules = [
                'email' => 'required|email',
            ];
            $value= rtrim($value,',');
            $explode=explode(',',$value);
            foreach ($explode as $email) {
                $data = [
                    'email' => $email
                ];
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
