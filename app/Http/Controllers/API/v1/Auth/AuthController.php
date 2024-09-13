<?php

namespace App\Http\Controllers\API\v1\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use App\Models\OTP;
use App\Helpers\OTPGenerator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        if (!empty($request->image)) {
            $image = FileUploader::uploadFile($request->file('image'), 'images/usersimage');
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'image' => $image,
            'address' => $request->address,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'device_id' => $request->device_id,
            'firebase_uid' => $request->fuid
        ]);
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    // public function OtpLogin(Request $request){

    //     $request->validate([
    //         // "phone" => "required|numeric",
    //         // "device_id" => "required"
    //     ]);

    //     \Log::info($request->token);

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
    //     $user = User::where('phone', $request->phone)->where("type","user")->first();
    //     if(!empty($user)){
    //         if($user->name == null){
    //             $type = "new";

    //         }
    //         $token = $user->createToken("user")->plainTextToken;
    //         $user->update([
    //             "device_id" => $request->device_id,
    //             "type" => "user"
    //         ]);

    //         return response([
    //             "data" => $user,
    //             "token" => $token,
    //             "type" => $type,
    //             "status" => true
    //         ],200);
    //     }
    //     else{
    //         $data = User::create([
    //             "phone" => $request->phone,
    //             "device_id" => $request->device_id,
    //             "type" => "user"
    //         ]);
    //         $token = $data->createToken("user")->plainTextToken;

    //         return response([
    //             "data" => $data,
    //             "token" => $token,
    //             "type" => "new",
    //             "status" => true
    //         ],200);
    //     }



    // }

    // public function loginOne(Request $request)
    // {
    //     // return $request->all();
    //     $t = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     $user = User::where('email', $request['email'])->first();
    //     if (!$user || !Hash::check($request['password'], $user->password)) {
    //         return response([
    //             'message' => 'Bad Credential!'
    //         ], 401);
    //     }
    //     $response = [
    //         'user' => $user,
    //         'token' => $user->createToken('user')->plainTextToken,
    //     ];
    //     return response($response, 200);
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

    //    return $request->all();
        $request->validate([
            "phone" => "required",
            "otp" => "required"
        ]);

        $type = "old";

        $user = User::where("phone", $request->phone)->first();



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

                    $user->update([
                        "device_id" => $request->device_id
                    ]);

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
                    "type" => "user"
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
        $type = 'old';
        if (!empty($user)) {
            $user->update([
                'device_id' => $request->device_id,
                'firebase_uid' => $uid,
            ]);
        } else {
            $user =  User::create([
                'email' => $email,
                'device_id' => $request->device_id,
                'firebase_uid' => $uid,
                "type" => "user"
            ]);
            $type = 'new';
        }
        return response([
            'type' => $type,
            'user_data' => $user,
            'token' => $user->createToken('user')->plainTextToken,
        ]);
    }

    public function logOut()
    {
        auth("sanctum")->user()->id->tokens()->delete();
        return response([
            "message" => "Logout Successfully",
            "status" => true
        ], 200);
    }

    // public function login(Request $request)
    // {
    //     // return $request->all();
    //     $t = $request->validate([
    //         'token' => 'required|string',
    //         'device_id' => 'required|string',
    //     ]);
    //     return $t;
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
    //     $phone = substr($firebase_user->phoneNumber, 3);
    //     $user = User::where('fuid', $uid)->first();
    //     $type = 'old';
    //     if (!empty($user)) {
    //         $user->update([
    //             'device_id' => $request->device_id,
    //         ]);
    //     } else {
    //         $user =  User::create([
    //             'phone' => $phone,
    //             'device_id' => $request->device_id,
    //             'fuid' => $uid,
    //         ]);
    //         $type = 'new';
    //     }
    //     return response([
    //         'type' => $type,
    //         'token' => $user->createToken('user')->plainTextToken,
    //     ]);
    // }
}
