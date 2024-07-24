<?php

namespace App\Http\Controllers\API\v1\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|min:3',
            'second_name' => 'required',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric|min:10',
            'gender' => 'required',
            'password' => 'required',
        ]);
        if (!empty($request->image)) {
            $image = FileUploader::uploadFile($request->file('image'), 'images/usersimage');
        }
        // return $image;
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

    public function loginOne(Request $request)
    {
        // return $request->all();
        $t = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request['email'])->first();
        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response([
                'message' => 'Bad Credential!'
            ], 401);
        }
        $response = [
            'user' => $user,
            'token' => $user->createToken('user')->plainTextToken,
        ];
        return response($response, 200);
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
