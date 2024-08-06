<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use App\Models\State;

class UserController extends Controller
{
    public function store(Request $request){
        // $request->validate([
        //     "username" => "required",
        //     "phone" => "required|numeric",
        //     "email" => "required|email",
        //     "gender" => "required",
        //     "state" => "required",
        //     "city" => "required",
        //     "address" => "required",
        //     "image" => "file|mimes:jg,jpeg,webp,png"
        // ]);

        $data = User::where("id",auth()->user()->id)->first();

        if(empty($data)){
            return response([
                "message" => "user not found",
                "status" => false
            ],400);
        }
        $data->name = $request->username;
        $data->email = $request->email;
        $data->gender = $request->gender;
        // $data->state = $request->state;
        // $data->city = $request->city;
        // $data->address = $request->address;
        // $data->lat_long = $request->lat_long;
        if($request->hasFile("profile_img")){
            $data->profile_pic = FileUploader::uploadFile($request->file("profile_img"),"images/profile_pic");
        }
        $data->save();


        return response([
            "message" => "User Data Added Successfully",
            "status" => true
        ],200);
    }

    public function profile(){
        $user_id = auth()->user()->id;
        $data = User::with("states","cities")->where("id",$user_id)->first(); 
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function getState(){
             $state = State::where("country_id", "101")->get();
        return response([
            "data" => $state,
            "status" => true
        ],200);

    }

    public function getCity(Request $request){
        $city = City::where("country_id", "101")->where("state_id",$request->query("state_id"))->get();
        return response([
            "data" => $city,
            "status" => true
        ],200);

    }
}
