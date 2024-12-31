<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Areas;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        $data = Category::active()->get();

        return response([
            "data" => $data,
            "status" => true,
        ], 200);
    }

    public function getArea(Request $request)
    {

        \Log::info($request->all());

        $user = User::find(auth()->user()->id);

        $user->update([
            "lat_long" => $request->lat_long,
        ]);

        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        // $radius = $business_settings['radius'];
        $category_id = $request->category_id;
        $lat_long = $request->lat_long;
        $radius = $business_settings['radius'];

        $category_data = Category::find($request->category_id);

        // Validate inputs
        if (!$category_id || !$lat_long || !$radius) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Parse lat_long into latitude and longitude
        list($latitude, $longitude) = explode(',', $lat_long);

        // Ensure latitude and longitude are valid
        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        // Query to get areas within a rough bounding box around the center point
        $earthRadius = 6371; // Earth radius in kilometers

        // Convert latitude and longitude from degrees to radians
        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);

        // Calculate bounding box
        $latDelta = $radius / $earthRadius;
        $lonDelta = $radius / ($earthRadius * cos($latFrom));

        $latMin = rad2deg($latFrom - $latDelta);
        $latMax = rad2deg($latFrom + $latDelta);
        $lonMin = rad2deg($lonFrom - $lonDelta);
        $lonMax = rad2deg($lonFrom + $lonDelta);

        // Get areas in bounding box
        // $areas = Areas::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
        //     ->where('category_id', $category_id)
        // // ->whereBetween('latitude', [$latMin, $latMax])
        // // ->whereBetween('longitude', [$lonMin, $lonMax])
        //     ->with("category:id,title,image")->take(1)
        //     ->get();

        $areas = DB::table('areas')->select('areas.*', DB::raw("
            (6371 * acos(
                cos(radians($latitude)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians($longitude)) +
                sin(radians($latitude)) *
                sin(radians(latitude))
            )) AS distance
        "))
            ->where('category_id', $category_id)->having('distance', '<', $radius)
            ->orderBy('distance')
            ->take(1)->get();

        // return $areas;

        if (!empty($areas[0])) {
            $area_data = Areas::with(["category:id,title,image"])->where('id', $areas[0]->id)->get();
        } else {
            $area_data = [];
        }

        $labours_device_id = User::where('type', 'labour')
            ->where("is_online", "yes")
            ->whereHas('category', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
            ->get()
            ->filter(function ($labour) use ($latitude, $longitude, $radius, $request) {
                [$labourLatitude, $labourLongitude] = explode(',', $request->lat_long);
                $distance = $this->haversineGreatCircleDistance(
                    $latitude,
                    $longitude,
                    $labourLatitude,
                    $labourLongitude
                );
                return $distance <= $radius;
            })
            ->map(function ($labour) {
                $labour->type = 'labour';
                return $labour;
            })->pluck("device_id")->toArray();

        // $labours = User::where('type', 'labour')
        //     ->where("is_online", "yes")
        //     ->whereHas('category', function ($query) use ($category_id) {
        //         $query->where('category_id', $category_id);
        //     })
        //     ->get()
        //     ->filter(function ($labour) use ($latitude, $longitude, $radius, $request) {
        //         [$labourLatitude, $labourLongitude] = explode(',', $request->lat_long);
        //         $distance = $this->haversineGreatCircleDistance(
        //             $latitude,
        //             $longitude,
        //             $labourLatitude,
        //             $labourLongitude
        //         );
        //         return $distance <= $radius;
        //     })
        //     ->map(function ($labour) {
        //         $labour->type = 'labour';
        //         return $labour;
        //     })->toArray();

        $labours = DB::table('users')
            ->select('*')
            ->selectRaw("
        (6371 * acos(
            cos(radians(?)) *
            cos(radians(SUBSTRING_INDEX(lat_long, ',', 1))) *
            cos(radians(SUBSTRING_INDEX(lat_long, ',', -1)) - radians(?)) +
            sin(radians(?)) *
            sin(radians(SUBSTRING_INDEX(lat_long, ',', 1)))
        )) AS distance
    ", [$latitude, $longitude, $latitude])
            ->where('type', 'labour')->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();
        \Log::info("message");

        \Log::info($labours);
        // $labours = User::where("type","labour")->get();
        // \Log::info("labour device id ==> ",$labours_device_id);
        // if(!empty($labours)){

        // $user_address = Address::where("user_id",auth()->user()->id)->where("is_primary","yes")->first();
        // $title = "New Job Available";
        // $message = "You have a new job available.";
        // $device_ids = $labours_device_id;
        // $additional_data = ["category_name" => $category_data->title,"address" => $user_address->address,"booking_id"=>32];

        // $firebaseService = new SendNotificationJob();
        // $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);
        // }

        $responseData = [
            'areas' => $area_data,
            'labour' => $labours,
        ];

        return response([
            "data" => $responseData,
            "status" => true,
        ], 200);
    }

    private function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
