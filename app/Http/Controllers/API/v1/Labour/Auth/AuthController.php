<?php

namespace App\Http\Controllers\API\v1\Labour\Auth;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\OTPGenerator;
use App\Models\User;
use App\Models\OTP;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{

    // public function OtpLogin(Request $request){
    //     \Log::info($request->all());
    //     $request->validate([
    //         // "phone" => "required|numeric",
    //         // "device_id" => "required"
    //     ]);

    //     $type = "old";

    //       $auth = app('firebase.auth');
    //     try {
    //         $verifiedIdToken = $auth->verifyIdToken($request->token);
    //     } catch (FailedToVerifyToken $e) {
    //         return response()->json([
    //             'message' => 'The token is invalid: ' . $e->getMessage(),
    //         ], 401);
    //     }
    //     $uid = $verifiedIdToken->claims()->get('sub');
    //     $firebase_user = $auth->getUser($uid);
    //     $phone = substr($firebase_user->phoneNumber, 3);
    //     $user = User::where('phone', $request->phone)->where("type","labour")->first();
    //     // return $user;
        
    //     if(!empty($user)){
    //         if($user->name == null){
    //             $type = "new";
    //         }
    //         $token = $user->createToken("user")->plainTextToken;
    //         $user->update([
    //             "device_id" => $request->device_id,
    //             "type" => "labour"
    //         ]);

    //         return response([
    //             "data" => $user,
    //             "token" => $token,
    //             "type" => $type,
    //             "status" => true
    //         ],200);
    //     }
    //     else{
           
    //             $data = User::create([
    //                 "phone" => $request->phone,
    //                 "device_id" => $request->device_id,
    //                 "type" => "labour"
    //             ]);
    //             $token = $data->createToken("user")->plainTextToken;
    
    //             return response([
    //                 "data" => $data,
    //                 "token" => $token,
    //                 "type" => "new",
    //                 "status" => true
    //             ],200);
    //         }
    // }
    
     public function generateOTP(Request $request)
    {
    
        $request->validate([
            "phone" => "required"
        ]);

        if ($request->phone == "8111111111" || $request->phone == '711111111') {

            return response([
                "phone" => $request->phone,
                "message" => "OTP send to your Mobile Number",
                "status" => true

            ], 200);
        }

        $otp = OTPGenerator::generate();

        $data = new OTP();

        $data->phone = $request->phone;
        $data->generated_otp = $otp;

        $data->save();


        $res =   OTPGenerator::sendMessage($otp, $request->phone);
       
            return response([
                "message" => "OTP send to your Mobile Number",
                "status" => true
            ], 200);
       


    }
    
    
     public function OTPLogin(Request $request)
    {
        
       
        $request->validate([
            "phone" => "required",
            "otp" => "required"
        ]);
        \Log::info($request->device_id);
        $type = "old";  

        $user = User::where("phone", $request->phone)->where("type","labour")->first();



        $otp = OTP::where("phone", $request->phone)->latest()->first();
        
       

        if (!empty($user)) {

            if ($otp->generated_otp == $request->otp) {
                if ($user->name == null) {
                    $type = 'new';

                    $user->update([
                        "device_id" => $request->device_id
                    ]);
                    $token = $user->createToken("otp_login")->plainTextToken;

                    return response([
                        "message" => "User Authenticated",
                        "token" => $token,
                        "type" => $type,
                        "status" => true,

                    ], 200);
                } else {
                    $type = 'old';
                    $token = $user->createToken("otp_login")->plainTextToken;

                    return response([
                        "message" => "User Authenticated",
                        "token" => $token,
                        "type" => $type,
                        "status" => true,

                    ], 200);
                }
            } else {
                return response([
                    "message" => "Invalid OTP",
                    "status" => false
                ], 400);
            }
        } else {

            if ($otp->generated_otp == $request->otp) {

                $user = User::create([
                    "phone" => $request->phone,
                    "device_id" => $request->device_id,
                    "type" => "labour"
                ]);
                $token = $user->createToken("otp_login")->plainTextToken;

                $type = "new";

                return response([
                    "type" => $type,
                    "token" => $token,
                    "message" => "User Created Successfully",
                    "status" => true,
                ], 200);
            } else {
                $type = "new";
                return response([
                    "type" => $type,
                    "message" => "Invalid OTP",
                    "status" => false
                ], 400);
            }
        }
    }
    
    
      public function googleLogin(Request $request)
     {
        // return $request->all();
        $t = $request->validate([
            'token' => 'required|string',
            'device_id' => 'required|string',
        ]);
        // return $t;
        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($request->token);
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'message' => 'The token is invalid: ' . $e->getMessage(),
            ], 401);
        }
        $uid = $verifiedIdToken->claims()->get('sub');
        $firebase_user = $auth->getUser($uid);
        $email = $firebase_user->email;
        $user = User::where('firebase_uid', $firebase_user->uid)->orWhere('email', $email)->first();

        $type = 'old';
        // if (!empty($user)) {
        //     if ($user->name != null && $user->phone != null) {
        //         $user->update([
        //             'device_id' => $request->device_id,
        //             'firebase_uid' => $uid,
        //         ]);
        //     } else {
        //         $type = 'new';
        //     }
        // } else {
        //     $user =  User::create([
        //         'email' => $email,
        //         'device_id' => $request->device_id,
        //         'firebase_uid' => $uid,
        //     ]);
        //     $type = 'new';
        // }
        $type='old';
        if(!empty($user)){
            $user->update([
                'device_id' => $request->device_id,
                    'firebase_uid' => $uid,
            ]);

        }
        else{
            $user =  User::create([
                        'email' => $email,
                        'device_id' => $request->device_id,
                        'firebase_uid' => $uid,
                        "type" => "labour"
                    ]);
                    $type = 'new';
        }
        return response([
            'type' => $type,
            'user_data' => $user,
            'token' => $user->createToken('user')->plainTextToken,
        ]);
    }


    public function signUp(Request $request){
        $data = User::find(auth()->user()->id);
        // return $data;
        $data->name = $request->name;        
        $data->email = $request->email;
        $data->state = $request->state;
        $data->city = $request->city;
        if($request->profile_image){
            $data->profile_pic = FileUploader::uploadFile($request->profile_image,"images/profile_pic");
        }
        if($request->aadhaar_card_front){
            $data->aadhaar_card_front = FileUploader::uploadFile($request->aadhaar_card_front,"images/aadhaar_card");
        }
        if($request->aadhaar_card_back){
            $data->aadhaar_card_back = FileUploader::uploadFile($request->aadhaar_card_back,"images/aadhaar_card");
        }
        $data->aadhaar_number = $request->aadhaar_number;
        $data->pan_card_number = $request->pan_number;
        $data->bank_name = $request->bank_name;
        $data->branch_address = $request->bank_address;
        $data->pan_card_number = $request->pan_number;
        if($request->pan_front){
            $data->pan_front = FileUploader::uploadFile($request->pan_front,"images/pan_card");
        }
        // $data->category = $request->category_id;
        $data->category()->attach($request->category_id);
        $data->gender = strtolower($request->gender);
        $data->lat_long = $request->lat_long;
        $data->address = $request->address;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->IFSC_code = $request->ifsc_code;
        $data->otp = mt_rand(111111,999999);
        // $data->preferred_shift = strtolower($request->preferred_shifts);
        $data->availability = $request->days_available;
        $data->qualification = $request->qualification;
        // $data->rate_per_day = $request->rate_per_day;
        $data->account_number = $request->account_number;
        $data->save();

        return response([
            "message" => "Labour Data Saved Successfully",
            "status" => true
        ],200);

    }

    public function logOut(Request $request){
        auth('sanctum')->user()->id->tokens()->delete();

        return response([
            "message" => "Labour Logout Successfully",
            "status" => true
        ],200);
    }   
}
