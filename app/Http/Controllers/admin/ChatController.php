<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Hiển thị trang Quản lý Chat riêng biệt cho Admin
     */
    public function index(Request $request)
    {
        $selectedUserId = $request->query('user_id');
        $initialUser = null;
        if ($selectedUserId) {
            $initialUser = User::where('id', '!=', Auth::id())->find($selectedUserId);
        }

        $totalCustomers = User::where('id', '!=', Auth::id())
            ->where(function ($q) {
                $q->where('is_admin', false)->orWhereNull('is_admin');
            })->count();

        $unreadCount = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('admin.chat.index', compact('initialUser', 'totalCustomers', 'unreadCount'));
    }

    /**
     * Lấy danh sách TẤT CẢ khách hàng (kể cả chưa từng nhắn) kèm tin nhắn gần nhất và sắp xếp
     */
    public function getUsers(Request $request)
    {
        $adminId = Auth::id();
        $search = trim($request->input('q', $request->input('search', '')));

        // Query tất cả user không phải admin
        $query = User::where('id', '!=', $adminId)
            ->where(function ($q) {
                $q->where('is_admin', false)->orWhereNull('is_admin');
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->select('id', 'name', 'email', 'created_at')->get();
        $userIds = $users->pluck('id')->toArray();

        // Lấy toàn bộ tin nhắn liên quan giữa admin và danh sách user
        $messages = Message::where(function ($q) use ($adminId, $userIds) {
            $q->where('receiver_id', $adminId)->whereIn('sender_id', $userIds);
        })->orWhere(function ($q) use ($adminId, $userIds) {
            $q->where('sender_id', $adminId)->whereIn('receiver_id', $userIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $latestMsgMap = [];
        $unreadMap = [];

        foreach ($messages as $msg) {
            $otherId = ($msg->sender_id == $adminId) ? $msg->receiver_id : $msg->sender_id;
            if (!isset($latestMsgMap[$otherId])) {
                $latestMsgMap[$otherId] = [
                    'content'    => $msg->content,
                    'created_at' => $msg->created_at ? $msg->created_at->toISOString() : null,
                    'time_human' => $msg->created_at ? $msg->created_at->diffForHumans() : '',
                    'is_me'      => ($msg->sender_id == $adminId),
                ];
            }
            if ($msg->receiver_id == $adminId && !$msg->is_read) {
                $unreadMap[$otherId] = ($unreadMap[$otherId] ?? 0) + 1;
            }
        }

        $result = $users->map(function ($user) use ($latestMsgMap, $unreadMap) {
            $latest = $latestMsgMap[$user->id] ?? null;
            $lastTs = $latest && !empty($latest['created_at']) 
                ? strtotime($latest['created_at']) 
                : ($user->created_at ? strtotime($user->created_at) : 0);

            return [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'avatar_text'    => strtoupper(substr($user->name ?? 'U', 0, 1)),
                'latest_message' => $latest,
                'unread_count'   => $unreadMap[$user->id] ?? 0,
                'last_active_ts' => $lastTs,
                'has_chatted'    => !is_null($latest),
            ];
        })
        ->sortByDesc('last_active_ts')
        ->values();

        return response()->json($result);
    }

    /**
     * Lấy lịch sử tin nhắn của một User cụ thể & đánh dấu đã đọc
     */
    public function getMessages($userId)
    {
        $adminId = Auth::id();

        // Đánh dấu các tin nhắn từ khách gửi đến admin là đã đọc
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender')
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $customer = User::select('id', 'name', 'email', 'created_at')->find($userId);

        return response()->json([
            'customer' => $customer,
            'messages' => $messages,
        ]);
    }

    /**
     * Admin gửi tin nhắn phản hồi
     */
    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->user_id,
            'content'     => $request->message,
            'is_read'     => false, // Đánh dấu chưa đọc để phía khách hàng có thông báo
        ]);

        $message->load('sender');

        return response()->json($message);
    }
}
