<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function get(){
        $data = PromoCode::get();

        return response([
            "data" => $data,
            "status" => true 
        ],200);
    }
}
