<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\AcceptedBooking;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{

    public function get()
    {
        $user_id = auth()->user()->id;

        $booking_data = Booking::with("labour", "checkout.address", "checkout.category")->where("user_id", $user_id)
            ->where("payment_status", "captured")
            ->get();

        $processed_bookings = $booking_data->map(function ($booking) {
            $labours = AcceptedBooking::with('labour:id,name,phone')->where("booking_id",$booking->id)->get();
            $booking->labours = $labours;
            return $booking;
        });

        return response([
            "data" => $booking_data,
            "status" => true
        ], 200);
    }
}
