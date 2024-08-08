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
            $order = $this->api->order->create([
                "amount" =>  $amount * 100 ,
                "currency" => $currency,
                "receipt" => $receipt,
                "payment_capture" => 1,
                "notes" => $note
                ]);

            return $order;
        }
        catch(\Exception $e){
            return response([
                "message" => "Something went wrong " . $e,
                "status" => false
            ],400);
        }
    }

    public function fetchOrder($order_id){
        try{    
          $order_id =  $this->api->order->fetch($order_id);

            $status = ['paid',"created"];

            if(in_array($order_id->status,$status)){
                return response([
                    "message" => "Tranasction Successful",
                    "status" => true
                ],200);
            }
            else{
                return response([
                    "message" => "Tranasction Failure",
                    "status" => false
                ],400);
            }
        }

        catch(\Exception $e){
            return response([
                "message" => "Something went wrong " . $e,
                "status" => false
            ],400);
        }
    }
}