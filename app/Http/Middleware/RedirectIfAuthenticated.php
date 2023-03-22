<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Redirect;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()) {
            if (Auth::user()->role == 0) {
                // logout the user
                Auth::logout();

                // redirect back to the previous page with errors
                return Redirect::to('login')->with('error', 'You are not allowed to login, because you do not have the right role!');
            }

//        return $next($request);
            return redirect(RouteServiceProvider::HOME);

        }

        return $next($request);
    }
}
