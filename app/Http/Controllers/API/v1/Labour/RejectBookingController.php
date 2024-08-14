<?php

namespace App\Http\Controllers\Api\v1\Labour;

use App\Http\Controllers\Controller;
use App\Models\RejectedBooking;
use Illuminate\Http\Request;

class RejectBookingController extends Controller
{
    public function rejectBooking(Request $request){
        RejectedBooking::create([
            "labour_id" => auth()->user()->id,
            "checkout_id" => $request->checkout_id,
        ]);

        return response([
            "message" => "Booking Rejected",
            "status" => true
        ],200);
    }
}
