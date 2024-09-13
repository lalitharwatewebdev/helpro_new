<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LabourRedeem;
use App\Models\Wallet;

class ReturnExpiredRedeemRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redeem:return-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return expired redeem requests to the main wallet';


    public function handle()
    {
        // checking for now plus 2 hours time
        $twoHoursAgo = now()->subHours(2);
        
        // if payment status is still pending after 2 hours when last time it was created
        $exipredRequests = LabourRedeem::where("payment_status", "pending")
        ->where("created_at", '<', $twoHoursAgo)->get();
        
        foreach($exipredRequests as $request){
            $wallet = Wallet::where("user_id",$request->labour_id)->first();
            $wallet->amount += $request->amount;
            $wallet->save();
            $request->payment_status = 'rejected';
            $request->save();
        }
        
    }
}
