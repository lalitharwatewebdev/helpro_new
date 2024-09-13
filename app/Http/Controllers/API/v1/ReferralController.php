<?php

namespace App\Http\Controllers\API\v1;
use App\Models\User;
use App\Models\Wallet;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\ReferralMaster;
use App\Models\Transactions;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function addReferral(Request $request){
        $referral_code = $request->referral_code;

        $referral_amount = BusinessSetting::where("key","referral_amount")->first();
        
        
        $user = User::where("referral_code",$referral_code)->where("type","user")->first();

        $is_refer = ReferralMaster::where("referral_user_id",$user->id)->where("user_id",auth()->user()->id)->first();

        if($is_refer){
            return response([
                "message" => "Referral Code Already Redeemed",
                "status" => true
            ],200);
        }
        else{
            ReferralMaster::create([
                "referral_user_id" => $user->id,
                "user_id" => auth()->user()->id
            ]);
        }

        if($user->referral_code == auth()->user()->referral_code){
            return response([
                "message" => "You cannot use this referral code",
                "status" => false
            ],400);
        }

        if($user){
            // adding to authenticated user
            Transactions::create([
            "amount" => $referral_amount,
            "transaction_type" => "credited",
            "user_id" => auth()->user()->id,
            "remark" => "Added to wallet via referral"
            ]);

            // adding to referral user
            Transactions::create([
                "amount" => $referral_amount,
                "transaction_type" => "credited",
                "remark" => "Added to wallet via referral",
                "user_id" => $user->id
            ]);
            return response([
                "message" => "Wallet Incremented",
                "status" => true
            ],200);
        }



     

      
        else{
            return response([
                "message" => "Invalid Referral Code",
                "status" => false
            ],400);
        }
    }
}
