<?php

namespace App\Http\Middleware;

use App\Models\HT20\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Redirect;

class AuthorizationApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->headers->has('Authorization')) {
            $header = $request->header('Authorization');
            $user = User::where('authentication', $header)->where("role",">",0)->where("status","0")->first();
            if (is_null($user)) {
                return response()
                    ->json([
                        'code' => 400,
                        'message' => 'Quyền không hợp lệ!'
                    ], 400);
            }
            if (Auth::check()) {
                Auth::logout();
            }
            Auth::login($user);
            return $next($request);
        } else {
            if (Auth::check()) return $next($request);
            else return Redirect::to('login')->with('infor', 'Bạn chưa đăng nhập!');
        }
    }
}
