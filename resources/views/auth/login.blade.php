@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-10">
    
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-gray-100 dark:border-slate-800 shadow-[0_10px_35px_rgba(0,0,0,0.04)] dark:shadow-2xl transition-colors duration-300">
        
        <!-- Logo & Heading -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-black dark:bg-slate-800 text-white rounded-2xl flex items-center justify-center text-2xl mx-auto shadow-md border border-transparent dark:border-slate-700 mb-4">
                📱
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Đăng Nhập</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Chào mừng bạn quay trở lại với PhoneStore</p>
        </div>

        <!-- Báo Lỗi -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">Địa chỉ Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com"
                       class="w-full px-4 py-3.5 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Mật khẩu -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-2">Mật khẩu</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3.5 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Ghi nhớ -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600 dark:text-slate-400 font-medium">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-600 dark:bg-slate-800">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
            </div>

            <!-- Nút Bấm -->
            <button type="submit" class="w-full py-3.5 bg-black dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-lg shadow-black/10 dark:shadow-blue-600/30 transition duration-300 cursor-pointer">
                Đăng Nhập Ngay
            </button>
        </form>

        <!-- Chuyển sang Đăng ký -->
        <div class="text-center mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
            <span class="text-xs text-gray-500 dark:text-slate-400">Chưa có tài khoản?</span>
            <a href="{{ route('register') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline ml-1">Đăng ký thành viên</a>
        </div>

    </div>

</div>
@endsection