<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Companies;

class CompanyDetailsController extends Controller
{
   public  function companyDetails(){
      $companyList = (new Companies())
              ->join('company_users', 'company_users.company_id','=', 'companies.company_id')
              ->join('users', 'users.id','=', 'company_users.user_id')
              ->select(\DB::raw('count(*) as user_count'), 'users.id','company_users.user_id','company_users.role','companies.name','users.email')
              //->where('company_users.role', 1)
              ->groupBy('company_users.company_id')
              ->get();
      // pr($companyList);
       //die;
       return view('users.company-details',compact('companyList'));
   }
}
