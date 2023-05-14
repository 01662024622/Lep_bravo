<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SpeedService;
use Illuminate\Http\Request;

class AutoController extends Controller
{
    private $SpeedService;
    public function create(Request $request)
    {
        $data = json_decode(json_encode($request->only(["type", "secret"])), FALSE);
        if ($data->secret != "9BE94DC179BB890F4AB1DC7EFF16F819B10C11C5") return response('error', 404);

        $this->SpeedService = SpeedService::getInstance();
        $bravo = new BravoController();
        if ($data->type == 1) {
            $bravo->procedureChangeOver($this->SpeedService);
        } else {
            $bravo->addListOrder($this->SpeedService);
        }

        return response("true", 200);
    }

}
