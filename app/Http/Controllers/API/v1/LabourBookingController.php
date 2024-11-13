<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;

//notification
use App\Models\Address;

// models
use App\Models\Areas;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\LabourBooking;
use App\Models\User;
use Illuminate\Http\Request;

// notification

class LabourBookingController extends Controller
{
    public function bookNew(Request $request)
    {
        \Log::info($request->all());
        \Log::info("Labour booking from user App ::: =>", $request->all());

        $request->validate([
            "category_id" => "required|exists:categories,id",
            "labour_quantity" => "required|integer",
            "address_id" => "required|exists:addresses,id",
            "lat_long" => "required",
            "start_time" => "required",
            "end_time" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "address_id" => 'required|exists:addresses,id',
        ]);
        // first we take users updated lat_long
        $user = User::find(auth()->user()->id);
        $user->update(["lat_long" => $request->lat_long]);

        // get labour as per the user required category
        $category_id = $request->category_id;
        $lat_long = $request->lat_long;

        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
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

        // get area first nearest to user's co-ordinate
        $areas = Areas::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->where('category_id', $category_id)
            ->whereBetween('latitude', [$latMin, $latMax])
            ->whereBetween('longitude', [$lonMin, $lonMax])
            ->with("category:id,title,image")->take(1)
            ->get();

        \Log::info("areas");
        \Log::info($areas);
        \Log::info(!empty($areas));

        $labours = '';

        if (!empty($areas)) {

            \Log::info("inside area");
            \Log::info($areas);
            $labours = User::where('type', 'labour')
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
        }
        // return response([
        //     "message" => "Booked Successfully",
        //     "status" => true,
        // ], 200);

        \Log::info("labours");
        \Log::info($labours);
        try {
            if (!empty($labours)) {
                \Log::info("inside labour");
                \Log::info($labours);
                $labourBooking = new LabourBooking();
                $labourBooking->user_id = auth()->user()->id;
                $labourBooking->category_id = $request->category_id;
                $labourBooking->labour_quantity = $request->labour_quantity;
                $labourBooking->address_id = $request->address_id;
                $labourBooking->labour_booking_code = sha1(now());
                $labourBooking->start_time = $request->start_time;
                $labourBooking->end_time = $request->end_time;
                $labourBooking->start_date = $request->start_date;
                $labourBooking->end_date = $request->end_date;
                $labourBooking->labour_amount = $request->labour_amount;
                $labourBooking->commission_amount = $request->commission_amount;
                $labourBooking->total_labour_charges = $request->total_labour_charges;
                $labourBooking->save();
                \Log::info($labourBooking);
                \Log::info("LabourBooking");

                // ("Labour Booking Done :: ", $labourBooki\Log::infong);
                if ($labourBooking) {
                    \Log::info("userDatataa");
                    \Log::info(auth()->user()->id);

                    $user_address = Address::where("user_id", auth()->user()->id)->where("is_primary", "yes")->first();

                    if (!$user_address) {
                        return response([
                            "message" => "Address is required",
                            "status" => true,
                        ], 400);
                    }

                    // $title = "New Job Available";
                    // $message = "You have a new job available.";
                    // $start_time = $request->start_time;
                    // $end_time = $request->end_time;
                    // $start_date = $request->start_date;
                    // $end_date = $request->end_date;
                    // $device_ids = $labours;
                    // $additional_data = [
                    //     "category_name" => $category_data->title,
                    //     "address" => $user_address->address,
                    //     "booking_code" => $labourBooking->labour_booking_code,
                    //     "start_date" => $start_date,
                    //     "end_date" => $end_date,
                    //     "start_time" => $start_time,
                    //     "end_time" => $end_time,
                    //     "price" => $request->labour_amount / $request->labour_quantity,

                    // ];

                    // $firebaseService = new SendNotificationJob();
                    // $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);
                    \Log::info("Notification send");
                }
            }

            return response([
                "message" => "Booked Successfully",
                "labour_booking_id" => $labourBooking->id,
                "status" => true,
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Error in sending notification: " . $e->getMessage());

            return response([
                "message" => "Something went wrong. Try Again Later",
                "status" => true,
            ], 400);
        }
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

    public function workDone(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
        ]);

        $booking = Booking::where('id', $request->booking_id)->first();
        $booking->is_user_work_done = 1;

        $booking->save();

        return response([
            "message" => "Work Done Successfully",
            "status" => true,
        ], 200);

    }
}
