<?php

namespace App\Http\Controllers;

use App\WebsocketNotification;
use App\Services\WebsocketNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WebsocketNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = $this->currentUser();
        if (!$user) {
            abort(401);
        }

        $notifications = $this->userNotifications($user)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('notifications.index', compact('notifications'));
    }

    public function fetch(Request $request, WebsocketNotificationService $notificationService)
    {
        $user = $this->currentUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $limit = min(max((int) $request->query('limit', 8), 1), 50);
        $notifications = $this->userNotifications($user)
            ->select('id', 'event_type', 'title', 'body', 'related_code', 'related_id', 'destination_url', 'occurred_at', 'read_at', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        foreach ($notifications as $notification) {
            if (in_array($notification->event_type, ['new_referral', 'transferred'], true)
                && preg_match('/\bby Dr\.\s*$/', $notification->body)) {
                $doctorName = $notificationService->referringDoctorName($notification->related_code);
                if ($doctorName !== '') {
                    $notification->body = preg_replace('/\bby Dr\.\s*$/', 'by Dr. ' . $doctorName, $notification->body);
                    $notification->save();
                }
            }
        }
        $unreadCount = $this->userNotifications($user)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'items' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead($id)
    {
        $user = $this->currentUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $updated = $this->userNotifications($user)
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['updated' => (bool) $updated]);
    }

    public function markAllRead()
    {
        $user = $this->currentUser();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->userNotifications($user)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['updated' => true]);
    }

    private function currentUser()
    {
        $sessionUser = Session::get('auth');
        if (!$sessionUser || empty($sessionUser->id)) {
            return null;
        }

        $user = DB::table('users')
            ->select('id', 'level')
            ->where('id', $sessionUser->id)
            ->first();

        if (!$user) {
            return null;
        }

        if (isset($sessionUser->level) && strcasecmp($sessionUser->level, $user->level) !== 0) {
            abort(403, 'The signed-in account role has changed. Please sign in again.');
        }

        return $user;
    }

    private function userNotifications($user)
    {
        return WebsocketNotification::where('user_id', $user->id)
            ->where(function ($query) use ($user) {
                $query->where('recipient_level', $user->level)
                    ->orWhereNull('recipient_level');
            });
    }
}