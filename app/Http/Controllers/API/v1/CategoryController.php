<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Areas;
use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get(Request $request)
    {



        $data = Category::active()->get();

        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }

    public function getArea(Request $request)
    {
        $category_id = $request->category_id;
        $lat_long = $request->lat_long;
        $radius = 5;

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
        $areas = Areas::with("category:id,title,image")->where('category_id', $category_id)
            ->whereBetween('latitude', [$latMin, $latMax])
            ->whereBetween('longitude', [$lonMin, $lonMax])
            ->get()
            ->filter(function ($area) use ($latitude, $longitude, $radius) {
                // Calculate distance to see if within radius
                $distance = $this->haversineGreatCircleDistance(
                    $latitude,
                    $longitude,
                    $area->latitude,
                    $area->longitude
                );
                return $distance <= $radius;
            });

        $labours = User::where('type', 'labour')
            ->whereHas('category', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
            ->get()
            ->filter(function ($labour) use ($latitude, $longitude, $radius) {
                [$labourLatitude, $labourLongitude] = explode(',', $labour->lat_long);
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
            });


        $responseData = [
            'areas' => $areas,
            'labour' => $labours
        ];


        return response([
            "data" => $responseData,
            "status" => true
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
