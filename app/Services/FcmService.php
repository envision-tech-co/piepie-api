<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send a push notification via FCM v1 API.
     * Fire-and-forget — never throws.
     */
    public function send(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $projectId = config('services.fcm.project_id');
        $serverKey = config('services.fcm.server_key');

        if (!$projectId || !$serverKey) {
            Log::debug("FCM not configured, skipping push to {$fcmToken}");
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Ensure all data values are strings (FCM requirement)
        $stringData = array_map('strval', $data);

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'high',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($serverKey)
                ->timeout(5)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::debug("FCM send to {$fcmToken}: success");
                return true;
            }

            // Invalid token — mark inactive
            if ($response->status() === 404 || $response->status() === 400) {
                DeviceToken::where('fcm_token', $fcmToken)->update(['is_active' => false]);
                Log::debug("FCM send to {$fcmToken}: invalid token, marked inactive");
            } else {
                Log::debug("FCM send to {$fcmToken}: failed with status {$response->status()}");
            }

            return false;
        } catch (\Throwable $e) {
            Log::debug("FCM send to {$fcmToken}: exception - {$e->getMessage()}");
            return false;
        }
    }
}
