@extends('layouts.admin')

@section('title', 'Hồ Sơ Người Dùng - ' . $user->name . ' - Admin PhoneStore')
@section('page_title', 'Hồ Sơ Người Dùng')
@section('page_heading', 'Thông Tin Chi Tiết Thành Viên')

@section('content')
<div class="space-y-6">

    <!-- Header Top -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-3xl bg-gradient-to-tr {{ $user->role === 'admin' ? 'from-purple-600 to-indigo-600' : 'from-blue-600 to-cyan-500' }} text-white flex items-center justify-center font-black text-xl uppercase shadow-md flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $user->name }}</h1>
                    @if($user->role === 'admin' || $user->is_admin)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800">
                            ⚡ Quản trị viên
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800">
                            👤 Khách hàng
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Thành viên từ: {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" 
               class="px-4 py-2 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-2xl transition">
                ✏️ Chỉnh Sửa
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition">
                ← Danh Sách
            </a>
        </div>
    </div>

    <!-- Thông tin cá nhân & Thống kê -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Card Profile Detail -->
        <div class="lg:col-span-1 bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
            <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Thông Tin Tài Khoản</h3>

            <div class="space-y-3 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold">Mã Người Dùng</span>
                    <p class="font-mono font-black text-slate-900 dark:text-white mt-0.5">#{{ $user->id }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold">Địa Chỉ Email</span>
                    <p class="font-bold text-slate-900 dark:text-white mt-0.5">{{ $user->email }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold">Số Điện Thoại</span>
                    <p class="font-bold text-slate-900 dark:text-white mt-0.5 font-mono">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 dark:text-slate-500 block text-[10px] uppercase font-bold">Địa Chỉ Giao Hàng Mặc Định</span>
                    <p class="font-medium text-slate-800 dark:text-slate-200 mt-0.5">{{ $user->address ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>

        <!-- Lịch sử đơn hàng gần nhất -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Đơn Hàng Gần Đây Của Khách</h3>
                <span class="text-xs text-slate-400 font-bold">{{ $user->orders->count() }} đơn</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="pb-3">Mã Đơn</th>
                            <th class="pb-3">Ngày Tạo</th>
                            <th class="pb-3">Tổng Tiền</th>
                            <th class="pb-3">Thanh Toán</th>
                            <th class="pb-3">Vận Chuyển</th>
                            <th class="pb-3 text-right">Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($user->orders as $order)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 font-mono font-bold text-blue-600 dark:text-blue-400">
                                #{{ $order->order_code }}
                            </td>
                            <td class="py-3 text-slate-500">
                                {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}
                            </td>
                            <td class="py-3 font-black text-slate-900 dark:text-white">
                                {{ number_format($order->total_amount) }} đ
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thu' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $order->shipping_status_text ?? $order->shipping_status }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                    Xem →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Người dùng này chưa có đơn hàng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
