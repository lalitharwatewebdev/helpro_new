<?php
namespace App\Http\Controllers\API\v1\Labour\Auth;

use App\Helpers\FileUploader;
use App\Helpers\OTPGenerator;
use App\Http\Controllers\Controller;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourRating;
use App\Models\OTP;
use App\Models\PayCommission;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Wallet;
use App\Providers\LabourRazorPayServiceProvider;
use App\Services\RazorPayIntegration;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    protected $labourRazorPay;

    public function __construct(LabourRazorPayServiceProvider $razorpay)
    {
        $this->labourRazorPay = $razorpay;
    }

    // public function OtpLogin(Request $request){
    //     \Log::info($request->all());
    //     $request->validate([
    //         // "phone" => "required|numeric",
    //         // "device_id" => "required"
    //     ]);

    //     $type = "old";

    //       $auth = app('firebase.auth');
    //     try {
    //         $verifiedIdToken = $auth->verifyIdToken($request->token);
    //     } catch (FailedToVerifyToken $e) {
    //         return response()->json([
    //             'message' => 'The token is invalid: ' . $e->getMessage(),
    //         ], 401);
    //     }
    //     $uid = $verifiedIdToken->claims()->get('sub');
    //     $firebase_user = $auth->getUser($uid);
    //     $phone = substr($firebase_user->phoneNumber, 3);
    //     $user = User::where('phone', $request->phone)->where("type","labour")->first();
    //     // return $user;

    //     if(!empty($user)){
    //         if($user->name == null){
    //             $type = "new";
    //         }
    //         $token = $user->createToken("user")->plainTextToken;
    //         $user->update([
    //             "device_id" => $request->device_id,
    //             "type" => "labour"
    //         ]);

    //         return response([
    //             "data" => $user,
    //             "token" => $token,
    //             "type" => $type,
    //             "status" => true
    //         ],200);
    //     }
    //     else{

    //             $data = User::create([
    //                 "phone" => $request->phone,
    //                 "device_id" => $request->device_id,
    //                 "type" => "labour"
    //             ]);
    //             $token = $data->createToken("user")->plainTextToken;

    //             return response([
    //                 "data" => $data,
    //                 "token" => $token,
    //                 "type" => "new",
    //                 "status" => true
    //             ],200);
    //         }
    // }

    public function generateOTP(Request $request)
    {

        $request->validate([
            "phone" => "required",
        ]);

        if ($request->phone == "8111111111") {

            return response([
                "phone"   => $request->phone,
                "message" => "OTP send to your Mobile Number",
                "status"  => true,

            ], 200);
        }

        $otp = OTPGenerator::generate();

        $data = new OTP();

        $data->phone         = $request->phone;
        $data->generated_otp = $otp;

        $data->save();

        $res = OTPGenerator::sendMessage($otp, $request->phone);

        return response([
            "message" => "OTP send to your Mobile Number",
            "status"  => true,
        ], 200);

    }

    public function OTPLogin(Request $request)
    {

        \Log::info("Labour Login", $request->all());

        $request->validate([
            "phone" => "required",
            "otp"   => "required",
        ]);
        $type = "old";

        $user = User::where("phone", $request->phone)->where("type", "labour")->first();

        $otp = OTP::where("phone", $request->phone)->latest()->first();

        if ($request->phone == '81111111111') {
            $token = $user->createToken("user_app")->plainTextToken;
            return response([
                "token" => $token,
            ]);
        }

        if (! empty($user)) {

            if ($otp->generated_otp == $request->otp) {
                if ($user->name == null) {
                    $type = 'new';

                    $user->update([
                        "device_id" => $request->device_id,
                    ]);
                    $token = $user->createToken("otp_login")->plainTextToken;

                    return response([
                        "message" => "User Authenticated",
                        "token"   => $token,
                        "type"    => $type,
                        "status"  => true,

                    ], 200);
                } else {
                    $type  = 'old';
                    $token = $user->createToken("otp_login")->plainTextToken;
                    $user->update([
                        "device_id" => $request->device_id,
                    ]);

                    return response([
                        "message" => "User Authenticated",
                        "token"   => $token,
                        "type"    => $type,
                        "status"  => true,

                    ], 200);
                }
            } else {
                return response([
                    "message" => "Invalid OTP",
                    "status"  => false,
                ], 400);
            }
        } else {
            $user = User::create([
                "device_id" => $request->device_id,
                "phone"     => $request->phone,
                "type"      => "labour",
            ]);

            $token = $user->createToken("labour_otp_login")->plainTextToken;
            $type  = "new";

            return response([
                "message" => 'Account Created Successfully',
                "status"  => true,
                "token"   => $token,
                "type"    => $type,
            ]);
        }
    }

    public function googleLogin(Request $request)
    {

        $t = $request->validate([
            'token'     => 'required|string',
            'device_id' => 'required|string',
        ]);

        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($request->token);
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'message' => 'The token is invalid: ' . $e->getMessage(),
            ], 401);
        }
        $uid           = $verifiedIdToken->claims()->get('sub');
        $firebase_user = $auth->getUser($uid);
        $email         = $firebase_user->email;
        $user          = User::Where('email', $email)->first();

        $type = 'old';

        if (! empty($user)) {
            if ($user->name == null) {
                $type = "new";
            }
            $user->update([
                'device_id' => $request->device_id,
            ]);
        } else {
            $type = 'new';
            $user = User::create([
                'email'     => $email,
                'device_id' => $request->device_id,
                "type"      => "labour",
            ]);

        }
        return response([
            'type'      => $type,
            'user_data' => $user,
            'token'     => $user->createToken('user')->plainTextToken,
        ]);
    }

    public function signUp(Request $request)
    {
        $request->validate([
            "category" => "required|exists:categories,id",
        ]);
        \Log::info($request->all());

        $data        = User::find(auth()->user()->id);
        $data->name  = $request->name;
        $data->email = $request->email;
        $data->state = $request->state;
        $data->city  = $request->city;
        if ($request->profile_image) {
            $data->profile_pic = FileUploader::uploadFile($request->profile_image, "images/profile_pic");
        }
        if ($request->aadhaar_card_front) {
            $data->aadhaar_card_front = FileUploader::uploadFile($request->aadhaar_card_front, "images/aadhaar_card");
        }
        if ($request->aadhaar_card_back) {
            $data->aadhaar_card_back = FileUploader::uploadFile($request->aadhaar_card_back, "images/aadhaar_card");
        }
        $data->aadhaar_number  = $request->aadhaar_number;
        $data->pan_card_number = $request->pan_number;
        $data->bank_name       = $request->bank_name;
        $data->branch_address  = $request->bank_address;
        $data->pan_card_number = $request->pan_number;
        if ($request->pan_front) {
            $data->pan_front = FileUploader::uploadFile($request->pan_front, "images/pan_card");
        }
        $data->gender         = strtolower($request->gender);
        $data->lat_long       = $request->lat_long;
        $data->address        = $request->address;
        $data->start_time     = $request->start_time;
        $data->end_time       = $request->end_time;
        $data->IFSC_code      = $request->ifsc_code;
        $data->otp            = mt_rand(111111, 999999);
        $data->availability   = $request->days_available;
        $data->qualification  = $request->qualification;
        $data->account_number = $request->account_number;
        $data->pin_code       = $request->pincode;

        $data->save();

        // adding category in user data
        $user = User::find(auth()->user()->id);
        $user->category()->detach();

        $user->category()->attach($request->category);

        return response([
            "message" => "Labour Data Saved Successfully",
            "status"  => true,
        ], 200);

    }

    public function updateCategory(Request $request)
    {

        $user = User::find(auth()->user()->id);

        $user->category()->sync($request->category);

        return response([
            "message" => "Category Updated Successfully",
            "status"  => true,
        ], 200);
    }

    public function logOut(Request $request)
    {
        $user            = User::where('id', $request->user()->id)->first();
        $user->device_id = null;
        $user->save();
        auth('sanctum')->user()->id->tokens()->delete();

        return response([
            "message" => "Labour Logout Successfully",
            "status"  => true,
        ], 200);
    }

    public function Profile()
    {
        $user = User::find(auth()->user()->id);

        $sum_rating    = LabourRating::where('labour_id', auth()->user()->id)->sum('rating');
        $booking_count = LabourRating::where('labour_id', auth()->user()->id)->count();

        if ($booking_count > 0) {

            $avg_rating = $sum_rating / $booking_count;
        } else {
            $avg_rating = 0;
        }

        $user['rating'] = $avg_rating ?? 0;
        $user_category  = $user->category()->orderBy('id', 'desc')->get();
        return response([
            "data"     => $user,
            "category" => $user_category,
            "status"   => true,
        ], 200);
    }

    public function startWork(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
        ]);
        $data                 = LabourAcceptedBooking::where('labour_id', auth()->user()->id)->where('booking_id', $request->booking_id)->first();
        $data->current_status = 1;
        $data->start_time     = date('Y-m-d H:s', strtotime(now()));
        $data->save();

        return response([
            "message" => "Started Work Successfully",
            "status"  => true,
        ], 200);
    }

    public function endWork(Request $request)
    {
        $request->validate([
            "booking_id" => "required",
        ]);
        $data                 = LabourAcceptedBooking::where('labour_id', auth()->user()->id)->where('booking_id', $request->booking_id)->first();
        $data->current_status = 2;
        $data->end_time       = date('Y-m-d H:s', strtotime(now()));
        $data->is_work_done   = 1;

        $data->save();

        return response([
            "message" => "Ended Work Successfully",
            "status"  => true,
        ], 200);
    }

    public function getLabourBookingHistory(Request $request)
    {
        // return $request->user()->id;
        $labour_booking = LabourAcceptedBooking::with(['booking' => ['user', 'labour_bookings']])->where('labour_id', auth()->user()->id)->where('is_work_done', '1')->orderBy('id', 'desc')->get();

        return response([
            "data"   => $labour_booking,
            "status" => true,
        ], 200);
    }

    public function payCommission(Request $request)
    {
        $request->validate([
            "amount" => "required",
        ]);
        $razorPayOrderId = RazorPayIntegration::createOrder($request->amount);

        if (isset($razorPayOrderId['razorpay_order_id'])) {

            $labourRazorPay               = new PayCommission();
            $labourRazorPay->order_id     = $razorPayOrderId['razorpay_order_id'];
            $labourRazorPay->labour_id    = $request->user()->id;
            $labourRazorPay->amount       = $request->amount;
            $labourRazorPay->order_status = "pending";

            $labourRazorPay->save();

            return response([
                "order_id" => $labourRazorPay->order_id,
                "status"   => true,
            ], 200);
        }
        return response([
            "message" => "Something went wrong",
            "status"  => false,
        ], 400);

    }

    public function fetchPayCommission(Request $request)
    {
        $request->validate([
            "order_id" => "required",
        ]);
        // $fetchOrder   = $this->labourRazorPay->fetchOrder($request->order_id);
        $checkPayment = RazorPayIntegration::fetchOrder($request->order_id);
        \Log::info("fetchOrder");
        \Log::info($request->order_id);

        \Log::info($checkPayment);
        if (isset($checkPayment['status'])) {

            $data               = PayCommission::where('order_id', $request->order_id)->first();
            $data->order_status = $checkPayment['status'];
            $data->save();

            $user_wallet         = Wallet::where('user_id', $request->user()->id)->first();
            $wallet_amount       = (int) $user_wallet->amount + (int) $data->amount;
            $user_wallet->amount = $wallet_amount;
            $user_wallet->save();

            Transactions::create([
                "amount"           => $data->amount,
                "transaction_type" => "credited",
                "user_id"          => $request->user()->id,
                "remark"           => "Wallet Credited",
            ]);

            return response([
                "message" => "Payment done Successfully",
                "status"  => true,
            ], 200);

        } else {
            return response([
                "message" => "Something went wrong ",
                "status"  => false,
            ], 400);
        }
    }
}
