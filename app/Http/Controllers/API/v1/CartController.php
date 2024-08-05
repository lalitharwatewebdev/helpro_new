<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;

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


        



        
    }   
}
