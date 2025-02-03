<?php
namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\AcceptedBooking;
use App\Models\Address;
use App\Models\Areas;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\ExtraTimeWork;
use App\Models\ExtraTimeWorkLabour;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourBooking;
use App\Models\LabourRating;
use App\Models\ReviewImage;
use App\Models\Transactions;
use App\Models\User;
use App\Models\UserReview;
use App\Models\Wallet;
use App\Providers\RazorpayServiceProvider;
use DateTime;
use DB;
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
        $day      = $dateTime->format('j'); // Day of the month without leading zeros
        $month    = $dateTime->format('M'); // Short month name (Jan, Feb, Mar, etc.)
        $year     = $dateTime->format('Y'); // Full year

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
        // \Log::info("Store");

        // \Log::info($request->all());
        $is_razorpay       = true;
        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        $services_charges  = $business_settings['service_charges'];
        $request->validate([
            "start_date" => "required",
            "end_date"   => "required",
            "start_time" => "required",
            "end_time"   => "required",
        ]);

        $area          = Areas::find($request->area_id);
        $category_data = Category::find($request->category_id);

        $labour_arr  = [];
        $diff        = (strtotime($request->end_date) - strtotime($request->start_date));
        $date_result = abs(round($diff) / 86400) + 1;

        // $amount = (intval($area->price) * intval($request->quantity)) * $date_result + $services_charges;
        $amount = $request->amount;

        $data                    = new Checkout();
        $data->start_date        = $request->start_date;
        $data->end_date          = $request->end_date;
        $data->start_time        = $request->start_time;
        $data->end_time          = $request->end_time;
        $data->address_id        = $request->address_id;
        $data->user_id           = auth()->user()->id;
        $data->category_id       = $request->category_id;
        $data->area_id           = $request->area_id;
        $data->labour_quantity   = $request->quantity;
        $data->alternate_number  = $request->alternate_number;
        $data->note              = $request->note;
        $data->transaction_type  = $request->transaction_type;
        $data->labour_booking_id = $request->labour_booking_id;
        $data->lat_long          = $request->lat_long;

        $data->save();

        if ($request->transaction_type == 'pre_paid') {
            if ($request->use_wallet == "yes") {
                $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

                if ($user_wallet->amount == 0) {

                    $order = $this->razorpay->createOrder($amount, "INR", $data->id);
                    \Log::info(json_encode($order));

                    $is_razorpay = true;
                    $wallet_use  = false;

                }

                if ($user_wallet->amount < $amount) {
                    $partial_amount = $amount - $user_wallet->amount;
                    // $user_wallet->decrement("amount", $user_wallet->amount);
                    // \Log::info("partial_amount");
                    // \Log::info($partial_amount);
                    $order = $this->razorpay->createOrder($partial_amount, "INR", $data->id);
                    // \Log::info(json_encode($order));

                    $is_razorpay = true;
                    $wallet_use  = "partial_use";
                } else {
                    $is_razorpay = false;
                    $wallet_use  = true;

                    $user_wallet->decrement("amount", $amount);
                }

                Transactions::create([
                    "user_id"          => auth()->user()->id,
                    "amount"           => $amount,
                    "remark"           => "Labours Purchased",
                    "transaction_type" => "debited",
                ]);
            } else {
                $order = $this->razorpay->createOrder($amount, "INR", $data->id);
                // \Log::info("orderrssssssssssssssssssssssssssq");
                \Log::info(json_encode($order));
                $is_razorpay = true;
                $wallet_use  = false;

            }
        } else {
            $is_razorpay = false;
            $wallet_use  = false;
        }
        if ($request->transaction_type == "post_paid" || $is_razorpay == false) {
            $booking                  = new Booking();
            $booking->user_id         = auth()->user()->id;
            $booking->total_amount    = $amount;
            $booking->service_charges = $services_charges;
            if ($request->use_wallet == 'yes') {
                $booking->payment_status = 'captured';
            } else {
                $booking->payment_status = 'failed';
            }
            $booking->checkout_id          = $data->id;
            $booking->quantity_required    = $request->quantity;
            $booking->otp                  = mt_rand(111111, 999999);
            $booking->transaction_type     = $request->transaction_type;
            $booking->labour_amount        = $request->labour_amount;
            $booking->commission_amount    = $request->commission_amount;
            $booking->total_labour_charges = $request->total_labour_charges;
            $booking->labour_booking_id    = $request->labour_booking_id;

            if ($request->transaction_type == 'pre_paid') {
                $booking->razorpay_status = "created";
            } else {
                $booking->razorpay_status = "pending";
                $booking->razorpay_type   = "offline";

            }

            $booking->save();
        }
        $category_id = $data->category_id;
        $user        = User::find(auth()->user()->id);
                                            // $user->update(["lat_long" => $request->lat_long]);
        $earthRadius                = 6371; // Earth radius in kilometers
        $business_settings          = BusinessSetting::pluck("value", "key")->toArray();
        $radius                     = $business_settings['radius'];
        list($latitude, $longitude) = explode(',', $data->lat_long);

        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);

        // Calculate bounding box
        $latDelta = $radius / $earthRadius;
        $lonDelta = $radius / ($earthRadius * cos($latFrom));

        $latMin = rad2deg($latFrom - $latDelta);
        $latMax = rad2deg($latFrom + $latDelta);
        $lonMin = rad2deg($lonFrom - $lonDelta);
        $lonMax = rad2deg($lonFrom + $lonDelta);

        // get area first nearest to user's co-ordinate
        // $areas = Areas::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
        //     ->where('category_id', $data->category_id)
        //     ->whereBetween('latitude', [$latMin, $latMax])
        //     ->whereBetween('longitude', [$lonMin, $lonMax])
        //     ->with("category:id,title,image")->take(1)
        //     ->get();

        $areas = DB::table('areas')->select('areas.*', DB::raw("
            (6371 * acos(
                cos(radians($latitude)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians($longitude)) +
                sin(radians($latitude)) *
                sin(radians(latitude))
            )) AS distance
        "))
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->take(1)->get();

        $area_data = Areas::with(["category:id,title,image"])->where('id', $areas[0]->id)->first();

        $labours = '';

        if (! empty($area_data)) {

            // \Log::info("inside area");
            // \Log::info($areas);
            // $labours = User::where('type', 'labour')
            //     ->whereHas('category', function ($query) use ($category_id) {
            //         $query->where('category_id', $category_id);
            //     })
            //     ->get()
            //     ->filter(function ($labour) use ($latitude, $longitude, $radius, $request, $data) {
            //         [$labourLatitude, $labourLongitude] = explode(',', $data->lat_long);
            //         $distance = $this->haversineGreatCircleDistance(
            //             $latitude,
            //             $longitude,
            //             $labourLatitude,
            //             $labourLongitude
            //         );
            //         return $distance <= $radius;
            //     })
            //     ->map(function ($labour) {
            //         $labour->type = 'labour';
            //         return $labour;
            //     })->whereNotNull('device_id')->pluck("device_id")->toArray();

            $labours_data = DB::table('users')
                ->select('*')
                ->selectRaw("
    (6371 * acos(
        cos(radians(?)) *
        cos(radians(SUBSTRING_INDEX(lat_long, ',', 1))) *
        cos(radians(SUBSTRING_INDEX(lat_long, ',', -1)) - radians(?)) +
        sin(radians(?)) *
        sin(radians(SUBSTRING_INDEX(lat_long, ',', 1)))
    )) AS distance
", [$latitude, $longitude, $latitude])
                ->where('type', 'labour')->having('distance', '<', $radius)
                ->orderBy('distance')
                ->pluck('id');
        }

        \Log::info("labours_data");
        \Log::info($labours_data);
        if (! empty($labours_data)) {
            $labours = User::whereIn('id', $labours_data)->whereHas('category', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })->whereNotNull('device_id')->pluck("device_id")->toArray();
        } else {
            $labours = [];
        }
        // \Log::info("labourssdevice_id");

        // \Log::info($labours);
        $user_address = Address::where("user_id", auth()->user()->id)->where("is_primary", "yes")->first();

        if (! $user_address) {
            return response([
                "message" => "Address is required",
                "status"  => true,
            ], 400);
        }

        $labour_booking_data = LabourBooking::where('id', $data->labour_booking_id)->first();
        // \Log::info("labour's device_id ===>", $labour_get_data);
        $user_address = Address::where("user_id", auth()->user()->id)->first();
        $title        = "New Job Available";
        $message      = "You have a new job available.";
        $device_ids   = $labours;
        if ($request->transaction_type == "post_paid" || $is_razorpay == false) {
            $additional_data = ["category_name" => "Helper", "address" => $user_address->address, "booking_id" => $booking->id, "start_time" => $this->formatTimeWithAMPM($data->start_time), "end_time" => $this->formatTimeWithAMPM($data->end_time), "price" => $booking->total_amount, "start_date" => $this->formatDateWithSuffix($data->start_date), "end_date" => $this->formatDateWithSuffix($data->end_date), "days_count" => $date_result, "user_ name" => $user->name, "category_id" => $request->category_id, "price" => $labour_booking_data->labour_amount / $labour_booking_data->labour_quantity, 'total_price' => $booking->total_amount / $labour_booking_data->labour_quantity];
        }

        // \Log::info("additional_data");

        // \Log::info($additional_data);
        // \Log::info("waleeeeettttt");

        // \Log::info($wallet_use);
        // \Log::info($request->transaction_type);

        // \Log::info($is_razorpay);

        if ($request->transaction_type == "post_paid" || $wallet_use === true) {
            // \Log::info("inside send notttttt");

            $firebaseService = new SendNotificationJob();
            $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);
            // \Log::info("firebaseService");
            // \Log::info($firebaseService);
        }
        // else if ($wallet_use === true) {
        //     $firebaseService = new SendNotificationJob();
        //     $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);
        // }
       
     
        $order_datas = ($order??null);

        return response()->json([
            "message"     => "Booking created successfully",
            "order_id"    => $order_datas['razorpay_order_id'] ?? $order->id ?? $order['id'] ?? null,
            "checkout_id" => $data->id,
            "is_razorpay" => $is_razorpay,
            "is_wallet"   => $wallet_use,
            "status"      => true,
        ], 200);
    }

    public function fetchOrder(Request $request)
    {
        $request->validate([
            "order_id" => "required",
        ]);

        $fetchOrder        = $this->razorpay->fetchOrder($request->order_id);
        $business_settings = BusinessSetting::pluck("value", "key")->toArray();
        $services_charges  = $business_settings['service_charges'];
        if ($fetchOrder['status'] == true) {
            if ($request->is_wallet == "partial_use") {

                $user_wallet         = Wallet::where("user_id", auth()->user()->id)->first();
                $user_wallet->amount = 0;
                $user_wallet->save();
            }

            $amount = $request->amount;

            $data                    = new Checkout();
            $data->start_date        = $request->start_date;
            $data->end_date          = $request->end_date;
            $data->start_time        = $request->start_time;
            $data->end_time          = $request->end_time;
            $data->address_id        = $request->address_id;
            $data->user_id           = auth()->user()->id;
            $data->category_id       = $request->category_id;
            $data->area_id           = $request->area_id;
            $data->labour_quantity   = $request->quantity;
            $data->alternate_number  = $request->alternate_number;
            $data->note              = $request->note;
            $data->transaction_type  = $request->transaction_type;
            $data->labour_booking_id = $request->labour_booking_id;
            $data->lat_long          = $request->lat_long;

            $data->save();

            $booking                  = new Booking();
            $booking->user_id         = auth()->user()->id;
            $booking->total_amount    = $amount;
            $booking->service_charges = $services_charges;
            if ($request->use_wallet == 'yes') {
                $booking->payment_status = 'captured';
            } else {
                $booking->payment_status = 'failed';
            }
            $booking->checkout_id          = $data->id;
            $booking->quantity_required    = $request->quantity;
            $booking->otp                  = mt_rand(111111, 999999);
            $booking->transaction_type     = $request->transaction_type;
            $booking->labour_amount        = $request->labour_amount;
            $booking->commission_amount    = $request->commission_amount;
            $booking->total_labour_charges = $request->total_labour_charges;
            $booking->labour_booking_id    = $request->labour_booking_id;

            if ($request->transaction_type == 'pre_paid') {
                $booking->razorpay_status = "created";
            } else {
                $booking->razorpay_status = "pending";
                $booking->razorpay_type   = "offline";

            }

            $booking->save();

            $booking_data = Booking::where('checkout_id', $fetchOrder["checkout_id"])->first();

            if ($booking_data->transaction_type == "pre_paid") {
                Booking::where("user_id", auth()->user()->id)->where("checkout_id", $fetchOrder["checkout_id"])->update([
                    "payment_status" => "captured",
                    "otp"            => mt_rand(111111, 999999),
                ]);
            } else {
                Booking::where("user_id", auth()->user()->id)->where("checkout_id", $fetchOrder["checkout_id"])->update([
                    "payment_status" => "captured",
                    "is_work_done"   => "1",
                    "otp"            => mt_rand(111111, 999999),
                ]);
            }

            $checkout_data       = Checkout::where('id', $fetchOrder["checkout_id"])->first();
            $labour_booking_data = LabourBooking::where('id', $checkout_data->labour_booking_id)->first();

            $category_id = $checkout_data->category_id;
            $user        = User::find(auth()->user()->id);
                                                // $user->update(["lat_long" => $request->lat_long]);
            $earthRadius                = 6371; // Earth radius in kilometers
            $business_settings          = BusinessSetting::pluck("value", "key")->toArray();
            $radius                     = $business_settings['radius'];
            list($latitude, $longitude) = explode(',', $checkout_data->lat_long);

            $latFrom = deg2rad($latitude);
            $lonFrom = deg2rad($longitude);

            // Calculate bounding box
            $latDelta = $radius / $earthRadius;
            $lonDelta = $radius / ($earthRadius * cos($latFrom));

            $latMin = rad2deg($latFrom - $latDelta);
            $latMax = rad2deg($latFrom + $latDelta);
            $lonMin = rad2deg($lonFrom - $lonDelta);
            $lonMax = rad2deg($lonFrom + $lonDelta);

            // get area first nearest to user's co-ordinate
            $areas = Areas::selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
                ->where('category_id', $checkout_data->category_id)
                ->whereBetween('latitude', [$latMin, $latMax])
                ->whereBetween('longitude', [$lonMin, $lonMax])
                ->with("category:id,title,image")->take(1)
                ->get();

            $labours = '';

            if (! empty($areas)) {

                // \Log::info("inside area");
                // \Log::info($areas);
                $labours = User::where('type', 'labour')
                    ->whereHas('category', function ($query) use ($category_id) {
                        $query->where('category_id', $category_id);
                    })
                    ->get()
                    ->filter(function ($labour) use ($latitude, $longitude, $radius, $request, $checkout_data) {
                        [$labourLatitude, $labourLongitude] = explode(',', $checkout_data->lat_long);
                        $distance                           = $this->haversineGreatCircleDistance(
                            $latitude,
                            $longitude,
                            $labourLatitude,
                            $labourLongitude
                        );
                        return $distance <= $radius;
                    })
                    ->map(function ($labour) {
                        $labour->type = 'labour';
                        return $labour;
                    })->pluck("device_id")->toArray();
            }

            $user_address = Address::where("user_id", auth()->user()->id)->where("is_primary", "yes")->first();

            if (! $user_address) {
                return response([
                    "message" => "Address is required",
                    "status"  => true,
                ], 400);
            }

            $diff        = (strtotime($checkout_data->end_date) - strtotime($checkout_data->start_date));
            $date_result = abs(round($diff) / 86400) + 1;
            // \Log::info("labours deatils");
            // \Log::info($labours);
            $title           = "New Job Available11";
            $message         = "You have a new job available.";
            $device_ids      = $labours;
            $additional_data = ["category_name" => "Helper", "address" => $user_address->address, "booking_id" => $booking_data->id, "start_time" => $this->formatTimeWithAMPM($checkout_data->start_time), "end_time" => $this->formatTimeWithAMPM($checkout_data->end_time), "price" => $booking_data->total_amount, "start_date" => $this->formatDateWithSuffix($checkout_data->start_date), "end_date" => $this->formatDateWithSuffix($checkout_data->end_date), "days_count" => $date_result, "user_ name" => $user->name, "category_id" => $request->category_id, "price" => $labour_booking_data->labour_amount / $labour_booking_data->labour_quantity];

            $firebaseService = new SendNotificationJob();
            $firebaseService->sendNotification($device_ids, $title, $message, $additional_data);
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
            "data"   => $data,
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
            "rating"     => "required",
            "review"     => "required",
        ]);

        $data             = new UserReview();
        $data->review     = $request->review;
        $data->rating     = $request->rating;
        $data->user_id    = auth()->user()->id;
        $data->booking_id = $request->booking_id;
        $data->save();

        if (! empty($request->images)) {
            foreach ($request->images as $key => $value) {
                $datas            = new ReviewImage();
                $datas->review_id = $data->id;
                if ($value) {
                    $datas->image = FileUploader::uploadFile($value, 'images/review_images');
                }
                $datas->save();
            }
        }

        $labour_booking = Booking::where('id', $request->booking_id)->first();

        $labourAccepted = LabourAcceptedBooking::where('booking_id', $labour_booking->labour_booking_id)->get();
        if (! empty($labourAccepted)) {
            foreach ($labourAccepted as $key => $value) {
                $labour_rating             = new LabourRating();
                $labour_rating->labour_id  = $value['labour_id'];
                $labour_rating->booking_id = $request->booking_id;
                $labour_rating->rating     = $request->rating;
                $labour_rating->save();
            }
        }

        return response([
            "message" => "Review Added Successfully",
            "status"  => true,
        ], 200);

    }

    public function postPaidPayment(Request $request)
    {
        $request->validate([
            "booking_id"    => "required",
            "razorpay_type" => "required",
            "amount"        => "required",

        ]);

        \Log::info($request->all());

        $amount  = $request->amount;
        $booking = Booking::where('id', $request->booking_id)->first();

        if ($request->razorpay_type == "online") {

            if ($request->use_wallet == "yes") {
                $user_wallet = Wallet::where("user_id", auth()->user()->id)->first();

                if ($user_wallet->amount == 0) {

                    $order       = $this->razorpay->createOrder($amount, "INR", $booking->checkout_id);
                    $is_razorpay = true;
                }

                if ($user_wallet->amount < $amount) {
                    $partial_amount = $amount - $user_wallet->amount;
                    $user_wallet->decrement("amount", $user_wallet->amount);
                    $order       = $this->razorpay->createOrder($partial_amount, "INR", $booking->checkout_id);
                    $is_razorpay = true;
                } else {
                    $is_razorpay = false;
                    $user_wallet->decrement("amount", $amount);
                    $booking->is_work_done  = 1;
                    $booking->razorpay_type = $request->razorpay_type;

                    $booking->save();
                }

                Transactions::create([
                    "user_id"          => auth()->user()->id,
                    "amount"           => $amount,
                    "remark"           => "Labours Purchased",
                    "transaction_type" => "debited",
                ]);
            } else {
                $order       = $this->razorpay->createOrder($amount, "INR", $booking->checkout_id);
                $is_razorpay = 1;
            }

            $one_labour_amount   = $booking->labour_amount / $booking->quantity_required;
            $labour_booking_data = LabourBooking::where('id', $booking->labour_booking_id)->first();

            $fdate     = $labour_booking_data->start_date;
            $tdate     = $labour_booking_data->end_date;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval  = $datetime1->diff($datetime2);
            $days      = $interval->format('%a') + 1;

            $labour_payable_amount = $one_labour_amount * $days;

            $labours = LabourAcceptedBooking::where('booking_id', $labour_booking_data->id)->get();

            foreach ($labours as $key => $value) {
                $wallet = Wallet::where('user_id', $value['labour_id'])->first();

                $add_on_charges = ExtraTimeWork::with(['labour:id,name,email,phone'])->whereHas('labour', function ($q) use ($value) {
                    $q->where('users.id', $value['labour_id']);
                })->where('booking_id', $request->booking_id)->get();
                $add_amount = 0;
                if (! empty($add_on_charges)) {
                    foreach ($add_on_charges as $key1 => $value1) {
                        $count_labour          = ExtraTimeWorkLabour::where('extra_time_work_id', $value1['id'])->count();
                        $one_labour_add_amount = $value1['labour_amount'] / $count_labour;
                        $add_amount            = $add_amount + $one_labour_add_amount;
                    }
                }

                if (! empty($wallet)) {
                    $amount         = $wallet->amount + $labour_payable_amount + $add_amount;
                    $wallet->amount = $amount;
                    $wallet->save();
                } else {
                    $wallets          = new Wallet();
                    $wallets->user_id = $value['labour_id'];
                    $wallets->amount  = $labour_payable_amount + $add_amount;
                    $wallets->save();
                }

            }

            return response()->json([
                "message"     => "Booking created successfully",
                "order_id"    => $order['id'] ?? null,
                "checkout_id" => $booking->checkout_id,
                "is_razorpay" => $is_razorpay,
                "status"      => true,
            ], 200);

        } else if ($request->razorpay_type == "offline") {

            $booking                = Booking::where('id', $request->booking_id)->first();
            $booking->razorpay_type = "offline";
            $booking->is_work_done  = 1;
            $booking->save();

            $labour_booking_data = LabourBooking::where('id', $booking->labour_booking_id)->first();

            $fdate     = $labour_booking_data->start_date;
            $tdate     = $labour_booking_data->end_date;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval  = $datetime1->diff($datetime2);
            $days      = $interval->format('%a') + 1;

            $one = $booking->labour_amount * $days;

            $second = $booking->total_amount - $one;

            $labour_payable_commision_amount = $second / $booking->quantity_required;

            // $one_labour_commission_amount = $booking->commission_amount / $booking->quantity_required;

            // // \Log::info($days);

            // $labour_payable_commision_amount = $one_labour_commission_amount * $days;

            $labours = LabourAcceptedBooking::where('booking_id', $labour_booking_data->id)->get();
            // \Log::info("labourssss");
            // \Log::info($labours);
            foreach ($labours as $key => $value) {
                $wallet = Wallet::where('user_id', $value['labour_id'])->first();
                // \Log::info("walletttttt");

                // \Log::info($wallet);
                // \Log::info("labour_payable_commision_amount");

                // \Log::info($labour_payable_commision_amount);
                $add_on_charges = ExtraTimeWork::with(['labour:id,name,email,phone'])->whereHas('labour', function ($q) use ($value) {
                    $q->where('users.id', $value['labour_id']);
                })->where('booking_id', $request->booking_id)->get();
                $add_on_commission_amount = 0;
                if (! empty($add_on_charges)) {
                    foreach ($add_on_charges as $key1 => $value1) {
                        $count_labour             = ExtraTimeWorkLabour::where('extra_time_work_id', $value1['id'])->count();
                        $one_labour_add_amount    = $value1['labour_amount'] / $count_labour;
                        $one_labour_commision     = $value1['commission_amount'] / $count_labour;
                        $add_on_commission_amount = $add_on_commission_amount + $one_labour_commision;
                    }
                }
                if (! empty($wallet)) {
                    $amounts        = (int) ($wallet->amount) - ((int) $labour_payable_commision_amount + (int) $add_on_commission_amount);
                    $wallet->amount = $amounts;
                    $wallet->save();

                    // \Log::info("wallettttttaaaa");

                    // \Log::info($wallet);
                } else {
                    $wallets          = new Wallet();
                    $wallets->user_id = $value['labour_id'];
                    $wallets->amount  = '-' . ((int) $labour_payable_commision_amount + (int) $add_on_commission_amount);
                    $wallets->save();

                    // \Log::info("wallettttttbbbbbbbbbb");

                    // \Log::info($wallets);
                }

            }

            $is_razorpay = false;
            return response()->json([
                "message"     => "Booking created successfully",
                "order_id"    => null,
                "checkout_id" => $booking->checkout_id,
                "is_razorpay" => $is_razorpay,
                "status"      => true,
            ], 200);
        }
    }
}
