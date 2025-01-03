<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserBookingExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LabourAcceptedBooking;
use App\Models\LabourBooking;
use App\Models\User;
use App\Models\UserReview;
use App\Models\Wallet;
use Excel;
use Illuminate\Http\Request;

class UserBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $url = request()->url();
        $type = collect(explode('/', $url))->last();

        return view('content.tables.userbooking', compact("type"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::findOrFail($id);
        return response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::where("id", $request->id)->first();
        User::find($request->id)->update([
            "labour_status" => $request->type,
        ]);

        // $firebaseService = new SendNotificationJob();
        // $firebaseService->sendNotification($user->device_id->toArray, "Accepted", "You're accepted");

        return response([

            "message" => "Labour Updated Successfully",
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Labour deleted successfully',
            'table' => 'slider-table',
        ]);
    }

    public function status(Request $request)
    {
        // $request->validate([
        //     'id' => 'required|numeric|exists:students,id',
        //     'status' => 'required|in:active,blocked',
        // ]);

        User::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'Labour status updated successfully',
            'table' => 'labour-table',
        ]);
    }

    public function changeStatus(Request $request)
    {

        $booking = Booking::where('id', $request->id)->first();

        $labour_booking = LabourBooking::where('id', $booking->labour_booking_id)->first();
        $accepted_labour_count = LabourAcceptedBooking::where('booking_id', $booking->labour_booking_id)->count();
        $remaining_labour = (int) $labour_booking->labour_quantity - ((int) $accepted_labour_count ?? 0);

        if ($remaining_labour > 0) {
            $is_user_wallet_exist = Wallet::where('user_id', $booking->user_id)->first();
            $amount_return = $remaining_labour * $labour_booking->labour_amount;
            if (!empty($is_user_wallet_exist)) {
                $total_wallet_amount = ((float) $is_user_wallet_exist->amount ?? 0) + $amount_return;
                $is_user_wallet_exist->amount = $total_wallet_amount;
                $is_user_wallet_exist->save();
            } else {
                $wallet = new Wallet();
                $wallet->user_id = $booking->user_id;
                $wallet->amount = $amount_return;
                $wallet->save();
            }
        }

        $booking->booking_status = $request->status;
        $booking->save();

        return response([
            'message' => 'Status Change successfully',
            'table' => 'labour-table',
        ]);

    }

    public function export(Request $request)
    {
        $type = $request->type ?? '';
        return Excel::download(new UserBookingExport($type), 'userbookings.xlsx');
    }

    public function labourlist(Request $request)
    {
        $labour_accepted = LabourAcceptedBooking::where('booking_id', $request->labour_booking_id)->pluck('labour_id');
        $labour_data = User::whereIn('id', $labour_accepted)->get();
        return view('content.tables.labourlist', compact("labour_data"));
    }

    public function userreview(Request $request)
    {
        $labour_data = UserReview::with(['user', 'booking'])->where('booking_id', $request->id)->get();
        return view('content.tables.userreview', compact("labour_data"));

    }

    public function cancelBooking(Request $request)
    {
        // dd($request->all());

        $booking_data = Booking::where('id', $request->id)->first();
        $booking_data->booking_status = "cancelled";
        $booking_data->save();

        if ($request->day == "day") {
            $deducted_amount = $booking_data->total_amount - $booking_data->commission_amount;

            $userwallet = Wallet::where('user_id', $booking_data->user_id)->first();
            $total = $userwallet + $deducted_amount;
            $userwallet->amount = $total;
            $userwallet->save();
        } else {

        }
        return response([
            'message' => 'Status Change successfully',
            'table' => 'labour-table',
        ]);
    }
}
