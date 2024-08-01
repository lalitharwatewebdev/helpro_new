<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function get(){
        $data = Slider::active()->where("app_type","user")->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
