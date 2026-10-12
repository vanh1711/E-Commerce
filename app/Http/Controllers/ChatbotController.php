<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Category;

class ChatbotController extends Controller
{
    /**
     * Danh sách các nhóm chức năng / nhu cầu điện thoại nổi bật
     */
    private function getUseCasesList(): array
    {
        return [
            [
                'key'   => 'gaming',
                'label' => '🎮 Chơi Game Cấu Hình Cao',
                'short' => 'Chơi Game',
                'desc'  => 'Chip A18 Pro / Snapdragon 8 Gen 3, màn hình 120Hz mượt mà, tản nhiệt tốt',
                'icon'  => '🎮'
            ],
            [
                'key'   => 'camera',
                'label' => '📸 Chụp Ảnh & Quay Phim Đẹp',
                'short' => 'Chụp Ảnh',
                'desc'  => 'Camera 48-200MP, zoom quang học, chống rung OIS, AI bắt nét chuyên nghiệp',
                'icon'  => '📸'
            ],
            [
                'key'   => 'battery',
                'label' => '🔋 Pin Trâu Dùng Cả Ngày',
                'short' => 'Pin Trâu',
                'desc'  => 'Dung lượng pin lớn 5000mAh+, sạc nhanh siêu tốc, dùng bền bỉ cả ngày',
                'icon'  => '🔋'
            ],
            [
                'key'   => 'budget',
                'label' => '🎓 Học Tập & Sinh Viên Giá Rẻ',
                'short' => 'Học Tập/Giá Tốt',
                'desc'  => 'Mức giá vừa túi tiền, độ bền cao, mượt mà tác vụ học tập và mạng xã hội',
                'icon'  => '🎓'
            ],
            [
                'key'   => 'flagship',
                'label' => '✨ Doanh Nhân & Flagship Sang Trọng',
                'short' => 'Flagship Cao Cấp',
                'desc'  => 'Khung titan, màn hình gập cao cấp, đẳng cấp dẫn đầu công nghệ hiện đại',
                'icon'  => '✨'
            ],
            [
                'key'   => 'compact',
                'label' => '📱 Nhỏ Gọn Cầm Vừa Tay',
                'short' => 'Nhỏ Gọn',
                'desc'  => 'Màn hình 6.1 inch, mỏng nhẹ, dễ dàng thao tác 1 tay và bỏ túi tiện lợi',
                'icon'  => '📱'
            ],
        ];
    }

    /**
     * Gán icon phù hợp cho danh mục
     */
    private function getCategoryIcon(string $name): string
    {
        $lower = mb_strtolower($name, 'UTF-8');
        if (str_contains($lower, 'apple') || str_contains($lower, 'iphone')) return '🍏';
        if (str_contains($lower, 'samsung') || str_contains($lower, 'galaxy')) return '🌌';
        if (str_contains($lower, 'xiaomi') || str_contains($lower, 'redmi')) return '⚡';
        if (str_contains($lower, 'pixel') || str_contains($lower, 'google')) return '🤖';
        if (str_contains($lower, 'gaming') || str_contains($lower, 'rog')) return '🎮';
        if (str_contains($lower, 'thoại') || str_contains($lower, 'phone')) return '📱';
        return '📱';
    }

    /**
     * Chuẩn hóa danh sách sản phẩm để gửi về Chat UI
     */
    private function formatProductsForChat($products): array
    {
        return $products->map(function ($p) {
            $imageUrl = $p->image_url;
            $priceFormatted = number_format($p->price, 0, ',', '.') . ' đ';
            $stock = (int) ($p->stock ?? 0);

            return [
                'id'              => $p->id,
                'name'            => $p->name,
                'brand'           => $p->brand ?? 'Chính hãng',
                'price'           => (float) $p->price,
                'price_formatted' => $priceFormatted,
                'image_url'       => $imageUrl,
                'url'             => route('products.show', $p->id),
                'stock'           => $stock,
                'is_in_stock'     => $stock > 0,
                'status_text'     => $stock > 0 ? 'Còn hàng' : 'Tạm hết',
                'category_name'   => $p->category->name ?? 'Điện Thoại',
                'tags'            => $p->tags ?? [],
            ];
        })->values()->all();
    }

    /**
     * Lọc sản phẩm theo chức năng / nhu cầu
     */
    private function getProductsByUseCase(string $useCaseKey, int $limit = 8)
    {
        $query = Product::with('category')->latest();

        switch ($useCaseKey) {
            case 'gaming':
                $query->where(function ($q) {
                    $q->whereJsonContains('tags', 'gaming')
                      ->orWhere('name', 'like', '%Pro%')
                      ->orWhere('name', 'like', '%Ultra%')
                      ->orWhere('name', 'like', '%Fold%')
                      ->orWhere('name', 'like', '%Gaming%');
                });
                break;

            case 'camera':
                $query->where(function ($q) {
                    $q->whereJsonContains('tags', 'camera')
                      ->orWhere('name', 'like', '%Pro%')
                      ->orWhere('name', 'like', '%Ultra%')
                      ->orWhere('description', 'like', '%camera%');
                });
                break;

            case 'battery':
                $query->where(function ($q) {
                    $q->whereJsonContains('tags', 'battery')
                      ->orWhere('name', 'like', '%Plus%')
                      ->orWhere('name', 'like', '%Max%')
                      ->orWhere('name', 'like', '%Ultra%')
                      ->orWhere('description', 'like', '%pin%');
                });
                break;

            case 'budget':
                $query->where('price', '<=', 22000000)->orderBy('price', 'asc');
                break;

            case 'flagship':
                $query->where(function ($q) {
                    $q->where('price', '>=', 25000000)
                      ->orWhere('name', 'like', '%Pro Max%')
                      ->orWhere('name', 'like', '%Ultra%')
                      ->orWhere('name', 'like', '%Fold%');
                })->orderBy('price', 'desc');
                break;

            case 'compact':
                $query->where(function ($q) {
                    $q->whereJsonContains('tags', 'compact')
                      ->orWhere('name', 'like', '%mini%')
                      ->orWhere(function ($sub) {
                          $sub->where('name', 'not like', '%Max%')
                              ->where('name', 'not like', '%Ultra%')
                              ->where('name', 'not like', '%Plus%')
                              ->where('name', 'not like', '%Fold%');
                      });
                });
                break;
        }

        $results = $query->take($limit)->get();
        if ($results->isEmpty()) {
            $results = Product::with('category')->latest()->take($limit)->get();
        }

        return $results;
    }

    /**
     * API Chatbot xử lý hội thoại, lọc danh mục & chức năng
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'     => 'nullable|string|max:1000',
            'category_id' => 'nullable|integer',
            'use_case'    => 'nullable|string',
        ]);

        $userMessage = trim($request->input('message', ''));
        $lowerMsg = mb_strtolower($userMessage, 'UTF-8');

        // =========================================================================
        // 1. TRƯỜNG HỢP: NGƯỜI DÙNG CHỌN DANH MỤC CỤ THỂ (Quy tắc hoặc tham số category_id)
        // =========================================================================
        $categoryId = $request->input('category_id');
        $matchedCategory = null;

        if ($categoryId) {
            $matchedCategory = Category::find($categoryId);
        } else {
            // Kiểm tra người dùng có nhắn "Xem danh mục: [Tên]" hoặc "danh mục [Tên]"
            $allCats = Category::all();
            foreach ($allCats as $cat) {
                $catLower = mb_strtolower($cat->name, 'UTF-8');
                if (str_contains($lowerMsg, 'danh mục: ' . $catLower) || 
                    str_contains($lowerMsg, 'danh mục ' . $catLower) || 
                    str_contains($lowerMsg, 'danh muc ' . $catLower) || 
                    ($lowerMsg === $catLower)) {
                    $matchedCategory = $cat;
                    break;
                }
            }
        }

        if ($matchedCategory) {
            $products = $matchedCategory->products()->latest()->take(10)->get();
            $formattedProds = $this->formatProductsForChat($products);

            return response()->json([
                'reply'             => "Dạ đây là các sản phẩm nổi bật thuộc danh mục **{$matchedCategory->name}** tại PhoneStore:",
                'type'              => 'products',
                'filter_title'      => '📁 Danh mục: ' . $matchedCategory->name,
                'products'          => $formattedProds,
                'suggested_actions' => [
                    ['text' => '📁 Xem danh mục khác', 'action' => 'categories'],
                    ['text' => '🎮 ĐT chơi game', 'action' => 'use_case', 'value' => 'gaming'],
                    ['text' => '📸 ĐT chụp ảnh đẹp', 'action' => 'use_case', 'value' => 'camera'],
                ],
                'source'            => 'category_filter'
            ]);
        }

        // =========================================================================
        // 2. TRƯỜNG HỢP: NGƯỜI DÙNG YÊU CẦU XEM DANH MỤC (Show categories list)
        // =========================================================================
        if ($lowerMsg === 'danh mục' || $lowerMsg === 'danh muc' || 
            str_contains($lowerMsg, 'xem danh mục') || str_contains($lowerMsg, 'xem danh muc') || 
            str_contains($lowerMsg, 'danh sách danh mục') || str_contains($lowerMsg, 'các danh mục') || 
            str_contains($lowerMsg, 'category') || str_contains($lowerMsg, 'loại sản phẩm') || 
            str_contains($lowerMsg, 'thể loại')) {
            
            $categories = Category::withCount('products')->get()->map(function ($c) {
                return [
                    'id'             => $c->id,
                    'name'           => $c->name,
                    'products_count' => $c->products_count,
                    'icon'           => $this->getCategoryIcon($c->name),
                ];
            });

            return response()->json([
                'reply'             => "Dạ PhoneStore hiện có các danh mục sản phẩm sau. Bạn hãy nhấn vào một danh mục để xem các sản phẩm tương ứng nhé:",
                'type'              => 'categories',
                'categories'        => $categories,
                'suggested_actions' => [
                    ['text' => '🎮 Lọc theo chức năng', 'action' => 'use_cases'],
                    ['text' => '🔥 iPhone bán chạy', 'action' => 'message', 'value' => 'iPhone'],
                    ['text' => '✨ Samsung Galaxy', 'action' => 'message', 'value' => 'Samsung'],
                ],
                'source'            => 'categories_list'
            ]);
        }

        // =========================================================================
        // 3. TRƯỜNG HỢP: NGƯỜI DÙNG CHỌN CHỨC NĂNG CỤ THỂ (Gaming, Camera, Battery, ...)
        // =========================================================================
        $useCaseParam = $request->input('use_case');
        $detectedUseCase = null;

        if ($useCaseParam) {
            $detectedUseCase = $useCaseParam;
        } else {
            // Nhận diện qua từ khóa trong tin nhắn
            if (str_contains($lowerMsg, 'chơi game') || str_contains($lowerMsg, 'choi game') || 
                str_contains($lowerMsg, 'gaming') || str_contains($lowerMsg, 'liên quân') || 
                str_contains($lowerMsg, 'pubg') || str_contains($lowerMsg, 'genshin')) {
                $detectedUseCase = 'gaming';
            } elseif (str_contains($lowerMsg, 'chụp ảnh') || str_contains($lowerMsg, 'chup anh') || 
                      str_contains($lowerMsg, 'quay phim') || str_contains($lowerMsg, 'quay video') || 
                      str_contains($lowerMsg, 'camera') || str_contains($lowerMsg, 'sống ảo') || 
                      str_contains($lowerMsg, 'selfie')) {
                $detectedUseCase = 'camera';
            } elseif (str_contains($lowerMsg, 'pin trâu') || str_contains($lowerMsg, 'pin trau') || 
                      str_contains($lowerMsg, 'pin khỏe') || str_contains($lowerMsg, 'sạc nhanh') || 
                      str_contains($lowerMsg, 'dung lượng pin')) {
                $detectedUseCase = 'battery';
            } elseif (str_contains($lowerMsg, 'sinh viên') || str_contains($lowerMsg, 'học tập') || 
                      str_contains($lowerMsg, 'hoc tap') || str_contains($lowerMsg, 'giá rẻ') || 
                      str_contains($lowerMsg, 'gia re') || str_contains($lowerMsg, 'tiết kiệm') || 
                      str_contains($lowerMsg, 'bình dân')) {
                $detectedUseCase = 'budget';
            } elseif (str_contains($lowerMsg, 'flagship') || str_contains($lowerMsg, 'cao cấp') || 
                      str_contains($lowerMsg, 'cao cap') || str_contains($lowerMsg, 'sang trọng') || 
                      str_contains($lowerMsg, 'doanh nhân')) {
                $detectedUseCase = 'flagship';
            } elseif (str_contains($lowerMsg, 'nhỏ gọn') || str_contains($lowerMsg, 'nho gon') || 
                      str_contains($lowerMsg, 'vừa tay')) {
                $detectedUseCase = 'compact';
            }
        }

        if ($detectedUseCase) {
            $useCases = collect($this->getUseCasesList())->keyBy('key');
            $currentCase = $useCases->get($detectedUseCase) ?? [
                'label' => '🎮 Điện Thoại Theo Nhu Cầu',
                'desc'  => 'Cấu hình tối ưu'
            ];

            $products = $this->getProductsByUseCase($detectedUseCase);
            $formattedProds = $this->formatProductsForChat($products);

            return response()->json([
                'reply'             => "Dạ đây là các dòng điện thoại tối ưu nhất cho nhu cầu **{$currentCase['label']}** ({$currentCase['desc']}) tại PhoneStore:",
                'type'              => 'products',
                'filter_title'      => $currentCase['label'],
                'products'          => $formattedProds,
                'suggested_actions' => [
                    ['text' => '🎯 Xem chức năng khác', 'action' => 'use_cases'],
                    ['text' => '📁 Xem danh mục', 'action' => 'categories'],
                    ['text' => '🛡️ Chính sách bảo hành', 'action' => 'message', 'value' => 'bảo hành'],
                ],
                'source'            => 'use_case_filter'
            ]);
        }

        // =========================================================================
        // 4. TRƯỜNG HỢP: NGƯỜI DÙNG HỎI DANH SÁCH CHỨC NĂNG / NHU CẦU (Show use cases)
        // =========================================================================
        if (str_contains($lowerMsg, 'chức năng') || str_contains($lowerMsg, 'chuc nang') || 
            str_contains($lowerMsg, 'nhu cầu') || str_contains($lowerMsg, 'nhu cau') || 
            str_contains($lowerMsg, 'lọc theo') || str_contains($lowerMsg, 'tư vấn máy') || 
            str_contains($lowerMsg, 'tính năng')) {
            
            return response()->json([
                'reply'             => "Dạ PhoneStore hỗ trợ lọc và gợi ý điện thoại theo từng nhu cầu sử dụng thực tế. Bạn hãy chọn chức năng mong muốn dưới đây:",
                'type'              => 'use_cases',
                'use_cases'         => $this->getUseCasesList(),
                'suggested_actions' => [
                    ['text' => '📁 Xem theo danh mục', 'action' => 'categories'],
                    ['text' => '💰 Bảng giá máy', 'action' => 'message', 'value' => 'bảng giá'],
                ],
                'source'            => 'use_cases_list'
            ]);
        }

        // =========================================================================
        // 5. TRƯỜNG HỢP: TƯ VẤN SẢN PHẨM / GIÁ / CÂU HỎI THƯỜNG GẶP
        // =========================================================================
        // Lấy danh sách sản phẩm mẫu từ Database
        $sampleProducts = Product::with('category')->latest()->take(15)->get();

        // 5.1. Thử gọi API Gemini nếu có cấu hình Key
        $apiKey = env('GEMINI_API_KEY');
        if (!empty($apiKey)) {
            $productListStr = "";
            foreach ($sampleProducts as $sp) {
                $status = ($sp->stock ?? 1) > 0 ? "Còn hàng" : "Hết hàng";
                $productListStr .= "- {$sp->name} (Hãng: {$sp->brand}, Giá: " . number_format($sp->price, 0, ',', '.') . " đ - {$status})\n";
            }

            $systemPrompt = "Bạn là Trợ lý AI tư vấn bán hàng thông minh của PhoneStore.
Quy tắc:
1. Luôn trả lời lịch sự, thân thiện bằng tiếng Việt, ngắn gọn và rõ ràng.
2. Gợi ý đúng các sản phẩm hiện có của cửa hàng bên dưới.
3. Nếu khách muốn gặp nhân viên, hãy nhắc khách bấm 'Gặp Admin' ở góc trên.
4. Nhắc khách có thể gõ 'Danh mục' hoặc 'Chơi game', 'Chụp ảnh' để lọc sản phẩm nhanh.
Danh sách sản phẩm hiện có:
{$productListStr}";

            $modelsToTry = ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro'];
            foreach ($modelsToTry as $modelName) {
                try {
                    $response = Http::timeout(8)->withHeaders([
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
                                'reply'             => trim($reply),
                                'type'              => 'text',
                                'suggested_actions' => [
                                    ['text' => '📁 Danh mục', 'action' => 'categories'],
                                    ['text' => '🎮 ĐT Chơi Game', 'action' => 'use_case', 'value' => 'gaming'],
                                    ['text' => '📸 ĐT Chụp Ảnh', 'action' => 'use_case', 'value' => 'camera'],
                                ],
                                'source'            => 'gemini'
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Chuyển qua rule fallback
                }
            }
        }

        // 5.2. Rule-based thông minh dự phòng
        if (str_contains($lowerMsg, 'chào') || str_contains($lowerMsg, 'hi') || str_contains($lowerMsg, 'hello')) {
            $reply = "Xin chào bạn! 👋 Tôi là **Trợ lý AI PhoneStore**.\nTôi có thể giúp bạn xem danh mục sản phẩm, tìm kiếm điện thoại theo nhu cầu (chơi game, chụp ảnh, pin trâu,...) và báo giá mới nhất.\n\nBạn có thể nhấn các nút gợi ý bên dưới hoặc hỏi trực tiếp nhé!";
            return response()->json([
                'reply'             => $reply,
                'type'              => 'text',
                'suggested_actions' => [
                    ['text' => '📁 Xem Danh Mục', 'action' => 'categories'],
                    ['text' => '🎮 ĐT Chơi Game', 'action' => 'use_case', 'value' => 'gaming'],
                    ['text' => '📸 ĐT Chụp Ảnh Đẹp', 'action' => 'use_case', 'value' => 'camera'],
                    ['text' => '🔋 ĐT Pin Trâu', 'action' => 'use_case', 'value' => 'battery'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        if (str_contains($lowerMsg, 'iphone') || str_contains($lowerMsg, 'apple')) {
            $ipList = $sampleProducts->filter(fn($p) => stripos($p->name, 'iPhone') !== false || stripos($p->brand, 'Apple') !== false);
            return response()->json([
                'reply'             => "Dạ đây là danh sách các dòng **iPhone Apple chính hãng** đang có sẵn tại PhoneStore:",
                'type'              => 'products',
                'filter_title'      => '🍎 iPhone Chính Hãng',
                'products'          => $this->formatProductsForChat($ipList),
                'suggested_actions' => [
                    ['text' => '📁 Xem danh mục khác', 'action' => 'categories'],
                    ['text' => '🎮 ĐT chơi game', 'action' => 'use_case', 'value' => 'gaming'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        if (str_contains($lowerMsg, 'samsung') || str_contains($lowerMsg, 'galaxy')) {
            $samList = $sampleProducts->filter(fn($p) => stripos($p->name, 'Samsung') !== false || stripos($p->brand, 'Samsung') !== false);
            return response()->json([
                'reply'             => "Dạ danh sách điện thoại **Samsung Galaxy** cao cấp đang có giá cực ưu đãi:",
                'type'              => 'products',
                'filter_title'      => '✨ Samsung Galaxy',
                'products'          => $this->formatProductsForChat($samList),
                'suggested_actions' => [
                    ['text' => '📁 Xem danh mục khác', 'action' => 'categories'],
                    ['text' => '📸 ĐT chụp ảnh đẹp', 'action' => 'use_case', 'value' => 'camera'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        if (str_contains($lowerMsg, 'giá') || str_contains($lowerMsg, 'tiền') || str_contains($lowerMsg, 'bao nhiêu')) {
            return response()->json([
                'reply'             => "Dạ bảng giá một số dòng điện thoại Flagship bán chạy nhất tại PhoneStore:",
                'type'              => 'products',
                'filter_title'      => '🔥 Sản phẩm nổi bật',
                'products'          => $this->formatProductsForChat($sampleProducts->take(6)),
                'suggested_actions' => [
                    ['text' => '📁 Xem theo danh mục', 'action' => 'categories'],
                    ['text' => '🎓 Giá cho sinh viên', 'action' => 'use_case', 'value' => 'budget'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        if (str_contains($lowerMsg, 'ship') || str_contains($lowerMsg, 'giao hàng') || str_contains($lowerMsg, 'vận chuyển')) {
            $reply = "🚚 **Chính sách giao hàng PhoneStore**:\n• Giao hàng nhanh toàn quốc kết nối cùng **GHN Express**.\n• Tự động tính cước chuẩn xác theo từng Phường/Xã.\n• Hỗ trợ nhận hàng kiểm tra rồi thanh toán (COD) hoặc Ví MoMo.";
            return response()->json([
                'reply'             => $reply,
                'type'              => 'text',
                'suggested_actions' => [
                    ['text' => '📁 Xem danh mục sản phẩm', 'action' => 'categories'],
                    ['text' => '🛡️ Chính sách bảo hành', 'action' => 'message', 'value' => 'bảo hành'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        if (str_contains($lowerMsg, 'bảo hành') || str_contains($lowerMsg, 'đổi trả')) {
            $reply = "🛡️ **Chính sách bảo hành PhoneStore**:\n• Bảo hành 12 tháng chính hãng toàn diện máy và phụ kiện.\n• 1 đổi 1 trong vòng 30 ngày nếu phát sinh lỗi nhà sản xuất.\n• Hỗ trợ cài đặt phần mềm và kỹ thuật trọn đời máy.";
            return response()->json([
                'reply'             => $reply,
                'type'              => 'text',
                'suggested_actions' => [
                    ['text' => '📁 Xem danh mục sản phẩm', 'action' => 'categories'],
                    ['text' => '🎮 ĐT Chơi Game', 'action' => 'use_case', 'value' => 'gaming'],
                ],
                'source'            => 'local_bot'
            ]);
        }

        // Câu trả lời mặc định nếu không khớp
        return response()->json([
            'reply'             => "Cảm ơn bạn đã nhắn tin cho PhoneStore! 👋\n\nTôi có thể giúp bạn khám phá sản phẩm theo **Danh mục** hoặc lọc theo **Chức năng** (chơi game, chụp ảnh, pin trâu, sinh viên,...). Bạn hãy chọn nhanh bên dưới hoặc đặt câu hỏi chi tiết nhé!",
            'type'              => 'text',
            'suggested_actions' => [
                ['text' => '📁 Danh mục sản phẩm', 'action' => 'categories'],
                ['text' => '🎮 Điện thoại chơi game', 'action' => 'use_case', 'value' => 'gaming'],
                ['text' => '📸 Điện thoại chụp ảnh', 'action' => 'use_case', 'value' => 'camera'],
                ['text' => '🔋 Điện thoại pin trâu', 'action' => 'use_case', 'value' => 'battery'],
            ],
            'source'            => 'local_bot'
        ]);
    }
}
