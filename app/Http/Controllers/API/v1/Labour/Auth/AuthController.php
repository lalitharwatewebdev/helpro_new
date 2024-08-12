<?php

namespace App\Http\Controllers\Api\v1\Labour\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function signUp(Request $request){
        $data = new User();

        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->gender = strtolower($request->gender);
        $data->address = $request->address;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->preferred_shift = $request->preferred_shifts;
        $data->save();

        return response([
            "message" => "Labour Data Saved Successfully",
            "status" => true
        ],200);
    }
}
