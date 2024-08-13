<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function get(){
        $user_id = auth()->user()->id;

        $booking_amount_data = Booking::where("labour_id",$user_id)->sum("total_amount");


        return response([
            "total_amount" => $booking_amount_data,
            "status" => true
        ],200);
    }


    public function history(){
        $user_id = auth()->user()->id;

        $booking_data = Booking::where("labour_id",auth()->user()->id)-latest()->get();

        return response([
            "data" => $booking_data,
            "status" => true
        ],200);
    }
}
