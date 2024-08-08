<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{

    public function get()
    {
        $user_id = auth()->user()->id;

        $data = Booking::with("labour", "checkout")->where("user_id", $user_id)
            ->where("booking_status", "captured")
            ->get();

        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }
}
