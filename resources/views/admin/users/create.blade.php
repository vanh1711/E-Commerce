@extends('layouts.admin')

@section('title', 'Thêm Người Dùng Mới - Admin PhoneStore')
@section('page_title', 'Thêm Người Dùng')
@section('page_heading', 'Tạo Tài Khoản Người Dùng Mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Top -->
    <div class="flex items-center justify-between bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>➕</span> Đăng Ký Tài Khoản
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Thêm Người Dùng</h1>
        </div>

        <a href="{{ route('admin.users.index') }}" 
           class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition">
            ← Quay lại
        </a>
    </div>

    <!-- Form Create -->
    <div class="bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Họ tên -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Họ Và Tên <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ví dụ: Nguyễn Văn A"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-rose-500 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Địa Chỉ Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="example@domain.com"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-rose-500 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Mật Khẩu <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" required minlength="6"
                       placeholder="Tối thiểu 6 ký tự"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition @error('password') border-rose-500 @enderror">
                @error('password')
                    <p class="text-rose-500 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phân quyền / Vai trò -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Vai Trò Hệ Thống <span class="text-rose-500">*</span>
                </label>
                <select name="role" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition @error('role') border-rose-500 @enderror">
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>👤 Khách hàng (User)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>⚡ Quản trị viên (Admin)</option>
                </select>
                @error('role')
                    <p class="text-rose-500 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Số điện thoại -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Số Điện Thoại
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="0987654321"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
            </div>

            <!-- Địa chỉ -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Địa Chỉ Liên Hệ
                </label>
                <input type="text" name="address" value="{{ old('address') }}"
                       placeholder="Số nhà, tên đường, phường xã..."
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-5 py-3 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Hủy bỏ
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs rounded-2xl shadow-lg shadow-blue-600/30 transition cursor-pointer">
                    ✓ Lưu Người Dùng
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
