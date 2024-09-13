<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;
use App\Models\AcceptedBooking;
use App\Models\Areas;
use App\Models\Booking;
use Razorpay\Api\Api;
use App\Models\Wallet;
use App\Models\BusinessSetting;
use App\Models\Transactions;

class CheckoutController extends Controller
{
    protected $razorpay;
    public function __construct(RazorpayServiceProvider $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    public function randomNumber()
    {
        $random = 0;
        for ($i = 0; $i < 6; $i++) {
            $random += rand(0, 9);
        }

        return $random;
    }

    public function store(Request $request)
    {
        $is_razorpay = true;
        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        $services_charges =  $business_settings['service_charges'];
        $request->validate([
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required"
        ]);

      

        $area = Areas::find($request->area_id);
   

        $labour_arr = [];
        $diff = (strtotime($request->end_date) - strtotime($request->start_date));
        $date_result = abs(round($diff) / 86400) + 1;

        $amount = (intval($area->price) * intval($request->quantity)) * $date_result + $services_charges;

        $data = new Checkout();
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->address_id = $request->address_id;
        $data->user_id = auth()->user()->id;
        $data->category_id = $request->category_id;
        $data->area_id = $request->area_id;
        $data->labour_quantity = $request->quantity;
        $data->alternate_number = $request->alternate_number;
        $data->note = $request->note;
        $data->save();



        $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

    

        if ($request->use_wallet == "yes") {


            if($user_wallet->amount == 0){
                
                $order = $this->razorpay->createOrder($amount, "INR", $data->id);
                $is_razorpay = true;
            }

            if ($user_wallet->amount < $amount) {
                
                $partial_amount = $amount - $user_wallet->amount;
                
                $user_wallet->decrement("amount",$user_wallet->amount);


                
                $order = $this->razorpay->createOrder($partial_amount, "INR", $data->id);

                
               
                $is_razorpay = true;
                
            }
            else{
                $is_razorpay = false;
                $user_wallet->decrement("amount", $amount);
            }
            
            Transactions::create([
                "user_id" => auth()->user()->id,
                "amount" => $amount,
                "remark" => "Labours Purchased",
                "transaction_type" => "debited"
            ]);
            
            
        } else {
            $order = $this->razorpay->createOrder($amount, "INR", $data->id);
        }

     




        $booking = new Booking();
        $booking->user_id = auth()->user()->id;
        $booking->total_amount = $amount;
        $booking->service_charges = $services_charges;
        if ($request->use_wallet == 'yes') {
            $booking->payment_status = 'captured';
        }
        $booking->checkout_id = $data->id;
        $booking->quantity_required =  $request->quantity;
        $booking->otp = mt_rand(111111, 999999);
        $booking->save();



        return response()->json([
            "message" => "Booking created successfully",
            "order_id" => $order['id'] ?? null,
            "checkout_id" => $data->id,
            "is_razorpay" => $is_razorpay,
            "status" => true
        ], 200);
    }


    public function fetchOrder(Request $request)
    {
        $request->validate([
            "order_id" => "required"
        ]);

        $fetchOrder = $this->razorpay->fetchOrder($request->order_id);
       
        if ($fetchOrder['status'] == true) {
            Booking::where("user_id", auth()->user()->id)->where("checkout_id", $fetchOrder["checkout_id"])->update([
                "payment_status" => "captured",
                "otp" => mt_rand(111111, 999999),
            ]);
            return response([
                "message" => "Booking Done Successfully"
            ], 200);
        } else {
            return response([
                "message" => "Transaction Failure"
            ], 200);
        }
    }

    public function bookingData()
    {
        // Fetch booking data with related models
        $data = Booking::with(['checkout.category', 'checkout.area', 'checkout.address.states:id,name', 'checkout.address.cities:id,name'])
            ->where('payment_status', 'captured')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->get();


        $processedData = $data->map(function ($booking) {
            if ($booking->current_quantity > 0) {

                $booking->booking_status = "accepted";
                
            }


            $labours = AcceptedBooking::with('labour:id,name,email,phone,profile_pic,otp')
                ->where("booking_id", $booking->id)
                ->get();

            
            

            

            // $processedCategory = $labours->map(function($labours){
            //     $booking->labours->category = Category::where("id",$labours->id)->first();
            // });

          

            $booking->labours = $labours;


            return $booking;
        });


        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }
}
