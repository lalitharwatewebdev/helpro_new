<?php

namespace App\Http\Controllers\API\v1;
use App\Models\User;
use App\Models\Wallet;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\ReferralMaster;
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
            // if referral code user is present in db increment his wallet
            $wallet = Wallet::where("user_id",$user->id)->first();

            if($wallet){

                Wallet::find($wallet->id)->increment("amount",$referral_amount);
            }
            else{
                Wallet::create([
                    "user_id" => $user->id,
                    "amount" => $referral_amount->value
                ]);
            }

            // and also increment the authenticated user wallet
            $user_wallet = Wallet::where("user_id",auth()->user()->id)->first();
            if($user_wallet){
                Wallet::find($user_wallet->id)->increment("amount",$referral_amount);
            }
            else{
                Wallet::create([
                    "user_id" => auth()->user()->id,
                    "amount" => $referral_amount->value
                ]);
            }

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
