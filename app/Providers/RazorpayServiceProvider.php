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
            // return $order_id;
          $order =  $this->api->order->fetch($order_id)->toArray();
         

          
        //   return $order['id'];

            $status = ['paid',"captured"];

            if(in_array($order['status'],$status)){

                return [
                    "message" => "Order Placed Successfully",
                    "checkout_id" => $order['notes']['checkout_id'],            
                    "status" => true

                ];
             

                // return $order_id;
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