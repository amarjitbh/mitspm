<?php

namespace App\Http\Controllers;

use App\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoftwareAdminController extends Controller
{
    function index(){

        //echo 'asdfsdf';
        $companyList = (new Companies())
            ->join('company_users','company_users.company_id','=','companies.company_id')
            ->join('users','users.id','=','company_users.user_id')
            ->groupBy('companies.company_id')
            ->get(['companies.name',DB::Raw('count(users.id) as user_count'),'users.email']);
            //->toArray();
        //pr($companyList,1);

        return view('users.company-details',compact('companyList'));
    }
}
