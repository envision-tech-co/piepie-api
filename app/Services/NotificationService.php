<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\DeviceToken;
use App\Models\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        protected FcmService $fcmService
    ) {}

    /**
     * Register a device token for push notifications.
     */
    public function registerDevice(Customer|ServiceProvider $user, string $fcmToken, string $platform): DeviceToken
    {
        $token = DeviceToken::updateOrCreate(
            ['fcm_token' => $fcmToken],
            [
                'tokenable_type' => get_class($user),
                'tokenable_id' => $user->id,
                'platform' => $platform,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        // Deactivate other tokens for same user on same platform
        DeviceToken::where('tokenable_type', get_class($user))
            ->where('tokenable_id', $user->id)
            ->where('platform', $platform)
            ->where('id', '!=', $token->id)
            ->update(['is_active' => false]);

        return $token;
    }

    /**
     * Remove (deactivate) a device token.
     */
    public function removeDevice(string $fcmToken): void
    {
        DeviceToken::where('fcm_token', $fcmToken)->update(['is_active' => false]);
    }

    /**
     * Send a notification to a single recipient.
     */
    public function send(Customer|ServiceProvider $recipient, string $type, string $title, string $body, array $data = []): void
    {
        // Create in-app notification record
        AppNotification::create([
            'notifiable_type' => get_class($recipient),
            'notifiable_id' => $recipient->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data ?: null,
            'sent_at' => now(),
        ]);

        // Get active device tokens
        $tokens = DeviceToken::where('tokenable_type', get_class($recipient))
            ->where('tokenable_id', $recipient->id)
            ->where('is_active', true)
            ->get();

        foreach ($tokens as $deviceToken) {
            try {
                $this->fcmService->send($deviceToken->fcm_token, $title, $body, $data);
            } catch (\Throwable $e) {
                Log::debug("Push notification failed for token {$deviceToken->fcm_token}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Send to multiple recipients.
     */
    public function sendToMultiple(array $recipients, string $type, string $title, string $body, array $data = []): void
    {
        foreach ($recipients as $recipient) {
            $this->send($recipient, $type, $title, $body, $data);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId, Customer|ServiceProvider $user): void
    {
        AppNotification::where('id', $notificationId)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnread(Customer|ServiceProvider $user): Collection
    {
        return AppNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }
}
