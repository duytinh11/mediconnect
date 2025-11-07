<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $this->wrap(function () use ($request) {
            return $request->user()->notifications()
                ->latest()
                ->paginate();
        });
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        return $this->wrap(function () use ($request, $notificationId) {
            $notification = $request->user()->notifications()->findOrFail($notificationId);
            $notification->markAsRead();

            return response()->json(['message' => 'Notification marked as read']);
        });
    }
}
