<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Razorpay\Api\Api;

class LabourRazorPayServiceProvider extends ServiceProvider
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(env("RAZORPAY_KEY"), env("RAZORPAY_SECRET"));
    }

    public function createOrder($amount){
        $ordering_id = "order_id".uniqid();

        try{
            $order= $this->api->order->create([
                "amount" => $amount * 100,
                "currency" => "INR",
                "receipt" => $ordering_id
            ]);

            return $order->toArray();
        }
        catch(\Exception $e){
            return response([
                "message" => "Something went wrong " . $e->getMessage(),
                "status" => false
            ],400);
        }
    }

    public function fetchOrder($order_id){
        try{
            $order =  $this->api->order->fetch($order_id)->toArray();

        

            $status = ["captured"];

            if (in_array($order['status'], $status)) {
                return [
                    "message" => "Order Placed Successfully",
                    "order_id," => $order["id"],
                    "status" => $order['status']
                ];
            }

        }
        catch(\Exception $e){
            return response([
                "message" => "Something went wrong ". $e->getMessage(),
                "status" => true
            ],400);
        }
    }
}
