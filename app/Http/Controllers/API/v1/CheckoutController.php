<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\AcceptedBooking;
use App\Models\Address;
use App\Models\Areas;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\Transactions;
use App\Models\User;
use App\Models\UserReview;
use App\Models\Wallet;
use App\Providers\RazorpayServiceProvider;
use DateTime;
use Illuminate\Http\Request;

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

    public function formatTimeWithAMPM($time)
    {
        $dateTime = new DateTime($time);
        return $dateTime->format('h:i A');
    }

    public function formatDateWithSuffix($date)
    {
        $dateTime = new DateTime($date);
        $day = $dateTime->format('j'); // Day of the month without leading zeros
        $month = $dateTime->format('M'); // Short month name (Jan, Feb, Mar, etc.)
        $year = $dateTime->format('Y'); // Full year

        // Determine the appropriate ordinal suffix
        if ($day % 10 == 1 && $day != 11) {
            $suffix = 'st';
        } elseif ($day % 10 == 2 && $day != 12) {
            $suffix = 'nd';
        } elseif ($day % 10 == 3 && $day != 13) {
            $suffix = 'rd';
        } else {
            $suffix = 'th';
        }

        // Handle special cases for 11th, 12th, and 13th
        if (in_array($day, [11, 12, 13])) {
            $suffix = 'th';
        }

        return $day . $suffix . ' ' . $month . ' ' . $year;
    }

    public function store(Request $request)
    {

        \Log::info($request->all());
        $is_razorpay = true;
        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        $services_charges = $business_settings['service_charges'];
        $request->validate([
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required",
        ]);

        $area = Areas::find($request->area_id);
        $category_data = Category::find($request->category_id);

        $labour_arr = [];
        $diff = (strtotime($request->end_date) - strtotime($request->start_date));
        $date_result = abs(round($diff) / 86400) + 1;
        \Log::info("date_result");
        \Log::info($date_result);

        // $amount = (intval($area->price) * intval($request->quantity)) * $date_result + $services_charges;
        $amount = $request->amount;

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
        $data->transaction_type = $request->transaction_type;
        $data->save();

        if ($request->transaction_type == 'pre_paid') {
            if ($request->use_wallet == "yes") {
                $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

                if ($user_wallet->amount == 0) {

                    $order = $this->razorpay->createOrder($amount, "INR", $data->id);
                    $is_razorpay = true;
                }

                if ($user_wallet->amount < $amount) {
                    $partial_amount = $amount - $user_wallet->amount;
                    $user_wallet->decrement("amount", $user_wallet->amount);
                    $order = $this->razorpay->createOrder($partial_amount, "INR", $data->id);
                    $is_razorpay = true;
                } else {
                    $is_razorpay = false;
                    $user_wallet->decrement("amount", $amount);
                }

                Transactions::create([
                    "user_id" => auth()->user()->id,
                    "amount" => $amount,
                    "remark" => "Labours Purchased",
                    "transaction_type" => "debited",
                ]);
            } else {
                $order = $this->razorpay->createOrder($amount, "INR", $data->id);
            }
        }

        $booking = new Booking();
        $booking->user_id = auth()->user()->id;
        $booking->total_amount = $amount;
        $booking->service_charges = $services_charges;
        if ($request->use_wallet == 'yes') {
            $booking->payment_status = 'captured';
        }
        $booking->checkout_id = $data->id;
        $booking->quantity_required = $request->quantity;
        $booking->otp = mt_rand(111111, 999999);
        $booking->transaction_type = $request->transaction_type;
        if ($request->transaction_type == 'pre_paid') {
            $booking->razorpay_status = "created";
        } else {
            $booking->razorpay_status = "pending";
        }

        $booking->save();

        $user_name = auth()->user()->firstname . " " . auth()->user()->lastname;

        $labour_get_data = User::where("type", "labour")->pluck("device_id");
        // \Log::info("labour's device_id ===>", $labour_get_data);
        $user_address = Address::where("user_id", auth()->user()->id)->first();
        $title = "New Job Available";
        $message = "You have a new job available.";
        $device_ids = $labour_get_data->toArray();
        $additional_data = ["category_name" => "Helper", "address" => $user_address->address, "booking_id" => "32", "start_time" => $this->formatTimeWithAMPM($data->start_time), "end_time" => $this->formatTimeWithAMPM($data->end_time), "price" => $booking->total_amount, "start_date" => $this->formatDateWithSuffix($data->start_date), "end_date" => $this->formatDateWithSuffix($data->end_date), "days_count" => $date_result, "user_ name" => $user_name, "category_id" => $request->category_id];

        $firebaseService = new SendNotificationJob();
        $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);

        return response()->json([
            "message" => "Booking created successfully",
            "order_id" => $order['id'] ?? null,
            "checkout_id" => $data->id,
            "is_razorpay" => $is_razorpay,
            "status" => true,
        ], 200);
    }

    public function fetchOrder(Request $request)
    {
        $request->validate([
            "order_id" => "required",
        ]);

        $fetchOrder = $this->razorpay->fetchOrder($request->order_id);

        if ($fetchOrder['status'] == true) {
            Booking::where("user_id", auth()->user()->id)->where("checkout_id", $fetchOrder["checkout_id"])->update([
                "payment_status" => "captured",
                "otp" => mt_rand(111111, 999999),
            ]);
            return response([
                "message" => "Booking Done Successfully",
            ], 200);
        } else {
            return response([
                "message" => "Transaction Failure",
            ], 200);
        }
    }

    public function bookingData()
    {
        // Fetch booking data with related models
        $data = Booking::with(['checkout.category', 'checkout.area', 'checkout', 'checkout.address.states:id,name', 'checkout.address.cities:id,name'])
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
            "status" => true,
        ], 200);
    }

    private function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function sendReview(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
            "rating" => "required",
            "review" => "required",
        ]);

        $data = new UserReview();
        $data->review = $request->review;
        $data->rating = $request->rating;
        $data->user_id = auth()->user()->id;
        $data->booking_id = $request->booking_id;
        $data->save();

        return response([
            "message" => "Review Added Successfully",
            "status" => true,
        ], 200);

    }
}
