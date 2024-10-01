<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourRejectedBooking;
use App\Models\LabourBooking;
use Illuminate\Http\Request;

use App\Jobs\SendNotificationJob;

class LabourAcceptBookingController extends Controller
{
    public function labourAcceptBooking(Request $request)
    {
        $request->validate([
            "labour_booking_code" => "required"
        ]);
        \Log::info("Whether to acceot for reject");
        // first get data from labour_bookings table by labour_booking_code
        $labour_booking_code = LabourBooking::with("user")->where("labour_booking_code", $request->labour_booking_code)->first();
        if (!empty($labour_booking_code)) {
            // checking if labour booking is done

            if ($request->booking_status == "accepted") {

                \Log::info("Booking Accepted");

                // $labourAccept = LabourAcceptedBooking::where("booking_id",$labour_booking_code->id)
                // ->where("labour_id",auth()->user()->id)->first();

                $booking_count = LabourAcceptedBooking::where("booking_id", $labour_booking_code->id)->count();

                if ($labour_booking_code->labour_quantity != $booking_count) {
                    $labourAccept = new LabourAcceptedBooking();
                    $labourAccept->labour_id = auth()->user()->id;
                    $labourAccept->booking_id = $labour_booking_code->id;
                    $labourAccept->save();

                    return response([
                        "message" => "Booking Accepted Successfully",
                        "status" => true
                    ], 200);
                } else {

                    return response([
                        "message" => "Slot is not empty",
                        "status" => true
                    ], 200);
                }
            }

            if ($request->booking_status == 'rejected') {
                \Log::info("Booking Rejected");
                $labourAccept = new LabourRejectedBooking();
                $labourAccept->labour_id = auth()->user()->id;
                $labourAccept->booking_id = $labour_booking_code->id;
                $labourAccept->save();

                if($labourAccept){
                    $firebaseService = new SendNotificationJob();
                    $firebaseService->sendNotification($labour_booking_code->user->device_id, "Booking Accepted", "Booking Accepted by " . auth()->user()->name);
                }

                return response([
                    "message" => "Booking Rejected",
                    "status" => true
                ], 400);
            }

        } else {
            return response([
                "message" => "Invalid Booking id",
                "status" => true
            ], 200);
        }
    }
}
