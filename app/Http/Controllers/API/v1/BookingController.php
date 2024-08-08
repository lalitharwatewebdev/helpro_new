<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RazorpayServiceProvider;

class BookingController extends Controller
{   
    protected $razorpay;
    public function __construct(RazorpayServiceProvider $razorpay){
        $this->razorpay = $razorpay;
    }

    public function createOrder(Request $request){
        $order = $this->razorpay->createOrder($request->amount);

        return response([
            "order_id" => $order
        ]);
    }
}
