<?php

namespace App\Http\Controllers\User;

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

        return !empty($adminIds) ? array_unique($adminIds) : [1];
    }

    /**
     * Gửi tin nhắn từ User tới Admin
     */
    public function send(Request $request)
    {
        // 1. Lấy nội dung từ request
        $messageText = $request->input('message');

        // 2. Kiểm tra nội dung trống
        if (empty(trim((string) $messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        // 3. Xác định Admin nhận tin
        $adminIds = $this->getAdminIds();
        // Lấy admin vừa trò chuyện gần nhất hoặc admin đầu tiên
        $lastAdminMsg = Message::where('receiver_id', Auth::id())
            ->whereIn('sender_id', $adminIds)
            ->latest()
            ->first();

        $receiverId = $lastAdminMsg ? $lastAdminMsg->sender_id : reset($adminIds);

        try {
            // 4. Lưu tin nhắn vào Database
            $message = Message::create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $receiverId,
                'content'     => $messageText,
                'is_read'     => false,
            ]);

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể gửi tin nhắn: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lấy lịch sử chat giữa User hiện tại và Admin
     */
    public function getMessages()
    {
        $userId = Auth::id();
        $adminIds = $this->getAdminIds();

        // Đánh dấu các tin nhắn từ BQT Admin gửi tới User là đã đọc
        Message::where('receiver_id', $userId)
            ->whereIn('sender_id', $adminIds)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Lấy toàn bộ hội thoại giữa 2 bên (User và BQT Admin)
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $adminIds) {
                // Tin nhắn User gửi cho Admin
                $q->where('sender_id', $userId)
                  ->whereIn('receiver_id', $adminIds);
            })
            ->orWhere(function ($q) use ($userId, $adminIds) {
                // Tin nhắn Admin phản hồi cho User
                $q->whereIn('sender_id', $adminIds)
                  ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
