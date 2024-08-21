<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use App\Models\Areas;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\RejectedBooking;
use App\Models\AcceptedBooking;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user_id  = auth()->user()->id;
        $data = User::with("states:id,name", "cities:id,name")->find($user_id);
        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }

    public function activeStatus(Request $request)
    {
        $data = User::find(auth()->user()->id);

        $data->is_online = $data->is_online == "yes" ? "no" : "yes";
        $data->save();
        return response([
            "message" => "Online Status Updated Successfully",
            "online_status" => $data->is_online,
            "status" => true
        ], 200);
    }

    public function get()
    {
        $labour_id = auth()->user();
        $categories =  $labour_id->category()->first();
        $category_id =  $categories->pivot->category_id;
        $radius = 5;
        $booking_amount_data = AcceptedBooking::with("booking")->where("labour_id", auth()->user()->id)->get();

        $total_amount = $booking_amount_data->sum(function($acceptedBooking){
            return $acceptedBooking->booking->total_amount; 
        });

      

        $total_booking_accepted = AcceptedBooking::where("labour_id", auth()->user()->id)->count();
        $total_rejected_booking = RejectedBooking::where("labour_id", auth()->user()->id)->count();
        // return Booking::where("user_id",auth()->user()->id)->get();
        $earthRadius = 6371; // Earth radius in kilometers
        $latitude = 19.1985893;
        $longitude = 72.955762;

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
        $area =  Areas::whereBetween('latitude', [$latMin, $latMax])
            ->whereBetween('longitude', [$lonMin, $lonMax])
            ->where("category_id", $category_id)
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
            })->first();

        // getting checkout id as per user location
        $checkout = Checkout::where("area_id", $area->id)
            ->where("category_id", $category_id)->first();

        $booking =  Booking::where("checkout_id", $checkout->id)->first();

        // checking if request booking is present for the current user
        $current_user_booking = BookingRequest::where("booking_id", $booking->id)->where("user_id", auth()->user()->id)->first();



        // this is checking to see if required quantity does not match current _current
        if ($booking->quantity_required != $booking->current_quantity && !$current_user_booking) {
            $request_booking = new BookingRequest();
            $request_booking->user_id = auth()->user()->id;
            $request_booking->area_id = $area->id;
            $request_booking->checkout_id = $checkout->id;
            $request_booking->category_id = $category_id;
            $request_booking->booking_id = $booking->id;
            $request_booking->save();
        }

        $bookings = BookingRequest::with("checkout", "checkout.user:id,name", "checkout.address.states:id,name", "checkout.address.cities:id,name", "checkout.area")->where("user_id", auth()->user()->id)
            ->where("category_id", $category_id)->get();

        // return $bookings;

        foreach ($bookings as $booking) {
            $start_date = $booking->checkout->start_time;
            $end_date = $booking->checkout->end_time;
            $diff = (strtotime($start_date) - strtotime($end_date));
            $date_result = abs(round($diff) / 86400) + 1;

            $labour_quantity = $booking->checkout->labour_quantity;
            $area_price = $booking->checkout->area->price;
            $final_price = ($area_price * $date_result) / $labour_quantity;
            $booking->labour_total_amount = round($final_price, 2);
        }




        return response([
            "bookings" => $bookings,
            "total_amount" => $total_amount,
            "total_booking_accepted" =>   $total_booking_accepted,
            "total_rejected_booking" => $total_rejected_booking,
            "status" => true
        ], 200);
    }   


    public function history()
    {
        // Fetch accepted and rejected bookings
        $accepted_bookings = AcceptedBooking::with("booking.checkout.address.states:id,name", "booking.checkout.address.cities:id,name", "booking.checkout.user:id,name,phone,lat_long")
            ->where("labour_id", auth()->user()->id)
            ->latest()
            ->get();

        $rejected_bookings = RejectedBooking::with("booking.checkout.address.states:id,name", "booking.checkout.address.cities:id,name", "booking.checkout.user:id,name,phone,lat_long")
            ->where("labour_id", auth()->user()->id)
            ->latest()
            ->get();


        $combined_bookings = $accepted_bookings->merge($rejected_bookings);

        $sorted_bookings = $combined_bookings->sortByDesc('created_at');


        $formatted_bookings = $sorted_bookings->values()->all();


        $results = [];

        foreach ($formatted_bookings as $booking) {
            $bookingData = $booking->booking;
            $start_date = $booking->booking->checkout->start_date;
            $end_date = $booking->booking->checkout->end_date;
            $diff = (strtotime($start_date) - strtotime($end_date));
            $date_result = abs(round($diff) / 86400) + 1;
            $labour_quantity = $booking->booking->checkout->labour_quantity;
            $area_price = $booking->booking->checkout->area->price;

            $final_price = ($area_price * $date_result) / $labour_quantity;

            $bookingData->labour_total_amount = round($final_price, 2);
            $results[] = $bookingData;
           
        }

    

        // Optionally, you can sort $results if needed, e.g., by start_date
        $sorted_results = collect($results)->sortByDesc('start_date')->values()->all();



        return response([
            "data" => $formatted_bookings,
            "status" => true
        ], 200);
    }

    public function acceptedBooking()
    {
        $data = AcceptedBooking::with("booking.checkout.address")->where("labour_id", auth()->user()->id)->get();

        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }

    public function rejectedBooking()
    {
        $data = Booking::with("user:id,name")->where("labour_id", auth()->user()->id)->get();
        return response([
            "data" => $data,
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

    // function to accept and reject the user booking
    public function acceptRejectBooking(Request $request)
    {
        $action = $request->action;
        $booking_id = $request->booking_id;



        if (strtolower($action) == "rejected") {
            $reject_data = RejectedBooking::create([
                "labour_id" => auth()->user()->id,
                "booking_id" => $booking_id,
            ]);

            BookingRequest::where("user_id", auth()->user()->id)->where("booking_id", $booking_id)->delete();
            return response([
                "message" => "Booking Rejected",
                "status" => true
            ], 200);
        }
        if (strtolower($action) == 'accepted') {
            $accept_data = AcceptedBooking::create([
                "labour_id" => auth()->user()->id,
                "booking_id" => $booking_id
            ]);
            Booking::where("booking_id", $booking_id)->increment("current_quantity", 1);

            BookingRequest::where("user_id", auth()->user()->id)->where("booking_id", $booking_id)->delete();

            return response([
                "message" => "Booking Accepted",
                "status" => true
            ], 200);
        }

        // else{
        //     return response([
        //         "message" => "Invalid Parameter",
        //         "stat"
        //     ])
        // }
    }
}
