<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login'); // Trả về file resources/views/auth/login.blade.php
    }

    // 2. Xử lý đăng nhập
   // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);
        // Thử xác thực tài khoản
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Kiểm tra trạng thái khóa tài khoản
            if (Auth::user()->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa bởi Quản trị viên. Vui lòng liên hệ hỗ trợ.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            // 1. NẾU LÀ ADMIN: VÀO THẲNG DASHBOARD QUẢN TRỊ
            if (Auth::user()->is_admin == 1 || Auth::user()->role === 'admin' || Auth::user()->email === 'admin@gmail.com' || Auth::user()->email === 'admin@example.com') {
                return redirect()->route('dashboard')->with('success', 'Chào mừng Admin quay trở lại!');
            }
            // 2. NẾU LÀ KHÁCH HÀNG: VỀ TRANG CHỦ MUA SẮM
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }
        // Đăng nhập thất bại
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    // 3. Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register'); // Trả về file resources/views/auth/register.blade.php
    }

    // 4. Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // Mặc định role là user
        ]);

        Auth::login($user);

        // Gửi email xác thực
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('success', 'Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    // 5. Xử lý đăng xuất
     public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Đăng xuất xong đưa về thẳng Trang Chủ
        return redirect()->route('home')->with('success', 'Đã đăng xuất thành công!');
    }
}