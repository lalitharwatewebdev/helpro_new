<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Slider;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\PromoCode;
use App\Models\Subscription;
use DB;

use App\Models\User;

class DashboardController extends Controller
{

    public function home()
    {

    //    user graph
        $user_graph = DB::table("users")
        ->select(DB::raw("DATE(created_at) as date"),DB::raw("count(*) as count"))
        ->where("type","user")
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy(DB::raw('DATE(created_at)'))
        ->get();

        // labour graph
        $labour_graph = DB::table("users")
        ->select(DB::raw("DATE(created_at) as date"),DB::raw("count(*) as count"))
        ->where("type",'labour')
        ->groupBy(DB::raw("DATE(created_at)"))
        ->orderBy(DB::raw('DATE(created_at)'))
        ->get();

        $user_count = $user_graph->pluck("count")->toArray();
        $user_date = $user_graph->pluck("date")->toArray();

        $labour_count = $labour_graph->pluck("count")->toArray();
        $labour_date = $labour_graph->pluck("date")->toArray();

        $business_settings = BusinessSetting::pluck("value","key");
        $promo_code = PromoCode::count();
      
        $users = User::where("type", "user")->count();
        $labours = User::where("type", "labour")->count();
        $user_slider = Slider::where("app_type", "user")->count();
        $labour_slider = Slider::where("app_type", "labour")->count();

        $subscription = Subscription::count();

        $total_category = Category::count();

        $data = compact("users", "labours", "user_slider", "labour_slider", "total_category","user_count","user_date","labour_count","labour_date","subscription","business_settings","promo_code");
        return view('content.dashboard', $data);
    }


    public function downloadPdf()
    {
        $data = [
            [
                'quantity' => 1,
                'description' => '1 Year Subscription',
                'price' => '129.00'
            ]
        ];
        // return view('content.pdf.invoice', compact('data'));
        $pdf = Pdf::loadView('content.pdf.invoice', ['data' => $data]);
        // return $pdf->download('test.pdf');
        return $pdf->stream('test.pdf');
    }
}
