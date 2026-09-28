<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Thống kê số lượng tổng quan
        $categoryCount    = Category::count();
        $productCount     = Product::count();
        $userCount        = User::count();
        $variantStock     = ProductVariant::sum('stock') ?? 0;
        $totalStock       = $variantStock > 0 ? $variantStock : (Product::sum('stock') ?? 0);
        $variantValue     = ProductVariant::selectRaw('SUM(price * stock) as total')->value('total') ?? 0;
        $totalValue       = $variantValue > 0 ? $variantValue : (Product::selectRaw('SUM(price * stock) as total')->value('total') ?? 0);

        // 2. Thống kê đơn hàng & Doanh thu
        $totalOrders      = Order::count();
        $pendingOrders    = Order::where('shipping_status', 'pending')->count();
        $shippingOrders   = Order::where('shipping_status', 'shipping')->count();
        $completedOrders  = Order::where('shipping_status', 'delivered')->count();
        $cancelledOrders  = Order::where('shipping_status', 'cancelled')->count();

        // Doanh thu thực tế (Từ các đơn đã thanh toán hoặc đã giao thành công)
        $totalRevenue     = Order::where(function ($q) {
                                $q->where('payment_status', 'paid')
                                  ->orWhere('shipping_status', 'delivered');
                            })->sum('total_amount') ?? 0;

        // 3. Sản phẩm cảnh báo tồn kho thấp (<= 5 máy)
        $lowStockProducts = Product::with(['category', 'variants'])
                                ->where('stock', '<=', 5)
                                ->orWhereHas('variants', function($q) {
                                    $q->where('stock', '<=', 5);
                                })
                                ->take(5)
                                ->get();

        // 4. Danh sách mới nhất
        $latestOrders     = Order::with('user', 'items.product')
                                ->latest()
                                ->take(6)
                                ->get();
        $latestCategories = Category::withCount('products')
                                ->latest()
                                ->take(6)
                                ->get();
        $latestProducts   = Product::with('category')
                                ->latest()
                                ->take(5)
                                ->get();

        return view('admin.dashboard', compact(
            'categoryCount', 
            'productCount', 
            'userCount', 
            'totalStock',
            'totalValue',
            'totalOrders',
            'pendingOrders',
            'shippingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'lowStockProducts',
            'latestOrders',
            'latestCategories', 
            'latestProducts'
        ));
    }
}