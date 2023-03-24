<?php

namespace App\Http\Controllers;

use App\B20Customer;
use Illuminate\Http\Request;

class BravoController extends Controller
{
    public function get()
    {
        B20Customer::create(['Code' => 'HH00192319','ParentId'=>18104192,'Name'=>'thangvm','CustomerType'=>0,'Tel'=>'0362024622']);
        return response("true", 200);
    }
}
