<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;


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

        $order = $this->razorpay->createOrder($request->amount)->toArray();

       

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
            "order" => $order['id'],
            "status" => true
        ],200);
    }
}
