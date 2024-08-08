<?php

namespace App\Providers;


use Razorpay\Api\Api;

class RazorpayServiceProvider{
    protected $api;
    public function __construct(){
        $this->api = new Api(env("RAZORPAY_KEY"),env("RAZORPAY_SECRET"));
    }

    public function createOrder($amount,$currency="INR"){
        $receipt = "receipt_id".time();

        $note = [
            "user" => auth()->user()->id,
            "amount" => $amount,
        ];
        try{
           return $this->api->order->create([
                "amount" => intval($amount * 100),
                "currency" => $currency,
                "receipt" => $receipt,
                "payment_capture" => 1,
                "notes" => $note
                
                ]);

                

               
        }
        catch(\Exception $e){
            return response([
                "message" => "Something went wrong " . $e,
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