<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RazorPayController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        // $this->razorpay = new Api(env("RAZORPAY_KEY"),env("RAZORPAY_SECRET"));
    }
}
