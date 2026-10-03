@extends('layouts.admin')

@section('title', 'Quản Lý Người Dùng & Khóa/Mở Tài Khoản - Admin PhoneStore')
@section('page_title', 'Quản Lý Tài Khoản')
@section('page_heading', 'Quản Lý Tài Khoản Khách Hàng & Thành Viên')

@section('content')
<div class="space-y-6">

    <!-- Header Top -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>👥</span> Hệ Thống Tài Khoản & Phân Quyền
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Quản Lý Tài Khoản Người Dùng</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Quản lý tài khoản khách hàng, phân quyền vai trò và thực hiện khóa / mở khóa tài khoản thành viên.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}" 
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <span>➕</span>
                <span>Thêm Người Dùng Mới</span>
            </a>
        </div>
    </div>

    <!-- Thống kê nhanh người dùng & trạng thái -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tổng người dùng -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Tổng Tài Khoản</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                👥
            </div>
        </div>

        <!-- Khách hàng -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Khách Hàng</p>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($stats['customers'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                👤
            </div>
        </div>

        <!-- Quản trị viên -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Quản Trị Viên</p>
                <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['admins'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
                ⚡
            </div>
        </div>

        <!-- Tài khoản bị khóa -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Tài Khoản Bị Khóa</p>
                <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['locked'] ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl">
                🔒
            </div>
        </div>
    </div>

    <!-- Bộ Lọc Tìm Kiếm & Trạng Thái -->
    <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Tìm kiếm -->
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm theo họ tên, email hoặc số điện thoại..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>

            <!-- Lọc vai trò -->
            <div class="sm:col-span-3">
                <select name="role" onchange="this.form.submit()" 
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer">
                    <option value="">Tất cả vai trò</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Khách hàng (User)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                </select>
            </div>

            <!-- Lọc trạng thái Khóa / Mở -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Đang hoạt động</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>🔒 Bị khóa</option>
                </select>
            </div>

            <!-- Nút Lọc / Đặt lại -->
            <div class="sm:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-2xl transition shadow-md cursor-pointer">
                    Lọc
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" title="Xóa bộ lọc" 
                       class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl text-xs transition">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bảng Danh Sách Người Dùng -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6 transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="pb-4">ID</th>
                        <th class="pb-4">Người Dùng</th>
                        <th class="pb-4">Email</th>
                        <th class="pb-4">Số Điện Thoại</th>
                        <th class="pb-4">Vai Trò</th>
                        <th class="pb-4 text-center">Trạng Thái</th>
                        <th class="pb-4">Đơn Hàng</th>
                        <th class="pb-4">Ngày Tham Gia</th>
                        <th class="pb-4 text-right">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition {{ $user->isLocked() ? 'bg-rose-50/20 dark:bg-rose-950/10' : '' }}">
                        <td class="py-4 font-mono font-black text-slate-400">
                            #{{ $user->id }}
                        </td>

                        <td class="py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr {{ $user->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-blue-600 to-cyan-500' }} text-white flex items-center justify-center font-black text-xs uppercase shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        {{ $user->name }}
                                        @if($user->isLocked())
                                            <span class="text-rose-500 text-xs" title="Tài khoản này đang bị khóa">🔒</span>
                                        @endif
                                    </p>
                                    @if(Auth::id() === $user->id)
                                        <span class="text-[9px] font-black text-blue-500 uppercase">(Tài khoản của bạn)</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="py-4 font-medium text-slate-600 dark:text-slate-300">
                            {{ $user->email }}
                        </td>

                        <td class="py-4 text-slate-600 dark:text-slate-400 font-mono">
                            {{ $user->phone ?? '—' }}
                        </td>

                        <td class="py-4">
                            @if($user->role === 'admin' || $user->is_admin)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black border bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800">
                                    ⚡ Quản trị viên
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800">
                                    👤 Khách hàng
                                </span>
                            @endif
                        </td>

                        <!-- Cột Trạng thái Khóa / Mở -->
                        <td class="py-4 text-center">
                            @if($user->isLocked())
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black border bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800 shadow-sm animate-pulse">
                                    <span>🔒</span> Bị khóa
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hoạt động
                                </span>
                            @endif
                        </td>

                        <td class="py-4 font-bold text-slate-700 dark:text-slate-300">
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono text-[11px]">
                                {{ $user->orders_count ?? 0 }} đơn
                            </span>
                        </td>

                        <td class="py-4 text-[11px] text-slate-400 dark:text-slate-500">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}
                        </td>

                        <td class="py-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-bold text-[11px] transition">
                                    Xem
                                </a>

                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="px-2.5 py-1 rounded-xl bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-600 dark:text-blue-400 font-bold text-[11px] transition">
                                    Sửa
                                </a>

                                <!-- Nút Khóa / Mở khóa tài khoản -->
                                @if(Auth::id() !== $user->id && !in_array($user->email, ['admin@gmail.com', 'admin@example.com']))
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->isLocked())
                                            <button type="submit" 
                                                    onclick="return confirm('Bạn có muốn MỞ KHÓA tài khoản {{ $user->name }} không? Tài khoản sẽ có thể đăng nhập bình thường.');"
                                                    class="px-2.5 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer"
                                                    title="Mở khóa tài khoản này">
                                                <span>🔓</span> Mở khóa
                                            </button>
                                        @else
                                            <button type="submit" 
                                                    onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản {{ $user->name }}? Người dùng này sẽ bị chặn đăng nhập.');"
                                                    class="px-2.5 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-300 font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer"
                                                    title="Khóa tài khoản này">
                                                <span>🔒</span> Khóa
                                            </button>
                                        @endif
                                    </form>
                                @endif

                                <!-- Nút Xóa -->
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ $user->name }} không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-bold text-[11px] transition cursor-pointer">
                                            Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400 dark:text-slate-500">Không tìm thấy người dùng nào phù hợp với bộ lọc.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
