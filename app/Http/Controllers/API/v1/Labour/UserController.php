<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;

class UserController extends Controller
{
    public function profile(Request $request){
        $user_id  = auth()->user()->id;
        $data = User::find($user_id);
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function activeStatus(Request $request){
        $data = User::find(auth()->user()->id);

        $data->is_online = $data->is_online == "yes" ? "no" :"yes";
        $data->save();
        return response([
            "message" => "Online Status Updated Successfully",
            "online_status" => $data->is_online,
            "status" => true
        ],200);
    }

    public function get(){
        $user_id = auth()->user()->id;

        $booking_amount_data = Booking::where("labour_id",$user_id)->sum("total_amount");
        $total_booking_accepted = Booking::where("labour_id",auth()->user()->id)->where("payment_status","captured")->count();


        return response([
            "total_amount" => $booking_amount_data,
            "total_booking_accepted" =>   $total_booking_accepted,
            "status" => true
        ],200);
    }


    public function history(){
       

        $booking_data = Booking::where("labour_id",auth()->user()->id)->latest()->get();

        return response([
            "data" => $booking_data,
            "status" => true
        ],200);
    }

    public function acceptedBooking(){
        $data = Booking::where("user_id",auth()->user()->id)->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function rejectedBooking(){
        $data = Booking::where("user_id",auth()->user()->id)->get();
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
