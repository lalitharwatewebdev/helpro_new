<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;


use Illuminate\Http\Request;

class LabourController extends Controller
{
    public function get(){
        $data = User::with(["states:id,name","cities:id,name"])->active()->where("labour_status","pending")
        ->where("type","labour")->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);

    }
}
