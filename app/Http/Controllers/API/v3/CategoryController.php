<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Category;
use Illuminate\Http\Request;

// models
use App\Models\User;
use App\Models\Areas;

class CategoryController extends Controller
{
    public function get(){
        $data = Category::active()->get();
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function getLabourCountAsPerCategory(Request $request){
        $category_id = $request->query('category_id');


        // getting lat_long from user who is booking
        $lat_long = $request->query("lat_long");

        // getting radius dynamically from business settings
        $radius = BusinessSetting::where("key","radius")->pluck("key")->first();

        list($latitude, $longitude) = explode(',', $lat_long);

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


        $labour_count = User::where('type', 'labour')
        ->where("is_online","yes")
        ->where("labour_status","accepted")
        ->whereHas('category', function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })
        ->count()
        ->filter(function ($labour) use ($latitude, $longitude, $radius) {
            [$labourLatitude, $labourLongitude] = explode(',', $labour->lat_long);
            $distance = $this->haversineGreatCircleDistance(
                $latitude,
                $longitude,
                $labourLatitude,
                $labourLongitude
            );
            return $distance <= $radius;
        })->toArray();

        return response([
            "message" => "There are ". $labour_count. " available in your area",
            "data" => $labour_count,
            "status" => true
        ]);




        // Get areas in bounding box
        //  $areas = Areas::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
        //     ->where('category_id', $category_id)
        //     ->whereBetween('latitude', [$latMin, $latMax])
        //     ->whereBetween('longitude', [$lonMin, $lonMax])
        //     ->with("category:id,title,image")->take(1)
        //     ->get();
        
        
        //  $labours_device_id = User::where('type', 'labour')
        //  ->where("is_online","yes")
        //     ->whereHas('category', function ($query) use ($category_id) {
        //         $query->where('category_id', $category_id);
        //     })
        //     ->get()
        //     ->filter(function ($labour) use ($latitude, $longitude, $radius) {
        //         [$labourLatitude, $labourLongitude] = explode(',', $labour->lat_long);
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
        //     })->pluck("device_id")->toArray();


          


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
