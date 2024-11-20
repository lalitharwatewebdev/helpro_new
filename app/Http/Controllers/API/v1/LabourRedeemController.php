<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\LabourRedeem;
use App\Models\Wallet;
use Illuminate\Http\Request;

class LabourRedeemController extends Controller
{
    public function redeemAmount(Request $request)
    {
        $wallet = Wallet::where("user_id", auth()->user()->id)->first();
        // $total_amount = AcceptedBooking::where("labour_id", auth()->user()->id)->sum("amount");
        // $wallet->amount = $total_amount;

        if (!$wallet || $wallet->amount < $request->amount) {
            return response([
                "message" => "No Sufficient amount in Wallet",
                "status" => false,
            ], 200);
        }

        $wallet->decrement("amount", intval($request->amount));

        LabourRedeem::create([
            "amount" => $request->amount,
            "labour_id" => auth()->user()->id,
        ]);

        return response([
            "message" => "Redeeming Amount will be transferred to your bank account shortly",
            "status" => true,
        ], 200);
    }

    public function getHistory(Request $request)
    {
        $data = LabourRedeem::where("labour_id", auth()->user()->id)->latest()->get();

        return response([
            "data" => $data,
            "status" => true,
        ], 200);
    }
}
