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
        $booking_amount_data = Booking::where("labour_id", auth()->user()->id)->sum("total_amount");
        // $total_booking_accepted = Booking::where("labour_id", auth()->user()->id)->where("payment_status", "captured")->count();

        $total_booking_accepted = AcceptedBooking::where("labour_id",auth()->user()->id)->count();
        $total_rejected_booking = RejectedBooking::where("labour_id",auth()->user()->id)->count();
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

        if ($booking->quantity_required != $booking->current_quantity && !$current_user_booking) {

            $request_booking = new BookingRequest();
            $request_booking->user_id = auth()->user()->id;
            $request_booking->area_id = $area->id;
            $request_booking->checkout_id = $checkout->id;
            $request_booking->category_id = $category_id;
            $request_booking->booking_id = $booking->id;
            $request_booking->save();
        }

        $booking = BookingRequest::with("checkout","checkout.user:id,name","checkout.address.states:id,name","checkout.address.cities:id,name")->where("user_id", auth()->user()->id)->get();

        return response([
            "bookings" => $booking,
            "total_amount" => $booking_amount_data,
            "total_booking_accepted" =>   $total_booking_accepted,
            "total_rejected_booking" => $total_rejected_booking,
            "status" => true
        ], 200);
    }


    public function history()
    {


        $booking_data = Booking::with("user:id,name")->where("labour_id", auth()->user()->id)->where("payment_status", "captured")->latest()->get();

        return response([
            "data" => $booking_data,
            "status" => true
        ], 200);
    }

    public function acceptedBooking()
    {
        $data = Booking::with("user:id,name")->where("labour_id", auth()->user()->id)->where("payment_status", "captured")->get();

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

    // function to accept the user booking
    public function AcceptedUserBooking(Request $request){
        $user_id = auth()->user()->id;

        $accept_booking = new AcceptedBooking();
        $accept_booking->labour_id = $user_id;
        $accept_booking->booking_id = $request->booking_id;
        $accept_booking->save();

        return response([
            "message" => "Booking Accepted Successfully",
            "status" => true
        ],200);


    }

    public function rejectUserBooking(Request $request){
        $user_id = auth()->user()->id;

        $rejectBooking  = new RejectedBooking();

        $rejectBooking->labour_id = $user_id;
        $rejectBooking->booking_id = $request->booking_id;
        $rejectBooking->save();

        return response([
            "message" => "Booking Rejected",
            "status" => true
        ],200);
    }
}
