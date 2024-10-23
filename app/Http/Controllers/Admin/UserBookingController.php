<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
}
