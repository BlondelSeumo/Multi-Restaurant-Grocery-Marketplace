<?php

namespace App\Traits;

use App\Models\Settings;
use Illuminate\Support\Facades\Http;
use Log;

trait Notification
{
    private string $url = 'https://fcm.googleapis.com/fcm/send';

    public function sendNotification(array $receivers = [], ?string $message = '', ?string $title = null, $data = []): string
    {
        $serverKey = $this->firebaseKey();

        $fields = [
            'registration_ids' => $receivers,
            'notification' => [
                'body'  => $message,
                'title' => $title,
            ],
            'data' => $data
        ];

        $headers = [
            'Authorization' => "key=$serverKey",
            'Content-Type'  => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post($this->url, $fields);
        Log::error('INFO NOTIFY', [$response->body()]);
        return $response->body();
    }

    private function firebaseKey()
    {
        return Settings::adminSettings()->where('key', 'server_key')->pluck('value')->first();
    }
}
