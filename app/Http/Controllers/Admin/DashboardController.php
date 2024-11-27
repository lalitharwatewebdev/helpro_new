<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\PromoCode;
use App\Models\Slider;
use App\Models\Subscription;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class DashboardController extends Controller
{

    public function home()
    {

        // $firebaseService = new SendNotificationJob();
        // $firebaseService->sendNotification(['dSFarbBWTS2qlud5WVFfIS:APA91bGEOJ4eojyeQOXw46anNV3hlrjEVkMDvZhHSMo1vzo4k7yslohYFDVkdQVOfgy7AxBq0AW4C866iaA3GlVYxb7kxe2wiqETFc9bSsT48F10JbwtEa0'], "hiii", "message");
        


        // $title = "New Job Available";
        // $message = "You have a new job available.";
        // $device_ids = "cWTfpKz-R8C9uim9xNHzKF:APA91bG9XyX4utJ5yAejC6AqORuB0ovrpnfRM_jcD4-Xbl03p7m0yGXdjfATMnFPc2PpodUO-K__Bre45UUib51V9dMFQfBlZsHQJPqguQMx1oSbm5LC8OWAMdN6qnvuMziOzCMtPKde";
        // $additional_data = ["category_name" => "Plumber","address" => "Thane(W)"];

        // $firebaseService = new SendNotificationJob();
        // $firebaseService->sendNotification($device_ids, $title, $message);

        // dispatch(new SendNotificationJob(

        // ));

        //    user graph
        $user_graph = DB::table("users")
            ->select(DB::raw("DATE(created_at) as date"), DB::raw("count(*) as count"))
            ->where("type", "user")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // labour graph
        $labour_graph = DB::table("users")
            ->select(DB::raw("DATE(created_at) as date"), DB::raw("count(*) as count"))
            ->where("type", 'labour')
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        $user_count = $user_graph->pluck("count")->toArray();
        $user_date = $user_graph->pluck("date")->toArray();

        $labour_count = $labour_graph->pluck("count")->toArray();
        $labour_date = $labour_graph->pluck("date")->toArray();

        $business_settings = BusinessSetting::pluck("value", "key");
        $promo_code = PromoCode::count();

        $users = User::where("type", "user")->count();
        $labours = User::where("type", "labour")->count();
        $user_slider = Slider::where("app_type", "user")->count();
        $labour_slider = Slider::where("app_type", "labour")->count();

        $subscription = Subscription::count();

        $total_category = Category::count();

        $data = compact("users", "labours", "user_slider", "labour_slider", "total_category", "user_count", "user_date", "labour_count", "labour_date", "subscription", "business_settings", "promo_code");
        return view('content.dashboard', $data);
    }

    public function downloadPdf()
    {
        $data = [
            [
                'quantity' => 1,
                'description' => '1 Year Subscription',
                'price' => '129.00',
            ],
        ];
        // return view('content.pdf.invoice', compact('data'));
        $pdf = Pdf::loadView('content.pdf.invoice', ['data' => $data]);
        // return $pdf->download('test.pdf');
        return $pdf->stream('test.pdf');
    }
}
