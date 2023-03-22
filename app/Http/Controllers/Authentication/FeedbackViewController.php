<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\HT20\User;
use Illuminate\Support\Facades\Auth;

class FeedbackViewController extends Controller
{
    public function feedbackApartmentAuth($auth)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $user = User::where('authentication', "=", $auth)->where("role",">",0)->where("status",0)->first();
        if ($user){
            Auth::login($user);
            return redirect('/review/apartment/feedback');
        }else{
            return view("errors.404");
        }
    }
    public function feedbackMeAuth($auth)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $user = User::where('authentication', "=", $auth)->where("role",">",0)->where("status",0)->first();
        if ($user){
            Auth::login($user);
            return redirect('/review/feedback');
        }else{
            return view("errors.404");
        }
    }
    public function feedbackAuthBrowser($auth)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $user = User::where('authentication', "=", $auth)->where("role",">",0)->where("status",0)->first();
        if ($user){
            Auth::login($user);
            return redirect('/review/browser/feedback');
        }else{
            return view("errors.404");
        }
    }
}
