<?php
namespace App\Jobs;

use Firebase;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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
        $firebase  = $this->firebase->withServiceAccount(base_path('/firebase.json'));
        $messaging = $firebase->createMessaging();

        $notificationPayload = [
            'title' => $title,
            'body'  => $body,
        ];
        if ($image) {
            $notificationPayload['image'] = $image;
        }
        $notification = Notification::fromArray($notificationPayload);
        \Log::info("token");

        \Log::info($token);

        foreach ($token as $key => $t) {
            \Log::info("token111");
            \Log::info($key);

            $message = CloudMessage::withTarget("token", $t)
                ->withNotification($notification)
                ->withHighestPossiblePriority('high');

            if ($data) {
                $messaging->validate($message);
                $message = $message->withData($data);
            }

            try {
                // $messaging->validate($message);
                $messaging->send($message);
                // \Log::info($messaging->send($message));
            } catch (MessagingException $e) {
                \Log::info($e);
                print_r($e->errors());
                // Skip the token if it's not registered or found
                continue;

            } catch (InvalidMessage $e) {
                echo $e->getMessage();
                print_r($e->errors());
            }
        }
    }

}
