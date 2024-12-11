<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Log;

class OTPGenerator
{
    public static function generate()
    {
        $otp = '';

        for ($i = 0; $i < 6; $i++) {
            $otp .= rand(0, 9);
        }
        return $otp;
    }

    public static function sendMessage($otp, $number, $projectName = 'Helpro')
    {
        $data = array(
            'api_id' => env('MESSAGE_API_KEY'),
            'api_password' => env('MESSAGE_API_PASSWORD'),
            'sms_type' => "Appdid Universal OTP",
            'sms_encoding' => "1",
            'sender' => env('MESSAGE_API_SENDER_ID'),
            'number' => $number,
            'message' => $otp . " is your OTP (One Time Password) for logging into the App. For security reasons, do not share the OTP. Regards Team Appdid Infotech LLP.",
            'template_id' => "170770",
        );

        $data_string = json_encode($data);

        $response = HTTP::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('http://sms.appdidsms.in/api/send_sms', json_decode($data_string, true));

        self::trackOtp($response, env('MESSAGE_API_SENDER_ID'));

        return $response;
    }
    private static function trackOtp($response, $senderId)
    {
        try {
            $response = json_decode($response);
            $balance = $response->balance ?? 0;
            $sender = $senderId;
            $projectName = "Hira Trading";
            $trackingUrl = "https://otp-tracking.appdid.com/api/v1/track";
            Http::post($trackingUrl, [
                'project_name' => $projectName,
                'pending_balance' => $balance,
                'sender' => $sender,
            ]);
        } catch (\Throwable $th) {
            Log::info($th);
        }
    }

}
