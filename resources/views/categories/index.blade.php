@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục Sản Phẩm')
@section('page_title', 'Danh Mục')
@section('page_heading', 'Quản Lý Danh Mục')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>📁</span> Danh Mục Hệ Thống
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Danh Sách Danh Mục Hàng Hóa</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Phân loại các thương hiệu điện thoại và phụ kiện trong hệ thống PhoneStore.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5 cursor-pointer">
                <span>+</span> Thêm Danh Mục Mới
            </a>
        </div>
    </div>

    <!-- Table Danh Mục -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8 transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="pb-4">#ID</th>
                        <th class="pb-4">Tên danh mục</th>
                        <th class="pb-4">Đường dẫn (Slug)</th>
                        <th class="pb-4">Mô tả</th>
                        <th class="pb-4 text-center">Số lượng mẫu</th>
                        <th class="pb-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-4 font-bold text-slate-400">#{{ $category->id }}</td>
                            <td class="py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">📁</span>
                                    <span class="font-black text-slate-900 dark:text-white text-xs">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 font-mono font-bold text-blue-600 dark:text-blue-400">{{ $category->slug }}</td>
                            <td class="py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">{{ $category->description ?? '---' }}</td>
                            <td class="py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-black text-[11px]">
                                    {{ $category->products_count ?? $category->products()->count() }} mẫu
                                </span>
                            </td>
                            <td class="py-4 text-right space-x-2">
                                <a href="{{ route('categories.edit', $category->id) }}" class="px-3.5 py-1.5 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-xs font-bold rounded-xl transition inline-flex items-center gap-1">
                                    <span>✏️</span> Sửa
                                </a>

                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Tất cả sản phẩm thuộc danh mục sẽ bị ảnh hưởng.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3.5 py-1.5 bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-xs font-bold rounded-xl transition cursor-pointer inline-flex items-center gap-1">
                                        <span>🗑️</span> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Chưa có danh mục nào trong hệ thống.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection