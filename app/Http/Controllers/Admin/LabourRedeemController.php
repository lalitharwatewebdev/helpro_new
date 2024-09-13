<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabourRedeem;
use Illuminate\Http\Request;

class LabourRedeemController extends Controller
{
    public function index(){
        $data = LabourRedeem::where("payment_status","pending")->get();
        return view("content.tables.labour_redeem",compact("data"));
    }
}
