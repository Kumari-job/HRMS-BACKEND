<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('read_at')->latest()->paginate(10);
        return NotificationResource::collection($notifications);
    }
    public function readNotifications()
    {
        $notifications = Auth::user()->readNotifications()->latest()->paginate(10);
        return NotificationResource::collection($notifications);
    }

    public function unReadNotifications(Request $request)
    {
        if($request->filled('count_only') && $request->count_only == true){
            return Auth::user()->unReadNotifications()->count();
        }

        $notifications = Auth::user()->unReadNotifications()->latest()->paginate(10);
        return NotificationResource::collection($notifications);
    }

    public function markAllRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All unread notifications marked as read.'], 200);
    }

    public function markSingleRead($id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        $notification->markAsRead();
        return response()->json(['success' => true, 'message' => 'Notification marked as read.'], 200);
    }

    public function markAllUnread(): JsonResponse
    {
        Auth::user()->readNotifications->markAsUnread();
        return response()->json(['success' => true, 'message' => 'All read notifications marked as unread.'], 200);
    }

    public function markSingleUnread($id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        $notification->markAsUnread();
        return response()->json(['success' => true, 'message' => 'Notification marked as unread.'], 200);
    }

    public function notificationDelete($id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Notification deleted successfully.'], 200);
    }

    public function clearAll(): JsonResponse
    {
        Auth::user()->notifications()->delete();
        return response()->json(['success' => true, 'message' => 'All notifications cleared successfully.'], 200);
    }
}
