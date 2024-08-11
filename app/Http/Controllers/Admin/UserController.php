<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {


        return view("content.tables.users");
    }
    public function store(Request $request)
    {

    }
    public function edit($id)
    {
        $name = new User();
        $data = $name::where('id', $id)->first();

        return response($data);
    }

    public function update(Request $request)
    {
        $data = User::where("id", $request->id)->first();

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->mobile_no;

        $data->save();

        return response([
            'message' => 'user updated successfully',
            'table' => 'users-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:users,id',
            'status' => 'required|in:active,blocked',
        ]);

        User::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'users status updated successfully',
            'table' => 'users-table',
        ]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'users deleted successfully',
            'table' => 'users-table',
        ]);
    }


    public function details(Request $request)
    {
        $data = User::with("addresses")->find($request->query("id"));
        $user_booking = Booking::with('labour', "checkout")->where("user_id", $request->query("id"))->where("payment_status", "captured")->get();
        // return $user_booking;
        return view("content.tables.details-users", compact("data"));
    }
}
