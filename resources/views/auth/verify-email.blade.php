@extends('layouts.app')

@section('title', 'Xác Thực Mã OTP - PhoneStore Security')

@section('content')
<div class="max-w-md mx-auto py-12 px-4 sm:px-0">
    <div class="bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl text-center space-y-6 transition-colors duration-300">
        
        <!-- Icon Bảo Mật PhoneStore -->
        <div class="w-16 h-16 bg-gradient-to-tr from-blue-600/10 via-indigo-600/10 to-cyan-500/10 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-3xl flex items-center justify-center text-3xl mx-auto shadow-sm border border-blue-500/20">
            🛡️
        </div>

        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Xác Thực Mã OTP</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Mã xác thực gồm 6 chữ số đã được gửi qua dịch vụ API bảo mật tới địa chỉ email:
            </p>
            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 mt-1 font-mono">
                {{ Auth::user()?->email }}
            </p>
        </div>

        <!-- Thông báo Thành công / Lỗi -->
        @if (session('success'))
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-2xl flex items-center gap-2 text-left">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-400 text-xs font-bold rounded-2xl flex items-center gap-2 text-left">
                <span>⚠️</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Form Nhập Mã OTP 6 Chữ Số -->
        <form method="POST" action="{{ route('verification.verify-otp') }}" class="space-y-4">
            @csrf
            <div>
                <label for="otpInput" class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-2">
                    Nhập mã OTP 6 số
                </label>
                <input type="text" id="otpInput" name="otp" maxlength="6" autofocus required
                       placeholder="••••••"
                       class="w-full py-3.5 px-4 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-center font-mono text-2xl font-black tracking-[0.4em] text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                       autocomplete="one-time-code">
            </div>

            <button type="submit" 
                    class="w-full py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-blue-500/25 transition transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                Xác Nhận Kích Hoạt Tài Khoản
            </button>
        </form>

        <!-- Hộp Trợ Giúp Thử Nghiệm Nhanh (Demo Mode) -->
        @if(session('last_generated_otp'))
            <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/60 rounded-2xl flex items-center justify-between text-xs">
                <div class="text-left">
                    <span class="text-slate-500 dark:text-slate-400 text-[10px] uppercase font-bold block">Mã OTP thử nghiệm (Sandbox):</span>
                    <span class="font-mono text-sm font-black text-blue-600 dark:text-blue-400 tracking-widest">{{ session('last_generated_otp') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('otpInput').value='{{ session('last_generated_otp') }}'" 
                        class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] rounded-xl transition cursor-pointer">
                    Nhập Nhanh
                </button>
            </div>
        @endif

        <!-- Gửi Lại Mã OTP -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
            <form method="POST" action="{{ route('verification.resend-otp') }}">
                @csrf
                <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline font-bold text-xs cursor-pointer">
                    🔄 Gửi Lại Mã Mới
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-500 text-xs font-semibold cursor-pointer">
                    Đăng xuất
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
