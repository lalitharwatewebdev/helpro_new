<?php

namespace App\Helpers;

class OTPGenerator{
    public static function generate(){
        $otp = '';

        for($i=0;$i<6;$i++){
            $otp .= rand(0,9);
        }
        return $otp;   
    }

    public static function sendMessage($otp,$phone){
        $apiKey = urlencode(env('MESSAGE_API_KEY'));
        // Message details
        $numbers = array($phone);
        $sender = urlencode(env('MESSAGE_SENDER_ID'));
        $message = rawurlencode($otp . ' is your OTP (One Time Password) for logging into the App. For security reasons, do not share the OTP. Regards Team Appdid Infotech LLP.');

        $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // Process your response here
        return $response;
    
    }
}