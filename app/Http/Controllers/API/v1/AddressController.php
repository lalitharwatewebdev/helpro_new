<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(Request $request){
        $id = auth()->user()->id;

        $data = new Address();
        $data->user_id = $id;
        $data->address = $request->address;
        $data->pincode = $request->pincode;
        $data->state_id = $request->state_id;
        $data->city_id = $request->city_id;
        $data->is_primary = $request->isPrimary;
        
        $data->save();

        return response([
            "message" => "Address Added Successfully",
            "status" => true
        ],200);
    }


    public function edit(Request $request){
        $id = $request->query("id");

        $data = Address::where("id",$id)->first();

        return response([
            "data" => $data,
            "status" => true,
        ],200);
    }

    public function delete(Request $request){
        $id = $request->id;

        Address::where("id",$id)->first()->delete();

        return response([
            "message" => "Address Deleted Successfully",
            "status" => true
        ],200);
    }

    public function get(){
        $data = Address::where("user_id",auth()->user()->id)->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    // public function update(){
    //     $id = auth()->user()->id;

    //     $user_address = Address::where("id","")
    // }
}
