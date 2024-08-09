<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{

    public function get()
    {
        $user_id = auth()->user()->id;

        $data = Booking::with("labour")->where("user_id", $user_id)
            ->where("payment_status", "captured")
            ->get();

            // $groupedByCheckout = $data->groupBy(function ($booking) {
            //     return $booking->checkout->id; // or use another unique attribute from checkout
            // });

        // $result = Booking::whereHas("checkout",function($query){
        //     $query->where("user_id",auth()->user()->id)->get();
        // })->get();

        return response([
            "data" => $data,
            // "result" => $result,
            "status" => true
        ], 200);
    }
}
