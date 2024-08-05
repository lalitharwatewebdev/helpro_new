<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;

class CartController extends Controller
{
    public function get(){
        $id = auth()->user()->id;
        $data = Cart::with("labour")->where("user_id",$id)->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }

    public function add(Request $request){
        $id = auth()->user()->id;
        $labour_id = $request->labour_id;

        $user = User::find($id);

        $user->labourAttach()->attach($labour_id);

        return response([
            "user" => $user,
            "message" => "Labour Added Successfully",
            "status" => true
        ],200);



        
    }  
    
    public function delete(Request $request){
        $id = auth()->user()->id;

        $user = User::find($id);

        $user->labourAttach()->detach($request->labour_id);

        return response([
            "message" => "Labour Removed Successfully",
            "status" => true
        ],200);
    }
}
