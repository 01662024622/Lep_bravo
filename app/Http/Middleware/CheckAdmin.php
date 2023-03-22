<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use Redirect;

class CheckAdmin
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
        if (Auth::check()) {
            if(Auth::user()->role >= 100)
            {
                return $next($request);

            }

        }
         // logout the user
        Auth::logout();
                // redirect back to the previous page with errors
        return Redirect::to('login')->with('error', 'You are not allowed to login, because you do not have the right role!');
    }
}
