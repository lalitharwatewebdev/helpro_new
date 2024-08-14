<?php

namespace App\Http\Controllers\Api\v1\Labour\Auth;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{

    public function OtpLogin(Request $request){

        $request->validate([
            // "phone" => "required|numeric",
            // "device_id" => "required"
        ]);

        $type = "old";

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
        $phone = substr($firebase_user->phoneNumber, 3);
        $user = User::where('phone', $request->phone)->where("type","labour")->first();
        // return $user;
        
        if(!empty($user)){
            if($user->name == null){
                $type = "new";
            }
            $token = $user->createToken("user")->plainTextToken;
            $user->update([
                "device_id" => $request->device_id,
                "type" => "labour"
            ]);

            return response([
                "data" => $user,
                "token" => $token,
                "type" => $type,
                "status" => true
            ],200);
        }
        else{
           
                $data = User::create([
                    "phone" => $request->phone,
                    "device_id" => $request->device_id,
                    "type" => "labour"
                ]);
                $token = $data->createToken("user")->plainTextToken;
    
                return response([
                    "data" => $data,
                    "token" => $token,
                    "type" => "new",
                    "status" => true
                ],200);
            }
           
        

        

    }


    public function signUp(Request $request){
        $data = User::find(auth()->user()->id);
        $data->name = $request->name;        
        $data->email = $request->email;
        $data->state = $request->state;
        $data->phone = $request->phone;
        if($request->profile_image){
            $data->profile_image = FileUploader::uploadFile($request->profile_image,"images/profile_pic");
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
        $data->category = $request->category_id;
        $data->category()->attach($request->category);
        $data->gender = strtolower($request->gender);
        $data->lat_long = $request->lat_long;
        $data->address = $request->address;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->preferred_shift = strtolower($request->preferred_shifts);
        $data->rate_per_day = $request->rate_per_day;
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
