<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;
use App\Models\User;
use App\Models\Wallet;

class WalletController extends Controller
{
    protected $razorpay;

    public function __construct(RazorpayServiceProvider $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    public function redeemAmount(Request $request){
        $request->validate([
            "amount" => "required", 
        ]);
        $redeem_amount = $request->amount;

        $wallet = Wallet::where("user_id",auth()->user()->id)->first();

        $user = User::find(auth()->user()->id);

        $account_number = $user->account_number;
        $ifsc_code = $user->ifsc_code;

        if($redeem_amount > $wallet->amount){
            return response([
                "message" => "Trying to Redeem more than Available Amount",
                "status" => true
            ],200);
        }
        
        $redeem  = $this->razorpay->createPayout($redeem_amount,$account_number,$ifsc_code);

        return $redeem;

    }
}
