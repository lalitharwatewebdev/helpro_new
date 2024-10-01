<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\LabourBooking;
use App\Models\LabourPayment;
use App\Models\LabourRazorPay;
use Illuminate\Http\Request;

use App\Providers\LabourRazorPayServiceProvider;



class LabourRazorPayController extends Controller
{

    protected $labourRazorPay;

    public function __construct(LabourRazorPayServiceProvider $razorpay){
        $this->labourRazorPay = $razorpay;
    }

    public function store(Request $request){
        $labourRazorpayment = $this->labourRazorPay->createOrder($request->amount);
        if(isset($labourRazorpayment['id'])){
            // getting booking and then adding it to order_data as json
            $booking_data = LabourBooking::where("labour_booking_code",$request->booking_code)->first();
            // return $booking_data;
            if($booking_data){
                
                $labourRazorPay = new LabourRazorPay();
                $labourRazorPay->order_id = $labourRazorpayment['id'];
                $labourRazorPay->order_data = json_encode($booking_data);
                $labourRazorPay->amount = $request->amount;
                $labourRazorPay->save();
            }

            return response([
                "order_id" => $labourRazorPay->order_id,
                "status" => true
            ],200);
        }
        return response([
            "message" => "Something went wrong",
            "status" => false
        ],400);  
    }

    public function fetchOrder(Request $request){
        $fetchOrder = $this->labourRazorPay->fetchOrder($request->order_id);
        
        if(isset($fetchOrder['status'])){
                // return $fetchOrder;

                // get booking by order_id
                $booking = LabourRazorPay::where("order_id",$fetchOrder['order_id'])->pluck("order_data")->first();
                $booking = json_decode($booking);

                if($booking){

                    $labourPayment = new LabourPayment();
    
                    $labourPayment->booking_id = $booking->id;
                    $labourPayment->save();
    
                    return response([
                        "message" => "Payment done Successfully",
                        "status" => true
                    ],200);
                }


        }
        else{
            return response([
                "message" => "Something went wrong ",
                "status" => false
            ],400);
        }
    }
}
