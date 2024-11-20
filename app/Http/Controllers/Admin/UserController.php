<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LabourExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $data->phone = $request->phone;

        $data->save();

        return response([
            'message' => 'user updated successfully',
            'table' => 'users-table',
            "reload" => true,
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

    public function export(Request $request)
    {
        [$start_date, $end_date] = explode(" to ", $request->month);
        return Excel::download(new UsersExport($start_date, $end_date), 'users.xlsx');
    }

    public function labourExport(Request $request)
    {
        \Log::info($request->all());
        [$start_date, $end_date] = explode(" to ", $request->month);
        return Excel::download(new LabourExport($start_date, $end_date, $request->type), "labours.xlsx");
    }

    public function exportpassword()
    {
        return view("content.tables.export-password");
    }

    public function saveExportPassword(Request $request)
    {

        Admin::where('name', 'admin')->update(['export_password' => $request->new_password]);

        return response([
            'header' => 'Updated!',
            'message' => 'Updated successfully',
            'table' => 'users-table',
        ]);

    }

    public function verifyPassword(Request $request)
    {
        // dd($request->all());

        $is_match = Admin::where('name', 'admin')->where('export_password', $request->password)->first();
        // dd($is_match);
        if (!empty($is_match)) {

            return response([
                'success' => true,
                'message' => 'Match Password',
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Wrong Password',
            ]);
        }

    }
}
