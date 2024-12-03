<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Rmunate\Utilities\SpellNumber;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Booking;

class LabourController extends Controller
{
    public function get(Request $request)
    {
        $type = $request->query("type");

        if ($type) {
            $data = User::whereHas("category", function ($query) use ($type) {
                $query->where("category_id", $type);
            })->with("category")->get();

            return response([
                "data" => $data,
                "status" => true,
            ], 200);
        }
        $data = User::with(["states:id,name", "cities:id,name", "labourImage:id,user_id,image"])->active()->where("labour_status", "pending")
            ->where("type", "labour")->get();

        return response([
            "data" => $data,
            "status" => true,
        ], 200);

    }


    public function invoice(Request $request){
        $data = Booking::with(["user","checkout.address",'checkout.category'])->find($request->booking_id);
        $total_amount  = (float) $data['service_charges'] + (float) $data['total_amount'];
        $total_amount_in_words = SpellNumber::value($total_amount)->locale('en')->toLetters();
        $pdf = Pdf::loadView("site.pdf.index", ['booking' => $data,'total_amount_in_words' => $total_amount_in_words]);
        return $pdf->stream();
    }

}
