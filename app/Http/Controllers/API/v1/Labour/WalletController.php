<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;
use App\Models\User;



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

        $wallet_amount = Transactions::where("user_id",auth()->user()->id)->where("transaction_type","credited")->sum("amount");

        $user = User::find(auth()->user()->id);

        $account_number = $user->account_number;
        $ifsc_code = $user->ifsc_code;

        if($redeem_amount > $wallet_amount){
            return response([
                "message" => "Trying to Redeem more than Available Amount",
                "status" => true
            ],200);
        }
        
        $redeem  = $this->razorpay->createPayout($redeem_amount,$account_number,$ifsc_code);

        return $redeem;

    }

    public function transactions(){
        $wallet_transaction = Transactions::where("user_id",auth()->user()->id)->latest()->get();

        return response([
            "data" => $wallet_transaction,
            "status" => true
        ],200);
        
    }

}
