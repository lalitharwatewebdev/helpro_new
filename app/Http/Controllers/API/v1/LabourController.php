<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Rmunate\Utilities\SpellNumber;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Booking;
use App\Models\BusinessSetting;
use DateTime;

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


    public function invoice(Request $request)
    {
        $request->validate([
            "booking_id" => "required|exists:bookings,id"
        ]);
        $data = Booking::with(["user", "checkout.address", 'checkout.category'])->find($request->booking_id);

        // getting gst from business settings
        $gst = BusinessSetting::where("key","gst")->first();

        // $igst = $gst
        // return $data;

        $start_date = $data['checkout']['start_date']; 
        $end_date = $data['checkout']['end_date'];    
        
        $start_time = $data['checkout']['start_time']; 
        $end_time = $data['checkout']['end_time'];     
        
        $start_datetime = $start_date . ' ' . $start_time;  
        $end_datetime = $end_date . ' ' . $end_time;       
        
        $start = new DateTime($start_datetime);
        $end = new DateTime($end_datetime);
        
        $interval = $start->diff($end);



        //per hour price
        
        $total_amount  = (float) $data['service_charges'] + (float) $data['total_amount'];
        $per_hour_price  = (float) $data['total_amount'] / $interval->h;

        

        $total_amount_in_words = SpellNumber::value($total_amount)->locale('en')->toLetters();
        $pdf = Pdf::loadView("site.pdf.index", ['booking' => $data, 'total_amount_in_words' => $total_amount_in_words, "hours" => $interval->h,"per_hour_price" => $per_hour_price, "gst" => $gst]);
        return $pdf->stream();
    }
}
