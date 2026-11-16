<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $query = $request->user()->userNotifications()->latest();
            if ($request->boolean('unread_only')) {
                $query->where('is_read', false);
            }

return response()->json(['success' => true, 'message' => 'Notifications fetched successfully.', 'unread_count' => $request->user()->userNotifications()->where('is_read', false)->count(), 'data' => $query->paginate(15)]);
        });
    }

    public function markRead(Request $request, int $notification)
    {
        return $this->execute(function () use ($request, $notification) {
            $model = $request->user()->userNotifications()->whereKey($notification)->firstOrFail();
            $model->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Notification marked as read.', 'data' => $model]);
        });
    }

    public function markAllRead(Request $request)
    {
        return $this->execute(function () use ($request) {
            $request->user()->userNotifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        });
    }

    public function destroy(Request $request, int $notification)
    {
        return $this->execute(function () use ($request, $notification) {
            $request->user()->userNotifications()->whereKey($notification)->firstOrFail()->delete();

            return response()->json(['success' => true, 'message' => 'Notification deleted.']);
        });
    }
}
