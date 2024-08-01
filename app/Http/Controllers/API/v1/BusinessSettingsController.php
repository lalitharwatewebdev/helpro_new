<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;

class BusinessSettingsController extends Controller
{
    public function get(){
        $data = BusinessSetting::pluck("value","key");

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
