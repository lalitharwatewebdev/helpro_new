<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Providers\RazorpayServiceProvider;
use App\Models\Cart;
use App\Models\AcceptedBooking;
use App\Models\Areas;
use App\Models\Booking;
use App\Models\BookingRequest;
use Razorpay\Api\Api;
use App\Models\BusinessSetting;

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
        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        $services_charges =  $business_settings['service_charges'];
        $request->validate([
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required"
        ]);

        // $user = Cart::with("labour:id,rate_per_day")
        //     ->where("user_id", auth()->user()->id)
        //     ->select("labour_id")
        //     ->get();

        $area = Areas::find($request->area_id);
        // return $area;

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
        $data->note = $request->note;
        $data->save();

        $order = $this->razorpay->createOrder($amount, "INR", $data->id);




        $booking = new Booking();
        // $labour_arr[] = $cart->labour_id;
        $booking->user_id = auth()->user()->id;
        // $booking->labour_id = $cart->labour_id;
        // $booking->total_amount = $amount - $services_charges;
        $booking->total_amount = $amount;
        $booking->service_charges = $services_charges;
        $booking->checkout_id = $data->id;
        $booking->quantity_required =  $request->quantity;
        $booking->otp = mt_rand(111111, 999999);
        $booking->save();



        // $booking_request = new BookingRequest();



        return response()->json([
            "message" => "Checkout created successfully",
            "order_id" => $order['id'],
            "checkout_id" => $data->id,
            "status" => true
        ], 200);
    }


    public function fetchOrder(Request $request)
    {
        $request->validate([
            "order_id" => "required"
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $fetchOrder = $this->razorpay->fetchOrder($request->order_id);
        // return $fetchOrder;

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

        // $labour_id = Cart::where("user_id", auth()->user()->id)->get();

        // $labour_data = $labour_id->pluck("labour_id")->toArray();




        // adding to booking page


        // $booking_data = Booking::with('user',"checkout")->where("user_id",auth()->user()->id)->get();

        // return response([
        //     "data" => $booking_data,
        //     "status" => true
        //     ],200);

    }

    public function bookingData()
    {
        // Fetch booking data with related models
        $data = Booking::with(['checkout.category', 'checkout.area', 'checkout.address'])
            ->where('payment_status', 'captured')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->get();

     
        $processedData = $data->map(function ($booking) {

           
            if ($booking->current_quantity == $booking->quantity_required) {
                $booking->booking_status = "completed";
            }

           
            $labours = AcceptedBooking::with('labour:id,name,phone')
                ->where("booking_id", $booking->id)
                ->get();

           
            $booking->labours = $labours;

          
            return $booking;
        });

        
        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
