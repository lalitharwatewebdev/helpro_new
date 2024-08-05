<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class BannerController extends Controller
{
    public function get(){
        $data = Slider::active()->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
