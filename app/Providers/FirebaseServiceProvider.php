<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Messaging::class, function ($app) {
            return $app->make(Messaging::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Perform service booting tasks here if needed
    }

    /**
     * Send a notification.
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @return array
     */
    public function sendNotification($deviceToken, $title = "Default Title", $body = "Default Body")
    {
        $messaging = $this->app->make(Messaging::class);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ])
            ->withData([
                'key' => 'value',
            ]);

        try {
            $messaging->send($message);
            return [
                'success' => true,
                'message' => 'Message sent successfully',
            ];
        } catch (MessagingException $e) {
            return [
                'success' => false,
                'message' => 'Message sending failed: ' . $e->getMessage(),
            ];
        }
    }
}
