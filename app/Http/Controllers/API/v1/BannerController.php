<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;

class BannerController extends Controller
{
    public function get()
    {
        // dfsdf
        $title = "New Job Available";
        $message = "You have a new job available.";
        $device_ids = "ejJ3zy3cTXyIy2grqij5Dn:APA91bG51347uKQcAhOQEfxNw4dTLYmqARnSa05eDjt5oZqXkDSF6MV9Bb2F1dyIwRj5boAQAv313KQyvRYtCNz-GSrmLN-3_CjWmR0YcDRsqNF9TNOeU2hfnV3axTzR5kXw3WurHLd8";
        $additional_data = ["key" => "sdfsdf"];

        $firebaseService = new SendNotificationJob();
        $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);

        $data = Slider::active()->where("app_type", "user")->get();

        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }

    public function getLabourSlider()
    {
        $data = Slider::active()->where("app_type", "labour")->get();

        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }
}
