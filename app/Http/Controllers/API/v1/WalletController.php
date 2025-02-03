<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\RazorPayModel;
use App\Models\Transactions;
use App\Models\Wallet;
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

        $amount       = $request->amount;
        $wallet_order = $this->razorpay->createOrder($amount);

        $order                  = new RazorPayModel();
        $order->user_id         = $request->user()->id;
        $order->payment_gateway = "razorpay";
        $order->amount          = $amount;
        $order->order_id        = $wallet_order['razorpay_order_id'] ?? '';
        $order->note            = "wallet";
        $order->save();

        // \Log::info($wallet_order);

        return response([
            "data" => $wallet_order['razorpay_order_id'] ?? '',
        ], 200);

    }

    public function fetchAmount(Request $request)
    {
        $order_id = $request->order_id;

        $fetch_wallet_order = $this->razorpay->fetchOrder($order_id);

        $order_data = RazorPayModel::where('order_id', $order_id)->first();

        if ($fetch_wallet_order) {
            $user_wallet = Wallet::where('user_id', $request->user()->id)->first();
            if (! empty($user_wallet)) {
                $user_wallet_amount  = (int) ($user_wallet->amount ?? 0) + (int) ($order_data->amount ?? 0);
                $user_wallet->amount = $user_wallet_amount;
                $user_wallet->save();
            } else {
                $user_wallets          = new Wallet();
                $user_wallets->amount  = $order_data->amount;
                $user_wallets->user_id = $request->user()->id;

                $user_wallets->save();
            }

            $user_transactions                   = new Transactions();
            $user_transactions->amount           = $order_data->amount;
            $user_transactions->user_id          = $request->user()->id;
            $user_transactions->remark           = "added money";
            $user_transactions->transaction_type = "credited";

            $user_transactions->save();

            return response([
                "message" => "Amount Added to the wallet",
                "status"  => true,
            ], 200);
        }

        return response([
            "message" => "Transaction Failure",
            "status"  => false,
        ], 400);
    }

    public function walletTransaction(Request $request)
    {
        $user              = auth()->user()->id;
        $user_transactions = Transactions::where("user_id", $user)->latest()->get();

        return response([
            "data"   => $user_transactions,
            "status" => true,
        ], 200);
    }

}
