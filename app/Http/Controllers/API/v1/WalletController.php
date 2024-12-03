<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Providers\RazorpayServiceProvider;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $razorpay;
    public function __construct(RazorpayServiceProvider $razorpay)
    {
        $this->razorpay = $razorpay;
    }
    public function createAmount(Request $request)
    {
        \Log::info($request->all());

        $amount = $request->amount;
        $wallet_order = $this->razorpay->createWalletOrder($amount);
        // \Log::info($wallet_order);

        return response([
            "data" => $wallet_order['id'] ?? '',
        ], 200);

    }

    public function fetchAmount(Request $request)
    {
        $order_id = $request->order_id;

        $fetch_wallet_order = $this->razorpay->fetchWalletOrder($order_id);

        if ($fetch_wallet_order) {
            return response([
                "message" => "Amount Added to the wallet",
                "status" => true,
            ], 200);
        }

        return response([
            "message" => "Transaction Failure",
            "status" => false,
        ], 400);
    }

    public function walletTransaction(Request $request)
    {
        $user = auth()->user()->id;
        $user_transactions = Transactions::where("user_id", $user)->latest()->get();

        return response([
            "data" => $user_transactions,
            "status" => true,
        ], 200);
    }

}
