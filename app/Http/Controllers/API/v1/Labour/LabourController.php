<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\AcceptedBooking;
use App\Models\Areas;
use App\Models\Booking;
use App\Models\BookingRequest;
use App\Models\BusinessSetting;
use App\Models\Checkout;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourBooking;
use App\Models\LabourRejectedBooking;
use App\Models\RejectedBooking;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class LabourController extends Controller
{

    // public function sendNotification()
    // {
    //     $firebaseService = new SendNotificationJob();
    //     $labour_full_name = auth()->user()->name;
    //     $device_id = "cLlFb-JGQ6CJxedN39t9hU:APA91bG-f9rO-N5lwaNleoBrrr0hngTCDWV1Vb1I75v-9NlErczmaYysrdgTKzs3j0BEGWCUiR7i2J9W3W6eF514Tjau1a1WOeQqDfHjRccGI4fZCITqP2Jd61TA69aHJKpYGNiKy0aF";
    //     $message = "Booking Accepted By " . $labour_full_name;
    //     $title = "Booking Accepted";
    //     $firebaseService->sendNotification($device_id, $title, $message);

    //     return response([
    //         "message" => "Notification send to user successfully",

    //     ], 200);
    // }

    public function profile(Request $request)
    {

        $user_id = auth()->user()->id;
        $user_data = User::with("states:id,name", "cities:id,name")->where("type", "labour")->find($user_id);
        $wallet_amount = Wallet::where("user_id", auth()->user()->id)->first();

        if ($user_data) {
            $user_data->wallet_amount = $wallet_amount;
        }

        return response([
            "data" => $user_data,
            "status" => true,
        ], 200);
    }

    public function activeStatus(Request $request)
    {
        $data = User::find(auth()->user()->id);

        $data->is_online = $data->is_online == "yes" ? "no" : "yes";
        $data->save();
        return response([
            "message" => "Online Status Updated Successfully",
            "online_status" => $data->is_online,
            "status" => true,
        ], 200);
    }

    public function get()
    {
        $earthRadius = 6371;
        $latitude = 19.1985893;
        $longitude = 72.955762;

        $labour_id = auth()->user();
        $categories = $labour_id->category()->first();
        $category_data = User::where('id', auth()->user()->id)->with(['category'])->first();
        // \Log::info("ccccccccccccategory_data");
        // \Log::info($category_data);

        $category_id = $category_data->category[0]['id'] ?? '';
        $radius = 5;
        $booking_amount_data = AcceptedBooking::with("booking.checkout")->where("labour_id", auth()->user()->id)->get();

        // $total_amount = $booking_amount_data->sum(function($acceptedBooking){
        //     return $acceptedBooking;
        //     $start_date = $acceptedBooking->checkout->start_time;

        //     $end_date = $acceptedBooking->checkout->end_time;
        //     $diff = (strtotime($start_date) - strtotime($end_date));
        //     $labour_quantity = $acceptedBooking->checkout->labour_quantity;
        //     $area_price = $acceptedBooking->checkout->area->price;
        //     return ($area_price * $diff) / $labour_quantity;

        // return null
        // });

        $total_booking_accepted = LabourAcceptedBooking::where("labour_id", auth()->user()->id)->count();
        \Log::info("Accepted " . $total_booking_accepted);
        $total_rejected_booking = LabourRejectedBooking::where("labour_id", auth()->user()->id)->count();
        \Log::info("Rejected " . $total_rejected_booking);

        // Convert latitude and longitude from degrees to radians
        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);

        // Calculate bounding box
        $latDelta = $radius / $earthRadius;
        $lonDelta = $radius / ($earthRadius * cos($latFrom));

        $latMin = rad2deg($latFrom - $latDelta);
        $latMax = rad2deg($latFrom + $latDelta);
        $lonMin = rad2deg($lonFrom - $lonDelta);
        $lonMax = rad2deg($lonFrom + $lonDelta);

        // Get areas in bounding box
        $area = Areas::whereBetween('latitude', [$latMin, $latMax])
            ->whereBetween('longitude', [$lonMin, $lonMax])
            ->where("category_id", $category_id)
            ->get()

            ->filter(function ($area) use ($latitude, $longitude, $radius) {
                // Calculate distance to see if within radius
                $distance = $this->haversineGreatCircleDistance(
                    $latitude,
                    $longitude,
                    $area->latitude,
                    $area->longitude
                );
                return $distance <= $radius;
            })->first();
        // \Log::info($checkouts);
        \Log::info($area);
        \Log::info($category_id);

        // getting checkout id as per user location
        // if (!empty($area->id) && !empty($category_id)) {
        // $checkouts = Checkout::where("area_id", $area->id)
        //     ->where("category_id", $category_id)->get();

        $checkouts = Checkout::where("category_id", $category_id)->get();
        // $checkouts = Checkout::where("area_id", $area->id)
        // ->where("category_id", $category_id)->get();
        // } else {
        //     $checkout = [];
        // }

        // return $checkout;
        foreach ($checkouts as $checkout) {

            $get_bookings = Booking::where("checkout_id", $checkout->id)->whereColumn("quantity_required", "!=", "current_quantity")->first();

            if ($get_bookings) {

                $current_user_booking = BookingRequest::where("booking_id", $get_bookings->id)->where("user_id", auth()->user()->id)->first();
                $accepted_booking_by_labour = AcceptedBooking::where("labour_id", auth()->user()->id)->where("booking_id", $get_bookings->id)->first();
                $rejected_booking_by_labour = RejectedBooking::where("labour_id", auth()->user()->id)->where("booking_id", $get_bookings->id)->first();

                if (empty($current_user_booking) && empty($accepted_booking_by_labour) && empty($rejected_booking_by_labour) && ($get_bookings->quantity_required != $get_bookings->current_quantity)) {
                    $request_booking = new BookingRequest();
                    $request_booking->user_id = auth()->user()->id ?? '';
                    // $request_booking->area_id = $area->id;
                    $request_booking->checkout_id = $checkout->id;
                    $request_booking->category_id = $category_id;
                    $request_booking->booking_id = $get_bookings->id;
                    $request_booking->save();
                }
            }

            // this is checking to see if required quantity does not match current _current
        }

        // getting total amount of user booking
        $bookings = BookingRequest::with("checkout", "checkout.user:id,name", "checkout.address.states:id,name", "checkout.address.cities:id,name", "checkout.area")->where("user_id", auth()->user()->id)
            ->where("category_id", $category_id)->first();

        $bookings = LabourAcceptedBooking::where("labour_id", auth()->user()->id)->with("booking", "booking.user:id,name", "booking.address.states:id,name", "booking.address.cities:id,name")->orderBy('id', 'desc')->where('current_status', '!=', '2')->first();

        if (!empty($bookings)) {

            $labour_book = LabourBooking::where('id', $bookings->booking_id)->first();
            $razorpay_status = Booking::where("labour_booking_id", $labour_book->id)->first();
            $razorpay_type = $razorpay_status->razorpay_type ?? '';
        } else {
            $razorpay_type = "online";
        }

        // foreach ($bookings as $booking) {

        //     $start_date = $booking->checkout->start_date;
        //     $end_date = $booking->checkout->end_date;
        //     $diff = (strtotime($start_date) - strtotime($end_date));
        //     $date_result = abs(round($diff) / 86400) + 1;

        //     $labour_quantity = $booking->checkout->labour_quantity;
        //     $area_price = $booking->checkout->area->price;
        //     $final_price = ($area_price * $date_result) / $labour_quantity;
        //     $booking->labour_total_amount = intval($final_price);
        // }

        // getting total accepted booking amount
        $total_amount = AcceptedBooking::where("labour_id", auth()->user()->id)->sum("amount");

        // getting total amount of money from wallet
        $total_wallet_amount = Wallet::where("user_id", auth()->user()->id)->first();
        \Log::info("Wallet ::->" . $total_wallet_amount);

        return response([
            "bookings" => $bookings ?? [],
            "total_wallet_amount" => $total_wallet_amount->amount ?? 0,
            "total_booking_accepted" => $total_booking_accepted,
            "total_rejected_booking" => $total_rejected_booking,
            'razorpay_status' => $razorpay_type ?? '',
            "status" => true,
        ], 200);
    }

    public function history()
    {
        // Fetch accepted and rejected bookings
        $accepted_bookings = AcceptedBooking::with("booking.checkout.address.states:id,name", "booking.checkout.address.cities:id,name", "booking.checkout.user:id,name,phone,lat_long")
            ->where("labour_id", auth()->user()->id)
            ->latest()
            ->get();

        $rejected_bookings = RejectedBooking::with("booking.checkout.address.states:id,name", "booking.checkout.address.cities:id,name", "booking.checkout.user:id,name,phone,lat_long")
            ->where("labour_id", auth()->user()->id)
            ->latest()
            ->get();

        $combined_bookings = $accepted_bookings->merge($rejected_bookings);
        $sorted_bookings = $combined_bookings->sortByDesc('created_at');

        $bookings = $sorted_bookings->values();

        // $results = [];

        // foreach ($formatted_bookings as $booking) {
        //     $bookingData = $booking->booking;
        //     $start_date = $booking->booking->checkout->start_date;
        //     $end_date = $booking->booking->checkout->end_date;
        //     $diff = (strtotime($start_date) - strtotime($end_date));
        //     $date_result = abs(round($diff) / 86400) + 1;
        //     $labour_quantity = $booking->booking->checkout->labour_quantity;
        //     $area_price = $booking->booking->checkout->area->price;

        //     $final_price = ($area_price * $date_result) / $labour_quantity;

        //     $bookingData->labour_total_amount = intval($final_price);
        //     $results[] = $bookingData;
        // }

        // Optionally, you can sort $results if needed, e.g., by start_date
        // $sorted_results = collect($results)->sortByDesc('start_date')->values()->all();

        return response([
            "data" => $bookings,
            "status" => true,
        ], 200);
    }

    public function acceptedBooking(Request $request)
    {

        //   if($request->query("booking_status") == "accepted"){

        //     // $data = AcceptedBooking::with(["booking.user", "booking.checkout.address"])->where("labour_id", auth()->user()->id)->get();
        //     $data = AcceptedBooking::with(['booking.user','booking.checkout.address'])->get();`
        //     return response([
        //         "data" => $data,

        //         "status" => true
        //     ], 200);
        //   }

        //   if($request->booking_status == "rejected"){
        //         $data = RejectedBooking::with(["booking.user", "booking.checkout.address"])->where("labour_id", auth()->user()->id)->get();
        //     return response([
        //         "data" => $data,

        //         "status" => true
        //     ], 200);
        //   }
    }

    public function rejectedBooking()
    {
        $data = RejectedBooking::where("labour_id", auth()->user()->id)->get();
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

    // function to accept and reject the user booking
    public function acceptRejectBooking(Request $request)
    {
        $action = $request->action;
        $booking_id = $request->booking_id;
        $reason = $request->reason ?? '';

        $business_settings = BusinessSetting::pluck("value", "key")->toArray();

        // if user rejected the booking
        if (strtolower($action) == "rejected") {
            // add it to rejected booking table
            $booking_data = Booking::find($booking_id);
            $start_date = $booking_data->checkout->start_date;
            $end_date = $booking_data->checkout->end_date;
            $diff = (strtotime($start_date) - strtotime($end_date));
            $date_result = abs(round($diff / 86400)) + 1;

            $labour_quantity = $booking_data->checkout->labour_quantity;
            $labour_total_amount = $booking_data->total_amount;

            $final_price = (($labour_total_amount - $services_charges) / $labour_quantity);

            RejectedBooking::create([
                "labour_id" => auth()->user()->id,
                "checkout_id" => $booking_data->checkout_id,
                "amount" => $final_price,
                "booking_id" => $booking_id,
                "reason" => $reason,

            ]);

            // and also removed from booking request so that it is not displayed on front page of labour app
            BookingRequest::where("user_id", auth()->user()->id)->where("booking_id", $booking_id)->delete();

            return response([
                "message" => "Booking Rejected",
                "status" => true,
            ], 200);
        }

        // if user accepts the booking
        if (strtolower($action) == 'accepted') {
            $booking = Booking::with("user")->where("id", $booking_id)->first();

            // do calculate as per start date and end date and calculate the diff. btw the two to get total_amount
            $start_date = $booking->checkout->start_date;
            $end_date = $booking->checkout->end_date;
            $diff = (strtotime($start_date) - strtotime($end_date));
            $date_result = abs(round($diff / 86400)) + 1;

            $labour_quantity = $booking->checkout->labour_quantity;
            $labour_total_amount = $booking->total_amount;

            $final_price = (($labour_total_amount - $services_charges) / $labour_quantity);

            //  add it to accepted booking table
            AcceptedBooking::create([
                "labour_id" => auth()->user()->id,
                "booking_id" => $booking_id,
                "amount" => intval($final_price),
                "otp" => mt_rand(111111, 999999),
            ]);

            // Add it to transactions table
            Transactions::create([
                "user_id" => auth()->user()->id,
                "amount" => $final_price,
                "transaction_type" => "credited",
            ]);

            // and also add it to wallet

            // first check is user wallet is create in table
            $wallet = Wallet::where("user_id", auth()->user()->id)->first();

            if ($wallet) {
                $wallet->increment("amount", intval($final_price));
            } else {
                Wallet::create([
                    "user_id" => auth()->user()->id,
                    "amount" => intval($final_price),
                ]);
            }

            // we will also increment the quatity of current_quantity
            Booking::where("id", $booking_id)->increment("current_quantity", 1);

            // and at the same time we are retrieving the booking data
            $check_booking = Booking::where("id", $booking_id)->first();

            // and check if quantity required and current are same
            if ($check_booking->quantity_required == $check_booking->current_quantity) {
                // BookingRequest::where("booking_id", $booking_id)->delete();
                BookingRequest::where("user_id", auth()->user()->id)->where("booking_id", $booking_id)->delete();
            }

            // if it is same we will delete it as booking is completed and we dont want any more users in it

            $firebaseService = new SendNotificationJob();
            $labour_full_name = auth()->user()->name;
            $device_id = ["cLlFb-JGQ6CJxedN39t9hU:APA91bG-f9rO-N5lwaNleoBrrr0hngTCDWV1Vb1I75v-9NlErczmaYysrdgTKzs3j0BEGWCUiR7i2J9W3W6eF514Tjau1a1WOeQqDfHjRccGI4fZCITqP2Jd61TA69aHJKpYGNiKy0aF"];
            $message = "Booking Accepted By " . $labour_full_name;
            $title = "Booking Accepted";
            $firebaseService->sendNotification($device_id, $title, $message);

            return response([
                "message" => "Booking Accepted",
                "status" => true,
            ], 200);
        }
    }

    public function getBooking(Request $request)
    {
        $booking_status = $request->booking_status;

        if ($booking_status == "accepted") {

            $data = LabourAcceptedBooking::with(['booking.user', 'booking.address.states:id,name', 'booking.address.cities:id,name'])->where("labour_id", auth()->user()->id)->orderBy('id', 'desc')->get();

            foreach ($data as $key => $value) {
                $book_data = Booking::where('labour_booking_id', $value->booking_id)->first();

                $data[$key]['razorpay_status'] = $book_data->razorpay_type ?? '';
            }

            \Log::info($data);

            return response([
                "data" => $data,
                "success" => true,
            ], 200);
        } else {
            $data = LabourRejectedBooking::with(['booking.user', 'booking.address.states:id,name', 'booking.address.cities:id,name'])->where("labour_id", auth()->user()->id)->orderBy('id', 'desc')->get();
            \Log::info("Rejected Labour Booking" . $data);

            foreach ($data as $key => $value) {
                $book_data = Booking::where('labour_booking_id', $value->booking_id)->first();

                $data[$key]['razorpay_status'] = $book_data->razorpay_type ?? '';
            }

            return response([
                "data" => $data,
                "success" => true,
            ], 200);
        }
    }

    public function labourHistory()
    {
        $acceptedBooking = LabourAcceptedBooking::where("labour_id", auth()->user()->id)->get();
        $rejectedBooking = LabourRejectedBooking::where("labour_id", auth()->user()->id)->get();

        $combined_bookings = $acceptedBooking->merge($rejectedBooking);
        $combined_bookings = $combined_bookings->sortByDesc("created_at");

        return response([
            "data" => $combined_bookings,
            "status" => true,
        ], 200);
    }

    public function currentJob()
    {
        $data = LabourAcceptedBooking::with("booking")->where("labour_id", auth()->user()->id)->first();

        return response([
            "data" => $data,
            "status" => true,
        ], 200);
    }

    public function getLabourAmount(Request $request)
    {
        $booking_data = Booking::where('id', $request->booking_id)->first();

        $labour_booking_data = LabourBooking::where('id', $booking_data->labour_booking_id)->first();

        $is_accept_booking = LabourAcceptedBooking::where('booking_id', $booking_data->labour_booking_id)->where('labour_id', auth()->user()->id)->get();

        $is_accept_booking->is_work_done = 1;
        $is_accept_booking->save();

        $is_all_accepted_booking = LabourAcceptedBooking::where('booking_id', $booking_data->labour_booking_id)->where('is_work_done', '0')->get();

        if (empty($is_all_accepted_booking)) {
            $booking_data->is_work_done = 1;
            $booking_data->save();
        }

        return response([
            "message" => "Work Done Successfully",
            "status" => true,
        ], 200);

    }
}
