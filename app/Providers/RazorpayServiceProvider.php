<?php

namespace App\Providers;

use App\Models\RazorPayModel;
use App\Models\Transactions;
use App\Models\Wallet;
use Razorpay\Api\Api;
use Razorpay\Api\Payout;

class RazorpayServiceProvider
{
    protected $api;
    public function __construct()
    {
        $this->api = new Api(env("RAZORPAY_KEY"), env("RAZORPAY_SECRET"));
    }

    public function createOrder($amount, $currency = "INR", $checkout_id)
    {
        \Log::info($amount);
        $receipt = "receipt_id" . time();
        $amountInRupees = (float) $amount; // Assuming amount is sent in rupees
        $amounts = intval($amountInRupees * 100);
        \Log::info($amounts);

        $note = [
            "user" => auth()->user()->id,
            "amount" => (float) $amount,
            "checkout_id" => $checkout_id,
        ];

        try {
            $order = $this->api->order->create([
                "amount" => (float) $amounts,
                "currency" => $currency,
                "receipt" => $receipt,
                "payment_capture" => 1,
                "notes" => $note,
            ]);

            return $order;
        } catch (\Exception $e) {
            return response([
                "message" => "Something went wrong " . $e,
                "status" => false,
            ], 400);
        }
    }

    public function fetchOrder($order_id)
    {
        try {
            $order = $this->api->order->fetch($order_id)->toArray();
            $status = ['paid', "captured", "created"];

            if (in_array($order['status'], $status)) {

                return [
                    "message" => "Order Placed Successfully",
                    "checkout_id" => $order['notes']['checkout_id'],
                    "status" => true,

                ];

                // return $order_id;
            } else {
                return response([
                    "message" => "Tranasction Failure",
                    "status" => false,
                ], 400);
            }
        } catch (\Exception $e) {
            return response([
                "message" => "Something went wrong " . $e,
                "status" => false,
            ], 400);
        }
    }

    public function createWalletOrder($amount)
    {
        $receipt = "receipt_id" . time();

        try {
            $order = $this->api->order->create([
                "amount" => $amount * 100,
                "currency" => "INR",
                "receipt" => $receipt,

            ]);

            RazorPayModel::create([
                "user_id" => auth()->user()->id,
                "payment_gateway" => "razorpay",
                "order_id" => $order['id'],
                "amount" => $amount,

            ]);

            return $order;
        } catch (\Exception $e) {
            return response([
                "message" => "Something went wrong " . $e,
                "status" => false,
            ], 400);
        }
    }

    public function fetchWalletOrder($order_id)
    {
        $fetch_order = $this->api->order->fetch($order_id);

        $status = ['paid', "captured", 'created'];

        if (in_array($fetch_order['status'], $status)) {

            // adding to transactions table
            $amount = $fetch_order['amount'] / 100;
            $transactions = Transactions::create([
                "user_id" => auth()->user()->id,
                "amount" => $amount,
                "transaction_type" => "credited",
                "remark" => "added money",
            ]);

            $wallet = Wallet::where("user_id", auth()->user()->id)->first();

            if ($wallet) {
                $wallet->increment("amount", $amount);
            } else {
                Wallet::create([
                    "user_id" => auth()->user()->id,
                    "amount" => $amount,

                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    public function createPayout($amount, $account_number, $ifsc_code)
    {
        $payout = $this->api->payout->create([
            "amount" => $amount * 100,
            "currency" => "INR",
            "method" => "bank_account",
            "bank_account" => [
                "account_number" => $account_number,
                "ifsc_code" => $ifsc_code,
            ],
            "description" => "Payment to " . $account_number,
        ]);

        return $payout->id;
    }
}
