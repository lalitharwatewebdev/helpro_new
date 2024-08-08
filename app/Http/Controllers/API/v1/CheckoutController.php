<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;
use App\Models\Cart;
use App\Models\Booking;


class CheckoutController extends Controller
{
    protected $razorpay;
    public function __construct(RazorpayServiceProvider $razorpay){
        $this->razorpay = $razorpay;
    }

    public function randomNumber(){
        $random = 0;
        for($i=0;$i<6;$i++){
            $random += rand(0,9);
        }

        return $random;
    }

    public function store(Request $request){
        $request->validate([
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required"
        ]);

        $user_cart = Cart::with("labour:id,rate_per_day")->where("user_id",auth()->user()->id)->select("labour_id")->get();
        // calculating time difference
        $labour_arr = array();
        // return $user_cart;
        $diff = (strtotime($request->end_date) - strtotime($request->start_date));

        $date_result = abs(round($diff)/86400) + 1;

        $total_labour_amount =0;
        $booking = '';

        foreach($user_cart as $cart){
            $booking = new Booking();
            $labour_arr[] = $cart->labour_id;
            $booking->user_id = auth()->user()->id;
            $booking->labour_id = $cart->labour_id;
            $total_labour_amount += intval(round($cart->labour->rate_per_day)) * $date_result;
            $booking->total_amount = intval(round($cart->labour->rate_per_day)) * $date_result ;
            $booking->save();
        }
        $order = $this->razorpay->createOrder($total_labour_amount,"INR",$labour_arr)->toArray();

        $data = new Checkout();

        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->address_id = $request->address_id;
        $data->note = $request->note;

        $data->save();

        return response([
            "message" => "Checkout created successfully",
            "order_id" => $order["id"],
            "status" => true
        ],200);
    }

    public function fetchOrder(Request $request){
        $request->validate([
            "order_id" => "required"
        ]);

        $fetchOrder = $this->razorpay->fetchOrder($request->order_id);

            $labour_id = Cart::where("user_id",auth()->user()->id)->get();
        
            $labour_data =  $labour_id->pluck("labour_id")->toArray();
            

            Cart::where("user_id",auth()->user()->id)->delete();

        // adding to booking page
        Booking::where("user_id",auth()->user()->id)->whereIn("labour_id",$labour_data)->update([
            "payment_status" => "captured",
            "otp" => $this->randomNumber()
        ]);
            return response([
                "message" => "Booking Done Successfully"
            ],200);
        }

    
}
