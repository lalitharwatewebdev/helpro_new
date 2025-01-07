<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\ExtraTimeWork;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourBooking;
use App\Models\LabourFeedbackImage;
use App\Models\ReferralMaster;
use App\Models\State;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Wallet;
use App\Providers\RazorpayServiceProvider;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function referralGenerator($username)
    {

        return strtoupper(substr($username, 0, 5)) . mt_rand(11, 99);
    }

    public function store(Request $request)
    {

        $user_referral = '';
        $data = User::where("id", auth()->user()->id)->first();

        $buinsess_settings = BusinessSetting::pluck("value", "key");
        $referral_amount = $buinsess_settings['referral_amount'];
        $referral_via_amount = $buinsess_settings['referral_via_amount'];

        if ($request->referral) {
            $user_referral = User::where("referral_code", $request->referral)->first();
        }

        if (empty($data)) {
            return response([
                "message" => "user not found",
                "status" => false,
            ], 400);
        }
        $data->name = $request->username;
        $data->email = $request->email;
        $data->gender = $request->gender;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->address = $request->address;
        $data->lat_long = $request->lat_long;
        $data->gst_no = $request->gst_no;
        $data->phone = $request->phone;

        $data->referral_code = $this->referralGenerator($request->username);

        if ($request->profile_pic) {
            $data->profile_pic = FileUploader::uploadFile($request->profile_pic, "images/profile_pic");
        }

        if ($request->referral) {
            $is_refer = ReferralMaster::where("referral_user_id", $user_referral->id)->where("user_id", auth()->user()->id)->first();
            if ($is_refer) {
                return response([
                    "message" => "Referral Code Already Redeemed",
                    "status" => true,
                ], 200);
            } else {
                ReferralMaster::create([
                    "referral_user_id" => $user_referral->id,
                    "user_id" => auth()->user()->id,
                ]);
            }

            if ($user_referral->referral_code == auth()->user()->referral_code) {
                return response([
                    "message" => "You cannot use this referral code",
                    "status" => false,
                ], 400);
            }

            if ($user_referral) {
                // adding to authenticated user
                Transactions::create([
                    "amount" => $referral_amount,
                    "transaction_type" => "credited",
                    "user_id" => auth()->user()->id,
                    "remark" => "Added to wallet via referral",
                ]);

                $wallet = Wallet::firstOrCreate([
                    ["user_id" => auth()->user()->id],
                    ["amount" => 0],
                ]);

                $wallet->increment("amount", intval($referral_amount));

                // adding to referral user
                Transactions::create([
                    "amount" => $referral_via_amount,
                    "transaction_type" => "credited",
                    "remark" => "Added to wallet via referral",
                    "user_id" => $user_referral->id,
                ]);

                $wallet = Wallet::firstOrCreate([
                    ["user_id" => $user_referral->id],
                    ["amount" => 0],
                ]);

                $wallet->increment("amount", intval($referral_via_amount));
            } else {
                return response([
                    "message" => "Invalid Referral Code",
                ], 200);
            }
        }

        $data->save();

        return response([
            "message" => "User Data Added Successfully",
            "status" => true,
        ], 200);
    }

    public function profile()
    {

        $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

        if (!$user_wallet) {
            Wallet::create([
                "user_id" => auth()->user()->id,
            ]);
        }

        $user_id = auth()->user()->id;
        $data = User::with("states", "cities")->where("id", $user_id)->first();

        return response([
            "data" => $data,
            "user_wallet" => $user_wallet,
            "status" => true,
        ], 200);
    }

    public function getState()
    {
        $state = State::where("country_id", "101")->get();
        return response([
            "data" => $state,
            "status" => true,
        ], 200);

    }

    public function getCity(Request $request)
    {
        $city = City::where("country_id", "101")->where("state_id", $request->query("state_id"))->get();
        return response([
            "data" => $city,
            "status" => true,
        ], 200);

    }

    public function cancelBooking(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)->first();

        if ($booking->transaction_type == "pre_paid") {
            $is_wallet_exist = Wallet::where('user_id', $booking->user_id)->first();

            if (!empty($is_wallet_exist)) {
                $total = $is_wallet_exist->amount + $booking->total_amount;
                $is_wallet_exist->amount = $total;
                $is_wallet_exist->save();
            } else {
                $wallet = new Wallet();
                $wallet->user_id = $booking->user_id;
                $wallet->amount = $booking->total_amount;
                $wallet->save();
            }
        }

        $booking->booking_status = "cancelled";
        $booking->save();

        $labour_accepted_booking = LabourAcceptedBooking::where('booking_id', $booking->labour_booking_id)->update(['status' => 'blocked']);

        return response([
            "success" => true,
            "message" => "Cancel Booking Successfully",
        ], 200);
    }

    public function getAcceptedLabourDetails(Request $request)
    {
        $booking_data = Booking::where('id', $request->booking_id)->first();

        $labour_booking_data = LabourAcceptedBooking::where('booking_id', $booking_data->labour_booking_id)->pluck('labour_id');

        $labour_data = User::with(['labourAcceptedBooking' => function ($q) use ($booking_data) {
            $q->where('booking_id', $booking_data->labour_booking_id);
        }])->whereIn('id', $labour_booking_data)->get();

        return response([
            "success" => true,
            "data" => $labour_data,
        ], 200);
    }

    public function addLabourFeedback(Request $request)
    {
        // \Log::info("addLabourFeedback");

        // \Log::info($request->all());
        // $booking_data = Booking::where('id', $request->booking_id)->first();

        // $labour_booking = LabourBooking::where('id', $booking_data->labour_booking_id)->first();

        $labour_accepted_booking = LabourBooking::where('id', $request->booking_id)->first();

        $labour_accepted_booking->labour_feedback = $request->feedback;
        $labour_accepted_booking->labour_rating = $request->rating;

        $labour_accepted_booking->save();

        if (!empty($request->images)) {
            foreach ($request->images as $key => $value) {
                $datas = new LabourFeedbackImage();
                $datas->labour_booking_id = $labour_accepted_booking->id;
                if ($value) {
                    $datas->image = FileUploader::uploadFile($value, 'images/labour_feedback_images');
                }
                $datas->save();
            }
        }

        return response([
            "success" => true,
            "message" => "Feedback Send Successfully",
        ], 200);

    }

    public function logOut(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        $user->device_id = null;
        $user->save();

        // auth('sanctum')->user()->id->tokens()->delete();

        return response([
            "message" => $user->type . " Logout Successfully",
            "status" => true,
        ], 200);
    }

    protected $razorpay;
    public function __construct(RazorpayServiceProvider $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    public function fetchExtraTineWorkOrder(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
            "total_amount" => "required",
        ]);

        $booking_data = Booking::where('id', $request->booking_id)->first();

        $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

        $data = new ExtraTimeWork();
        $data->booking_id = $request->booking_id;
        $data->labour_booking_id = $booking_data->labour_booking_id;
        $data->total_amount = $request->total_amount;
        $data->commission_amount = $request->commission_amount;
        $data->gst = $request->gst;
        $data->labour_id = implode(',', $request->labour_id);
        $data->labour_amount = $request->labour_amount;

        if ($booking_data->transaction_type == 'pre_paid') {
            if ($request->use_wallet == "yes") {
                if ($user_wallet->amount == 0) {
                    $order = $this->razorpay->createOrder($request->total_amount, "INR", $request->booking_id);

                    $is_razorpay = true;
                    $wallet_use = false;

                    $data->order_status = "pending";
                    $data->razorpay_order_id = $order->id ?? $order['id'] ?? null;

                }

                if ($user_wallet->amount < $request->total_amount) {
                    $partial_amount = $request->total_amount - $user_wallet->amount;
                    // $user_wallet->decrement("amount", $user_wallet->amount);
                    // \Log::info("partial_amount");
                    // \Log::info($partial_amount);
                    $order = $this->razorpay->createOrder($partial_amount, "INR", $request->booking_id);
                    // \Log::info(json_encode($order));

                    $is_razorpay = true;
                    $wallet_use = "partial_use";

                    $data->order_status = "pending";
                    $data->razorpay_order_id = $order->id ?? $order['id'] ?? null;

                } else {
                    $is_razorpay = false;
                    $wallet_use = true;

                    $user_wallet->decrement("amount", $request->total_amount);

                    $data->order_status = "paid";
                    $data->razorpay_order_id = null;
                }
            } else {
                $order = $this->razorpay->createOrder($request->total_amount, "INR", $request->booking_id);
                $is_razorpay = true;
                $wallet_use = false;

                $data->order_status = "paid";
                $data->razorpay_order_id = null;

            }

            $data->save();

            // \Log::info("fetchExtraTineWorkOrder");

            // \Log::info($data);

            return response()->json([
                "message" => "Add On created successfully",
                "order_id" => $order->id ?? $order['id'] ?? null,
                "is_razorpay" => $is_razorpay,
                "is_wallet" => $wallet_use,
                "status" => true,
            ], 200);
        } else {

            $data = new ExtraTimeWork();
            $data->booking_id = $request->booking_id;
            $data->labour_booking_id = $booking_data->labour_booking_id;
            $data->total_amount = $request->total_amount;
            $data->commission_amount = $request->commission_amount;
            $data->gst = $request->gst;
            $data->labour_id = implode(',', $request->labour_id);
            $data->labour_amount = $request->labour_amount;
            $data->order_status = "pending";
            $data->razorpay_order_id = null;
            $data->save();

            return response()->json([
                "status" => true,
                "message" => "Add On created successfully",
            ], 200);
        }

    }

    public function createExtraTineWorkOrder(Request $request)
    {
        $request->validate([
            "order_id" => "required",
        ]);

        $fetchOrder = $this->razorpay->fetchOrder($request->order_id);

        $data = ExtraTimeWork::where('razorpay_order_id', $request->order_id)->first();
        if ($fetchOrder['status'] == true) {
            $data->order_status = "paid";
            $data->save();
        }

        return response([
            "success" => true,
            "message" => "Booking Done Successfully",
        ], 200);

    }

    public function getExtraWorkData(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
        ]);

        $data = ExtraTimeWork::where('booking_id', $request->booking_id)->get();

        return response([
            "success" => true,
            "data" => $data,
        ], 200);
    }
}
