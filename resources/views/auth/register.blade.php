@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-10">
    
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-gray-100 dark:border-slate-800 shadow-[0_10px_35px_rgba(0,0,0,0.04)] dark:shadow-2xl transition-colors duration-300">
        
        <!-- Logo & Heading -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl mx-auto shadow-md mb-4">
                ✨
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Đăng Ký Thành Viên</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Trở thành thành viên VanhPhone nhận ngập tràn ưu đãi</p>
        </div>

        <!-- Báo Lỗi -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Họ tên -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-1.5">Họ và tên</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nguyễn Văn A"
                       class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-1.5">Địa chỉ Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com"
                       class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Mật khẩu -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-1.5">Mật khẩu</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Xác nhận Mật khẩu -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-1.5">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                       class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <!-- Nút Bấm -->
            <button type="submit" class="w-full mt-2 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-full shadow-lg shadow-blue-500/25 transition duration-300 cursor-pointer">
                Đăng Ký Tài Khoản
            </button>
        </form>

        <!-- Chuyển sang Đăng nhập -->
        <div class="text-center mt-6 pt-5 border-t border-gray-100 dark:border-slate-800">
            <span class="text-xs text-gray-500 dark:text-slate-400">Đã có tài khoản?</span>
            <a href="{{ route('login') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline ml-1">Đăng nhập ngay</a>
        </div>

    </div>

</div>
@endsection