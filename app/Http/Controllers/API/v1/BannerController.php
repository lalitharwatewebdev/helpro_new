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
        // $title = "New Job Available";
        // $message = "You have a new job available.";
        // $device_ids = "cWTfpKz-R8C9uim9xNHzKF:APA91bG9XyX4utJ5yAejC6AqORuB0ovrpnfRM_jcD4-Xbl03p7m0yGXdjfATMnFPc2PpodUO-K__Bre45UUib51V9dMFQfBlZsHQJPqguQMx1oSbm5LC8OWAMdN6qnvuMziOzCMtPKde";
        // $additional_data = ["category_name" => "Plumber","address" => "Thane(W)"];

        // $firebaseService = new SendNotificationJob();
        // $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);

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
