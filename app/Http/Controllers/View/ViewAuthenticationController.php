<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewAuthenticationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home(){
        return view("view_infor.dashboard",['active' => 'dashboard', 'group' => '']);
    }
}
