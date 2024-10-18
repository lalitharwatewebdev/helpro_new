<?php

namespace App\Http\Controllers\API\v2\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// models
use App\Models\User;
use App\Models\OTP;
// helpers
use App\Helpers\FileUploader;
use App\Helpers\OTPGenerator;

// package
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    public function register(Request $request)    {

        // \Log::info($request->all());

        // if (!empty($request->image)) {
        //     $image = FileUploader::uploadFile($request->file('image'), 'images/usersimage');
        // }

        // $user = User::create([
        //     'first_name' => $request->first_name,
        //     'second_name' => $request->second_name,
        //     'email' => $request->email,
        //     'phone' => $request->phone,
        //     'image' => $image,
        //     'address' => $request->address,
        //     'gender' => $request->gender,
        //     'password' => Hash::make($request->password),
        //     'device_id' => $request->device_id,
        //     'firebase_uid' => $request->fuid
        // ]);
        // $token = $user->createToken('myapptoken')->plainTextToken;

        // $response = [
        //     'user' => $user,
        //     'token' => $token
        // ];
        // return response($response, 201);

        Log::info("User Registeration details:: ", $request->all());

        $user = User::find(auth()->user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        
        if($request->image){
            $user->image = FileUploader::uploadFile($request->image,"/images/user-image");
        }

        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
    }

  

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


        // $res =   OTPGenerator::sendMessage($otp, $request->phone);

        return response([
            "message" => "OTP send to your Mobile Number",
            "status" => true
        ], 200);
    }


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
                        "device_id" => $request->device_id
                    ]);
                    $token = $user->createToken("user-otp-login")->plainTextToken;
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
                "message" => "User Authenticated",
                "status" => true,
                "type" => "new"
            ]);
        }
       
    }


    // public function googleLogin(Request $request)
    // {
    //     Log::info($request->all());
    //     $request->validate([
    //         'token' => 'required|string',
    //         'device_id' => 'required|string',
    //     ]);
        
    //     $auth = app('firebase.auth');
    //     try {
    //         $verifiedIdToken = $auth->verifyIdToken($request->token);
    //     } catch (FailedToVerifyToken $e) {
    //         return response()->json([
    //             'message' => 'The token is invalid: ' . $e->getMessage(),
    //         ], 401);
    //     }
    //     $uid = $verifiedIdToken->claims()->get('sub');
    //     $firebase_user = $auth->getUser($uid);
    //     $email = $firebase_user->email;
    //     $user = User::where('email', $email)->first();

    //     $type = "old";
    //    if($user){
    //     if($user->name != null){
    //         $user->update([
    //             "device_id" => $request->device_id
    //         ]);
    //     }
    //     else{
    //         $type = "new";

    //     }
    //    }
    // }

    public function logOut()
    {
        auth("sanctum")->user()->id->tokens()->delete();
        return response([
            "message" => "Logout Successfully",
            "status" => true
        ], 200);
    }

  
}
