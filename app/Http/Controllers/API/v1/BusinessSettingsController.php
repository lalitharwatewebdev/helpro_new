<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\LabourBusinessSettings;

class BusinessSettingsController extends Controller
{
    public function get(){
        $data = BusinessSetting::pluck("value","key")->toArray();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function labourGet(){
        $data  = LabourBusinessSettings::pluck("value","title")->toArray();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
