<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LabourRedeemExport;
use App\Http\Controllers\Controller;
use App\Models\LabourRedeem;
use App\Models\Wallet;
use Excel;
use Illuminate\Http\Request;

class LabourRedeemController extends Controller
{
    public function index()
    {
        $data = LabourRedeem::where("payment_status", "pending")->get();
        return view("content.tables.labour_redeem", compact("data"));
    }

    public function acceptLabourRedeem(Request $request)
    {
        if ($request->status == "accepted") {
            $data = LabourRedeem::where("id", $request->id)->update(['payment_status' => $request->status]);
        } else if ($request->status == "rejected") {
            $labour_redeem = LabourRedeem::where("id", $request->id)->first();
            $wallet = Wallet::where('user_id', $labour_redeem->labour_id)->first();
            $amount = ($wallet->amount ?? 0) + ($labour_redeem->amount ?? 0);

            // dd($amount);

            $data = LabourRedeem::where("id", $request->id)->update(['payment_status' => $request->status]);

            $wallet->amount = $amount;
            $wallet->save();

        }

        return response([
            "message" => "Status Updated Successfully",
            "reload" => true,
        ], 200);
    }

    public function export(Request $request)
    {
        $type = $request->type ?? '';
        return Excel::download(new LabourRedeemExport($type), 'labourredeem.xlsx');
    }
}
