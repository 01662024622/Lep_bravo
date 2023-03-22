<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Redirect;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
        public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            if(Auth::user()->role == 0||Auth::user()->status==1)
            {
                // logout the user
                Auth::logout();

                // redirect back to the previous page with errors
                return Redirect::to('login')->with('error', 'You are not allowed to login, because you do not have the right role!');
            }

        }
        $this->authenticate($request, $guards);

        return $next($request);
    }

}
