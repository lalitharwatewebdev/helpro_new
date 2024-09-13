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
        $device_ids = "cdCKPrT4RhiQFQdt60T9lk:APA91bEtqJW5_WXwp3Rc8OEwpkTkck2CB-kZNepJp4SRuHz6kDT3Zbdf0vo0wtl85IWEwe0KeBINJ4DJgZA7PIpmgnj9B7yC4My_7JOHW_keAHR6eHSuZ4C70aG5d10qnOsE8akbAqd-";
        $additional_data = ["category_name" => "Plumber","address" => "Thane(W)"];

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
