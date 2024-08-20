<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function get(){
        $data = Slider::where("app_type","labour")->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
