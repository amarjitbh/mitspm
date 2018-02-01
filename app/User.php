<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','first_name','last_name','country_id','country_timezone_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      //  'password', 'remember_token',
    ];


    public function UserExists($email){
        return $this
            ->where(['email'=>$email])
            ->first(['id','email','remember_token', 'user_type']);
    }
    public function UserAssociatedWithCompany($email,$companyId){
        return $this
            ->where(['email'=>$email])
            ->join('company_users','company_users.user_id','=','users.id')
            ->where(['company_users.company_id' => $companyId])
            ->first(['id','email','remember_token']);
    }

    //not using below fxn
    public function fetchResult($userID){
        return $this
            ->where('id','=',$userID)
            ->first(['*']);
    }



}
