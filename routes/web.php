<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatbotController;
use App\Models\Category;
use App\Models\Product;

// 1. PUBLIC ROUTES (Trang chủ phong cách Samsung & Xem sản phẩm)
Route::get('/', function (Request $request) {
    $categories = Category::withCount('products')->get();
    
    $query = Product::with('category');

    if ($request->filled('search')) {
        $searchTerm = trim($request->get('search'));
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('model', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
              ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                  $catQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
              });
        });
    }


    $products         = $query->latest()->get();
    $featuredProducts = Product::with('category')->latest()->take(4)->get();

    return view('welcome', compact('categories', 'products', 'featuredProducts'));
})->name('home');


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/compare', [ProductController::class, 'compare'])->name('products.compare');
Route::get('/api/products/search', [ProductController::class, 'searchApi'])->name('api.products.search');
Route::get('/api/products/compare', [ProductController::class, 'compareApi'])->name('api.products.compare');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/chat', [ChatbotController::class, 'chat'])->name('chat');



// 2. GUEST ROUTES (Chỉ dành cho người CHƯA đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. AUTH ROUTES (Bắt buộc ĐÃ đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Hiển thị thông báo xác thực email
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Xử lý link xác nhận (từ email)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home'); // Redirect về trang chủ
    })->middleware(['signed'])->name('verification.verify');

    // Gửi lại email xác nhận
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // ===== ROUTES GIỎ HÀNG =====
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // ===== ROUTES THANH TOÁN & ĐẶT SHIP (CHECKOUT) =====
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/search-location', [\App\Http\Controllers\CheckoutController::class, 'geocodeSearch'])->name('checkout.search-location');
    Route::post('/checkout/calculate-shipping', [\App\Http\Controllers\CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{id}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

    // ===== ROUTES ĐỊA LÝ & TÍNH CƯỚC GHN EXPRESS =====
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/provinces', [\App\Http\Controllers\User\GHNController::class, 'getProvinces'])->name('provinces');
        Route::get('/districts/{provinceId}', [\App\Http\Controllers\User\GHNController::class, 'getDistricts'])->name('districts');
        Route::get('/wards/{districtId}', [\App\Http\Controllers\User\GHNController::class, 'getWards'])->name('wards');
        Route::post('/calculate-fee', [\App\Http\Controllers\User\GHNController::class, 'getShippingFee'])->name('fee');
    });


    // ===== ROUTES ĐƠN HÀNG & THANH TOÁN (COD, MOMO) =====
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/user/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('user.orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/sync-ghn', [\App\Http\Controllers\OrderController::class, 'syncGHN'])->name('orders.sync-ghn');
    Route::post('/payment/process', [\App\Http\Controllers\OrderController::class, 'processPayment'])->name('payment.process');

    // Luồng thanh toán MoMo Sandbox
    Route::get('/orders/{order}/start-momo', [\App\Http\Controllers\User\MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders/{order}/pay/momo', [\App\Http\Controllers\User\MomoController::class, 'payAgain'])->name('orders.momo.pay');
    Route::get('/user/orders/{order}/start-momo', [\App\Http\Controllers\User\MomoController::class, 'start'])->name('user.orders.momo.start');
    Route::get('/user/orders/{order}/pay/momo', [\App\Http\Controllers\User\MomoController::class, 'payAgain'])->name('user.orders.momo.pay');

    // ===== ROUTES NHẮN TIN LIVECHAT KHÁCH HÀNG (LAB 07) =====
    Route::post('/user/chat/send', [\App\Http\Controllers\User\ChatController::class, 'send'])->name('user.chat.send');
    Route::get('/user/chat/messages', [\App\Http\Controllers\User\ChatController::class, 'getMessages'])->name('user.chat.messages');
    Route::post('/chat/send', [\App\Http\Controllers\User\ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [\App\Http\Controllers\User\ChatController::class, 'getMessages'])->name('chat.messages');
});

// ===== THIRD-PARTY WEBHOOKS & CALLBACKS (GHN EXPRESS, MOMO IPN) =====
Route::post('/payment/momo/ipn', [\App\Http\Controllers\User\MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [\App\Http\Controllers\User\MomoController::class, 'callback'])->name('user.payment.momo.callback');
Route::get('/payment/callback', [\App\Http\Controllers\User\MomoController::class, 'callback'])->name('payment.momo.callback');

// Webhook nhận trạng thái tự động từ GHN Express
Route::post('/api/ghn/webhook', [\App\Http\Controllers\Api\GHNWebhookController::class, 'handle'])->name('api.ghn.webhook');
Route::post('/ghn/webhook', [\App\Http\Controllers\Api\GHNWebhookController::class, 'handle'])->name('ghn.webhook');

// 4. ADMIN ROUTES (Quản trị viên & Toàn quyền CRUD)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // CRUD Sản phẩm & Danh mục (categories.*, products.*)
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('products', [ProductController::class, 'index']);

    // Quản lý người dùng (Lab 08)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'show'    => 'admin.users.show',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');

    // Báo cáo & Thống kê doanh thu (Lab 08)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/charts', [\App\Http\Controllers\Admin\ReportController::class, 'charts'])->name('admin.reports.charts');

    // ===== BÁO CÁO TÀI CHÍNH & GIAO DỊCH (LAB 09 - FINANCE) =====
    Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/finance/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'transactions'])->name('admin.finance.transactions');
    Route::patch('/finance/{order}/status', [\App\Http\Controllers\Admin\FinanceController::class, 'updateStatus'])->name('admin.finance.update-status');

    // Quản lý đơn hàng & Ship hàng
    Route::get('/orders', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/bulk-update', [\App\Http\Controllers\Admin\AdminOrderController::class, 'bulkUpdate'])->name('admin.orders.bulk-update');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{id}/shipping', [\App\Http\Controllers\Admin\AdminOrderController::class, 'updateShipping'])->name('admin.orders.update-shipping');
    Route::post('/orders/{id}/sync-ghn', [\App\Http\Controllers\Admin\AdminOrderController::class, 'syncGHN'])->name('admin.orders.sync-ghn');
    Route::post('/orders/{id}/create-ghn', [\App\Http\Controllers\Admin\AdminOrderController::class, 'createGHN'])->name('admin.orders.create-ghn');
    Route::get('/orders/{id}/print-ghn', [\App\Http\Controllers\Admin\AdminOrderController::class, 'printGHN'])->name('admin.orders.print-ghn');

    // ===== ROUTES NHẮN TIN LIVECHAT QUẢN TRỊ VIÊN (LAB 07) =====
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/chat/users', [\App\Http\Controllers\Admin\ChatController::class, 'getUsers'])->name('admin.chat.users');
    Route::get('/chat/messages/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/chat/send', [\App\Http\Controllers\Admin\ChatController::class, 'send'])->name('admin.chat.send');
});