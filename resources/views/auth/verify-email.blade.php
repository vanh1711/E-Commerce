@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl text-center space-y-5 transition-colors duration-300">
        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-inner">
            ✉️
        </div>

        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Xác Thực Email</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Vui lòng kiểm tra hộp thư đến email của bạn và bấm vào liên kết xác nhận để kích hoạt tài khoản đầy đủ.
            </p>
        </div>

        @if (session('message'))
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-xl">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                Gửi Lại Email Xác Thực
            </button>
        </form>

        <div>
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                Về trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
