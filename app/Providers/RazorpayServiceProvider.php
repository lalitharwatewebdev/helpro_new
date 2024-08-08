<?php

namespace App\Services;

use App\Models\RazorPayModel;
use Razorpay\Api\Api;

class RazorpayServiceProvider{
    protected $razorpay;
    public function __construct(){
        $this->razorpay = new Api(env("RAZORPAY_KEY"),env("RAZORPAY_SECRET"));
    }

    public function createOrder($amount,$currency="INR"){
        $receipt = "receipt_id".time();

        $note = [
            "user" => auth()->user()->id,
            "amount" => $amount,
        ];
        try{
           $order =  $this->razorpay->order->create([
                "amount" => intval($amount * 100),
                "currency" => $currency,
                "receipt" => $receipt,
                "payment_capture" => 1,
                "notes" => $note
                
                ]);

                RazorPayModel::create([
                    "user_id" => auth()->user()->id,
                    "order_id" => $order->id,
                    "payment_gateway" => "razorpay",
                    "amount" => $amount,
                    "note" => $note
                ]);

                return response([
                    "order_id" => $order->id,
                    "status" => true
                ],200);
        }
        catch(\Exception $e){
            return response([
                "message" => "Something went wrong " . $e,
                "order_id" => $order->id,
                "status" => false
            ],400);
        }
    }

    // public function fetchOrder($order_id){
    //     try{    
    //         $this->razorpay->order->fetch($order_id)->toArray();

    //         // $status = ['paid']
    //     }

    //     catch(\Exception $e){
    //         return response([
    //             "message" => "Something went wrong " . $e,
    //             "status" => false
    //         ],400);
    //     }
    // }
}