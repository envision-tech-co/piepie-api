<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterDeviceTokenRequest;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Register FCM device token.
     */
    public function register(RegisterDeviceTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        $this->notificationService->registerDevice(
            $user,
            $request->input('fcm_token'),
            $request->input('platform')
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered.',
        ]);
    }

    /**
     * Remove (deactivate) device token.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);

        $this->notificationService->removeDevice($request->input('fcm_token'));

        return response()->json([
            'success' => true,
            'message' => 'Device token removed.',
        ]);
    }

    /**
     * List notifications (newest first, paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = AppNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'body' => $n->body,
                    'data' => $n->data,
                    'read' => $n->read_at !== null,
                    'created_at' => $n->created_at->toISOString(),
                ];
            }),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $this->notificationService->markAsRead($id, $user);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        AppNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }
}
