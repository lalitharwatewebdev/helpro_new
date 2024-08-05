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
        $data->is_primary = $request->isPrimary;
        
        $data->save();

        return response([
            "message" => "Address Added Successfully",
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
