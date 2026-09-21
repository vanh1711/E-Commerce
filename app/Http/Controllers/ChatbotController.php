<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = trim($request->input('message'));
        
        // 1. Lấy danh sách sản phẩm thực tế từ Database
        $products = Product::select('name', 'price', 'brand', 'description', 'stock')
            ->latest()
            ->take(20)
            ->get();
        
        $productListStr = "";
        foreach ($products as $product) {
            $status = ($product->stock ?? 1) > 0 ? "Còn hàng" : "Hết hàng";
            $productListStr .= "- {$product->name} (Hãng: {$product->brand}, Giá: " . number_format($product->price, 0, ',', '.') . " đ - {$status})\n";
        }

        // 2. Kiểm tra API Key Gemini từ .env
        $apiKey = env('GEMINI_API_KEY');
        
        $systemPrompt = "Bạn là Trợ lý AI tư vấn bán hàng thông minh của cửa hàng điện thoại PhoneStore.
Quy tắc trả lời:
1. Luôn trả lời lịch sự, thân thiện bằng tiếng Việt, tư vấn nhiệt tình và ngắn gọn.
2. Sử dụng danh sách sản phẩm thực tế của cửa hàng bên dưới để gợi ý và báo giá chính xác.
3. Nếu khách hàng hỏi vấn đề phức tạp, khiếu nại, hỗ trợ bảo hành riêng biệt hoặc muốn gặp người thật, hãy gợi ý khách nhấn nút 'Liên hệ Admin' để chat trực tiếp với Quản trị viên.
4. Trình bày bằng gạch đầu dòng rõ ràng, làm nổi bật tên máy và mức giá.

Danh sách sản phẩm hiện có của PhoneStore:
{$productListStr}";

        if (!empty($apiKey)) {
            $modelsToTry = ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro'];
            
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::timeout(10)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $systemPrompt . "\n\nKhách hàng hỏi: " . $userMessage]
                                ]
                            ]
                        ]
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if (!empty($reply)) {
                            return response()->json([
                                'reply' => trim($reply),
                                'source' => 'gemini'
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Tiếp tục thử model kế tiếp hoặc fallback
                }
            }
        }

        // 3. Phản hồi thông minh cục bộ (Smart Rule-Based Fallback)
        $lowerMsg = mb_strtolower($userMessage, 'UTF-8');
        $reply = "";

        if (str_contains($lowerMsg, 'chào') || str_contains($lowerMsg, 'hi') || str_contains($lowerMsg, 'hello')) {
            $reply = "Xin chào bạn! 👋 Tôi là **Trợ lý AI PhoneStore**.\nTôi có thể giúp bạn tìm kiếm các dòng điện thoại iPhone, Samsung, Xiaomi mới nhất hoặc tư vấn cấu hình phù hợp ngân sách.\n\nBạn đang quan tâm đến sản phẩm nào ạ?";
        } elseif (str_contains($lowerMsg, 'iphone') || str_contains($lowerMsg, 'apple')) {
            $ipList = $products->filter(fn($p) => stripos($p->name, 'iPhone') !== false || stripos($p->brand, 'Apple') !== false);
            if ($ipList->count() > 0) {
                $reply = "Dạ tại **PhoneStore** đang có sẵn các dòng iPhone chính hãng:\n";
                foreach ($ipList as $ip) {
                    $reply .= "• **{$ip->name}**: " . number_format($ip->price, 0, ',', '.') . " đ\n";
                }
                $reply .= "\nTất cả máy đều bảo hành 12 tháng chính hãng và miễn phí giao hàng toàn quốc ạ!";
            } else {
                $reply = "Dạ hiện tại dòng iPhone đang chuẩn bị về thêm đợt hàng mới. Bạn có thể xem thêm các dòng máy Samsung Flagship hoặc nhấn **Liên hệ Admin** để đặt trước nhé!";
            }
        } elseif (str_contains($lowerMsg, 'samsung') || str_contains($lowerMsg, 'galaxy')) {
            $samList = $products->filter(fn($p) => stripos($p->name, 'Samsung') !== false || stripos($p->brand, 'Samsung') !== false);
            if ($samList->count() > 0) {
                $reply = "Dạ danh sách điện thoại **Samsung Galaxy** nổi bật đang có giá cực tốt:\n";
                foreach ($samList as $sam) {
                    $reply .= "• **{$sam->name}**: " . number_format($sam->price, 0, ',', '.') . " đ\n";
                }
                $reply .= "\nCó hỗ trợ trả góp 0% và thanh toán qua MoMo/COD thuận tiện ạ!";
            } else {
                $reply = "Dạ các dòng Samsung Galaxy đang được khuyến mãi lớn, bạn có thể tham khảo danh sách sản phẩm trên trang chủ nhé!";
            }
        } elseif (str_contains($lowerMsg, 'giá') || str_contains($lowerMsg, 'tiền') || str_contains($lowerMsg, 'bao nhiêu')) {
            $reply = "Dạ bảng giá một số dòng Flagship bán chạy nhất tại PhoneStore:\n";
            foreach ($products->take(4) as $p) {
                $reply .= "• **{$p->name}**: " . number_format($p->price, 0, ',', '.') . " đ\n";
            }
            $reply .= "\nBạn có thể nhấn nút **Liên hệ Admin** ở trên để được nhân viên tư vấn chi tiết hơn nhé!";
        } elseif (str_contains($lowerMsg, 'ship') || str_contains($lowerMsg, 'giao hàng') || str_contains($lowerMsg, 'vận chuyển')) {
            $reply = "🚚 **Chính sách giao hàng tại PhoneStore**:\n• Giao nhanh toàn quốc liên kết cùng **GHN Express**.\n• Tự động tính cước chuẩn xác và định vị GPS.\n• Hỗ trợ thanh toán khi nhận hàng (COD) hoặc qua Ví MoMo.";
        } elseif (str_contains($lowerMsg, 'bảo hành') || str_contains($lowerMsg, 'đổi trả')) {
            $reply = "🛡️ **Chính sách bảo hành PhoneStore**:\n• Bảo hành 12 tháng chính hãng toàn diện.\n• 1 đổi 1 trong vòng 30 ngày nếu phát sinh lỗi nhà sản xuất.\n• Hỗ trợ kỹ thuật trọn đời máy.";
        } else {
            $reply = "Cảm ơn bạn đã nhắn tin cho PhoneStore! 👋\n\nHiện tại cửa hàng đang sẵn sàng nhiều mẫu điện thoại cao cấp với giá ưu đãi. Nếu bạn cần hỗ trợ đặt hàng hoặc tư vấn kỹ hơn, bạn có thể gửi câu hỏi chi tiết hoặc nhấn nút **'👨‍💼 Liên hệ Admin'** ở phía trên để trò chuyện trực tiếp với nhân viên tư vấn nhé!";
        }

        return response()->json([
            'reply' => $reply,
            'source' => 'local_bot'
        ]);
    }
}
