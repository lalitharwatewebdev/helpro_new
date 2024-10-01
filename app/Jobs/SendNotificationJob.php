<?php

namespace App\Jobs;

use Firebase;
use Illuminate\Bus\Queueable;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

class SendNotificationJob
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $firebase;
    public function __construct()
    {
        $this->firebase = app(Factory::class);
    }
    public function sendNotification(array $token = [], string $title, string $body, array $data = [], string $image = null)
    {

        $firebase = $this->firebase->withServiceAccount(base_path('/firebase.json'));
        $messaging = $firebase->createMessaging();

        $notificationPayload = [
            'title' => $title,
            'body' => $body,
        ];
        // Add the image to the notification payload if provided
        if ($image) {
            $notificationPayload['image'] = $image;
        }
        $notification = Notification::fromArray($notificationPayload);
        
    
        foreach($token as $t){
                $message = CloudMessage::withTarget("token",$t)
            ->withNotification($notification)
            ->withHighestPossiblePriority('high');
        if ($data) {
            $message = $message->withData($data);
        }
         $messaging->send($message);    
        }
        
    }
}