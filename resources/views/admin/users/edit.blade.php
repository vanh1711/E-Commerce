@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Người Dùng - Admin PhoneStore')
@section('page_title', 'Chỉnh Sửa Người Dùng')
@section('page_heading', 'Cập Nhật Tài Khoản Người Dùng')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Top -->
    <div class="flex items-center justify-between bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-base uppercase shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Chỉnh Sửa: {{ $user->name }}</h1>
                <p class="text-xs text-slate-400 dark:text-slate-500">ID: #{{ $user->id }} &bull; Email: {{ $user->email }}</p>
            </div>
        </div>

        <a href="{{ route('admin.users.index') }}" 
           class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition">
            ← Quay lại
        </a>
    </div>

    <!-- Form Edit -->
    <div class="bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Họ tên -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Họ Và Tên <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
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
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-rose-500 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mật khẩu mới (Tùy chọn) -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Mật Khẩu Mới <span class="text-slate-400 font-normal lowercase">(để trống nếu không đổi)</span>
                </label>
                <input type="password" name="password" minlength="6"
                       placeholder="Nhập mật khẩu mới nếu muốn thay đổi..."
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
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>👤 Khách hàng (User)</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' || $user->is_admin ? 'selected' : '' }}>⚡ Quản trị viên (Admin)</option>
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
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-blue-500 transition">
            </div>

            <!-- Địa chỉ -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Địa Chỉ Liên Hệ
                </label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}"
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
                    ✓ Cập Nhật Thông Tin
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
