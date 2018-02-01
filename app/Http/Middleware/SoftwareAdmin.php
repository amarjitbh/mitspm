<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class SoftwareAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //return $next($request);
        $userID = \Auth::user()->id;
        $users = (new User())->where(['id' => $userID])->first(['user_type']);
        if($users->user_type=='1'){
            return $next($request);
        }
        return redirect(route('login'))->withErrors('unauthorized access.');
    }
}
