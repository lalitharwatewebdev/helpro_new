<?php

namespace App\Providers;


use Razorpay\Api\Api;

class RazorpayServiceProvider{
    protected $api;
    public function __construct(){
        $this->api = new Api(env("RAZORPAY_KEY"),env("RAZORPAY_SECRET"));
    }

    public function createOrder($amount,$currency="INR",$checkout_id){
        $receipt = "receipt_id".time();

    

        $note = [
            "user" => auth()->user()->id,
            "amount" => $amount,
            "checkout_id" => $checkout_id
        ];

        

        try{
            $order = $this->api->order->create([
                "amount" =>  $amount  ,
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
          $order_id =  $this->api->order->fetch($order_id)->toArray();
          
     

            $status = ['paid',"captured",'created'];

            if(in_array($order_id['status'],$status)){
                return response([
                    "message" => "Order Placed Successfully",
                    "labour_id" => $order_id['notes']['checkout_id'],
                    "order_id" => $order_id,
                    
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