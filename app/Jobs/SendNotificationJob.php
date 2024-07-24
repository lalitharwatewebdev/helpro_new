<?php

namespace App\Jobs;

use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $title = null;
    public $message = null;
    public $device_ids = [];
    public $small_picture = null;
    public $big_picture = null;
    public $channel = null;
    public $additional_data = [];

    public function __construct($title, $message, $device_ids = null, $small_picture = null, $big_picture = null, array $additional_data = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->device_ids = $device_ids;
        $this->small_picture = $small_picture;
        $this->big_picture = $big_picture;
        $this->additional_data = $additional_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = [];
        $contents = ["en" => $this->message];

        if (!empty($this->device_ids)) {
            $params['include_player_ids'] = $this->device_ids;
        } else {
            $params['included_segments'] = ['All'];
        }
        $params['contents'] = $contents;
        $params['headings'] = ["en" => $this->title];
        $params['data'] = $this->additional_data;
        // $params['data'] =  ['msg' => 'hello there'];
        $params['large_icon'] = $this->small_picture ?? asset('images/logo/logo.png');
        if ($this->channel) {
            $params['android_channel_id'] = $this->channel;
        }
        if ($this->big_picture != null) {
            $params['big_picture'] = $this->big_picture;
            // $params['ios_attachments'] = ['id' => asset($img)];
        }
        try {
            $signal = OneSignalFacade::sendNotificationCustom($params);
            return $signal;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
