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
     * Lấy danh sách ID của toàn bộ Quản trị viên (Admin)
     */
    protected function getAdminIds(): array
    {
        $adminIds = User::where('is_admin', 1)
            ->orWhere('role', 'admin')
            ->orWhere('email', 'admin@gmail.com')
            ->orWhere('email', 'admin@example.com')
            ->pluck('id')
            ->toArray();

        $currentId = Auth::id();
        if ($currentId && !in_array($currentId, $adminIds)) {
            $adminIds[] = $currentId;
        }

        return !empty($adminIds) ? array_unique($adminIds) : [1];
    }

    /**
     * Hiển thị trang Quản lý Chat riêng biệt cho Admin
     */
    public function index(Request $request)
    {
        $adminIds = $this->getAdminIds();
        $selectedUserId = $request->query('user_id');
        $initialUser = null;
        if ($selectedUserId) {
            $initialUser = User::whereNotIn('id', $adminIds)->find($selectedUserId);
        }

        $totalCustomers = User::whereNotIn('id', $adminIds)
            ->where(function ($q) {
                $q->where('is_admin', false)->orWhereNull('is_admin');
            })->count();

        $unreadCount = Message::whereIn('receiver_id', $adminIds)
            ->where('is_read', false)
            ->count();

        return view('admin.chat.index', compact('initialUser', 'totalCustomers', 'unreadCount'));
    }

    /**
     * Lấy danh sách TẤT CẢ khách hàng kèm tin nhắn gần nhất và sắp xếp
     */
    public function getUsers(Request $request)
    {
        $adminIds = $this->getAdminIds();
        $search = trim($request->input('q', $request->input('search', '')));

        // Query tất cả user không phải admin
        $query = User::whereNotIn('id', $adminIds)
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

        // Lấy toàn bộ tin nhắn liên quan giữa BQT Admin và danh sách user
        $messages = Message::where(function ($q) use ($adminIds, $userIds) {
            $q->whereIn('receiver_id', $adminIds)->whereIn('sender_id', $userIds);
        })->orWhere(function ($q) use ($adminIds, $userIds) {
            $q->whereIn('sender_id', $adminIds)->whereIn('receiver_id', $userIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $latestMsgMap = [];
        $unreadMap = [];

        foreach ($messages as $msg) {
            $isMsgFromAdmin = in_array($msg->sender_id, $adminIds);
            $otherId = $isMsgFromAdmin ? $msg->receiver_id : $msg->sender_id;

            if (!isset($latestMsgMap[$otherId])) {
                $latestMsgMap[$otherId] = [
                    'content'    => $msg->content,
                    'created_at' => $msg->created_at ? $msg->created_at->toISOString() : null,
                    'time_human' => $msg->created_at ? $msg->created_at->diffForHumans() : '',
                    'is_me'      => $isMsgFromAdmin,
                ];
            }
            if (in_array($msg->receiver_id, $adminIds) && !$msg->is_read) {
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
        $adminIds = $this->getAdminIds();

        // Đánh dấu các tin nhắn từ khách gửi đến BQT admin là đã đọc
        Message::where('sender_id', $userId)
            ->whereIn('receiver_id', $adminIds)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender')
            ->where(function ($q) use ($userId, $adminIds) {
                $q->where('sender_id', $userId)->whereIn('receiver_id', $adminIds);
            })
            ->orWhere(function ($q) use ($userId, $adminIds) {
                $q->whereIn('sender_id', $adminIds)->where('receiver_id', $userId);
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
