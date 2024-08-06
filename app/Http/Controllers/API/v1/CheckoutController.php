<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request){
        $request->validate([\
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required"
        ]);

        $data = new Checkout();

        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;

        $data->save();

        return response([
            "message" => "Checkout created successfully",
            "status" => true
        ],200);
    }
}
