<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     * 
     * @authenticated
     * 
     * @return JsonResponse returns a list of all user's notifications
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $notifications = $user->notifications()
            ->with(['item'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return $this->success(NotificationResource::collection($notifications), 'Notifications fetched successfully');
    }

    /**
     * Display a specific notification
     * 
     * @authenticated
     * 
     * @param Notifications $notification
     * @return JsonResponse returns a specific notification
     */
    public function show(Request $request, Notifications $notification)
    {
        if (Gate::denies('read', $notification)) {
            return $this->forbidden('You are not authorized to read this notification.');
        }
        return $this->success(new NotificationResource($notification), 'Notification fetched successfully');
    }

    /**
     * Mark a specific notification as read
     * 
     * @authenticated
     * 
     * @param Notifications $notification
     * @return JsonResponse returns a specific notification
     */
    public function read(Request $request, Notifications $notification)
    {
        if (Gate::denies('read', $notification)) {
            return $this->forbidden('You are not authorized to read this notification.');
        }
        $notification->update(['is_read' => true]); 
        return $this->success(new NotificationResource($notification), 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     * 
     * @authenticated
     * 
     * @return JsonResponse returns all notifications
     */
    public function readAll(Request $request)
    {
        $user = auth('sanctum')->user();
        $count = $user->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return $this->success(NotificationResource::collection($user->notifications()->get()), "{$count} notifications marked as read");
    }

    /**
     * Delete a specific notification
     * 
     * @authenticated
     * 
     * @param Notifications $notification
     * @return JsonResponse returns a specific notification
     */
    public function delete(Request $request, Notifications $notification)
    {
        if (Gate::denies('delete', $notification)) {
            return $this->forbidden('You are not authorized to delete this notification.');
        }
        $notification->delete();
        return $this->success([], 'Notification deleted successfully');
    }

    /**
     * Delete all notifications
     * 
     * @authenticated
     * 
     * @return JsonResponse returns all notifications
     */
    public function deleteAll(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->notifications()
            ->delete();
        return $this->success([], 'All notifications deleted successfully');
    }
}
