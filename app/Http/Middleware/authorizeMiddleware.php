<?php

namespace App\Http\Middleware;

use Closure;

class authorizeMiddleware
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
        $routeName = \Route::currentRouteName();
        pr($routeName);
        pr(myRoutes()); die;
        if (in_array($routeName, myRoutes())) {
            return $next($request);
        } else {
            return redirect(route('login'))->withErrors('unauthorized access.');
        }
    }
}
