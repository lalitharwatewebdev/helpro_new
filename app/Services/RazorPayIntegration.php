<?php
namespace App\Services;

use Razorpay\Api\Api;

class RazorPayIntegration
{
    public static function createOrder($amount)
    {
        \Log::info(env('RAZORPAY_KEY'));
        \Log::info(env('RAZORPAY_SECRET'));
        try {
            $api      = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $response = $api->order->create([
                'amount'   => $amount * 100,
                'currency' => 'INR',
                'notes'    => [
                    'message' => 'test',
                ],
            ]);
            return [
                'success'           => true,
                'razorpay_order_id' => $response->id,
            ];
        } catch (\Exception $th) {
            \Log::info($th);
            return [
                'success'       => false,
                'error_message' => $th,
            ];
        }
    }

    public static function fetchOrder($orderId)
    {
        try {
            $api      = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $response = $api->order->fetch($orderId);
            // return $response;
            return [
                'success' => true,
                'status'  => $response->status,
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                'success' => false,
                'errors'  => 'Issue while fetching the order',
            ];
        }
    }
}
