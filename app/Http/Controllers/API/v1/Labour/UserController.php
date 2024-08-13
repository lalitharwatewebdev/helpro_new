<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function profile(Request $request){
        $user_id  = auth()->user()->id;
        $data = User::find($user_id);
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function activeStatus(Request $request){
        $data = User::find(auth()->user()->id);

        $data->is_online = !$data->is_online;

        return response([
            "message" => "Online Status Updated Successfully",
            "status" => true
        ],200);
    }
}
