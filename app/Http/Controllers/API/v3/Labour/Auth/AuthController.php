<?php

namespace App\Http\Controllers\API\v3\Labour\Auth;

use App\Helpers\OTPGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// models
use App\Models\OTP;

class AuthController extends Controller
{
    public function generateOTP(Request $request){
        $request->validate([
            "phone" => "required"
        ]);

        // for testing purpose
        if($request->phone == "8111111111"){
            return response([
                "phone" => $request->phone,
                "message" => "OTP send to your mobile number",
                "status" => true
            ],200);
        }

        $otp = OTPGenerator::generate();

        $data = new OTP();

        $data->phone = $request->phone;
        $data->generated_otp = $otp;
        $data->type = "labour";
        $data->save();

        OTPGenerator::sendMessage($otp,$request->phone);

        return response([
            "message" => "OTP has been send to your mobile number",
            "status" => true
        ],200);
    }
}
