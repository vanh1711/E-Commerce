<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra đăng nhập và is_admin == 1 (hoặc role == 'admin')
        if (Auth::check() && (Auth::user()->is_admin == 1 || Auth::user()->role === 'admin')) {
            return $next($request);
        }
        // Nếu không phải Admin, chuyển hướng về trang chủ 'home' kèm thông báo lỗi
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang quản trị!');
    }
}
