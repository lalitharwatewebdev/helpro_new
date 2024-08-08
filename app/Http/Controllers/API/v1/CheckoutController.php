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

        if($fetchOrder->status() == 200){
            Cart::where("user_id",auth()->user()->id)->whereHas("labour_id",$fetchOrder->labour_id)->delete();
            
            if(isset($fetchOrder->labour_id)){
                Booking::where("user_id",auth()->user()->id)->whereHas([$fetchOrder->labour_id]);
            }
        }

       
      
        return $fetchOrder;
    }
}
