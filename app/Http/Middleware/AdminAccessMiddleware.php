<?php

namespace App\Http\Middleware;

use Closure;

class AdminAccessMiddleware
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
        $role=$request->session()->get('user_role');
        if($role==\Config::get('constants.ROLE.ADMIN')){
            return $next($request);
        }
        return redirect(route('login'))->withErrors('unauthorized access.');
    }
}
