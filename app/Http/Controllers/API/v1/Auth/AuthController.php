<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Helpers\FileUploader;
use App\Helpers\OTPGenerator;
use App\Http\Controllers\Controller;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    // tested and working
    public function register(Request $request)
    {
     
        $user = User::find(auth()->user()->id);
        $user->name = $request->username;
        $user->email = $request->email;
        
        if($request->profile_pic){
            
        $user->profile_pic = FileUploader::uploadFile($request->profile_pic,"images/profile_pic");
        }
        $user->gender = $request->gender;
        $user->save();

        return response([
            "message" => "User Data Saved Successfully",
            "status" => true
            ],200);
    }
    
    //remove comment to send otp and working
     public function generateOTP(Request $request)
    {
        $request->validate([
            "phone" => "required",
        ]);

        if ($request->phone == "8111111111" || $request->phone == '711111111') {

            return response([
                "phone" => $request->phone,
                "message" => "OTP send to your Mobile Number now",
                "status" => true

            ], 200);
        }

        $otp = OTPGenerator::generate();

        $data = new OTP();

        $data->phone = $request->phone;
        $data->generated_otp = $otp;

        $data->save();

        \Log::info("Generated OTP:: ". $data->generated_otp);
        OTPGenerator::sendMessage($otp, $request->phone);

        return response([
            "message" => "OTP send to your Mobile Number",
            "status" => true,
        ], 200);
    }

    //tested and working
    public function OTPLogin(Request $request)
    {

         \Log::info("User Login Details:: ",$request->all());
         
      
        $request->validate([
            "phone" => "required",
            "otp" => "required",
            "device_id" => "required|string"
        ]);

        $user = User::where("phone", $request->phone)->where("type","user")->first();
        
        $otp = OTP::where("phone", $request->phone)->latest()->first();
        
        if($user){
            if($otp->generated_otp == $request->otp){
                if($user->name != null){
                    $user->update([
                        "device_id" => $request->device_id,
                    ]);
                    $token = $user->createToken("user-otp-login")->plainTextToken;
                    return response([
                        "message" => "User Authenticated Successfully",
                        "token" => $token,
                        "type" => "old",
                        "status" => true
                    ],200);
                }
                if($user->name == null){
                 
                    $user->update([
                        "device_id" => $request->device_id
                    ]);
                    $token = $user->createToken("user-otp-login")->plainTextToken;
                    return response([
                        "message" => "User Authenticated Successfully",
                        "token" => $token,
                        "type" => "new",
                        "status" => true
                    ],200); 
                }
            }
            else{
                return response([
                    "message" => "Invalid OTP",
                    "status" => true
                ],400);
            }
        }
        else{
            $user = new User();
            $user->phone = $request->phone;
            $user->device_id = $request->device_id;
             $user->type = 'user';
            $user->save();

            $token = $user->createToken("user-otp-login")->plainTextToken;

            return response([
                "token" => $token,
                "message" => "User Authenticated",
                "status" => true,
                "type" => "new"
            ]);
        }
    }

    public function googleLogin(Request $request)
    {
        
        \Log::info($request->all());
       
        
        $t = $request->validate([
            'token' => 'required|string',
            'device_id' => 'required|string',
        ]);
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
        $user = User::where('email', $email)->where("type","user")->first();

      
        if (!empty($user)) {
            if($user->name != null){
            $user->update([
                'device_id' => $request->device_id,
            ]);
            $token = $user->createToken("user-google-login")->plainTextToken;
                
            return response([
                "message" => "User Authenticated Successfully",
                "token" => $token,
                "type" => "old",
                "status" => true
                ],200);
            
            }
            else{
               $user->update([
                   "device_id" => $request->device_id
                   ]);
                   
                $token = $user->createToken("user-google-login")->plainTextToken;
                
                return response([
                    "message" => "User Authenticated",
                    "status" => true,
                    "type" => "new",
                    "token" => $token
                    ],200);
            }
        } else {
            $user = new User();
                $user->email = $email;
                $user->device_id = $request->device_id;
                $user->type = "user";
                $user->save();
                
                $token = $user->createToken("user-google-login")->plainTextToken;
                
                return response([
                    "message" => "User Authenticated",
                    "status" => true,
                    "type" => "new",
                    "token" => $token
                    ],200);
        }
       
    }

    public function logOut()
    {
        auth("sanctum")->user()->id->tokens()->delete();
        return response([
            "message" => "Logout Successfully",
            "status" => true,
        ], 200);
    }

   
}
