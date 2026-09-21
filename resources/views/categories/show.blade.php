@extends('layouts.admin')

@section('title', 'Danh Mục: ' . $category->name . ' - PhoneStore Admin')
@section('page_title', 'Danh Mục')
@section('page_heading', $category->name)

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>📁</span> Chi Tiết Danh Mục
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $category->name }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $category->description ?? 'Không có mô tả' }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-blue-500/25 transition">
                + Thêm Sản Phẩm Thuộc Nhóm
            </a>
            <a href="{{ route('categories.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-2xl transition">
                ← Danh Sách
            </a>
        </div>
    </div>

    <!-- Danh sách sản phẩm thuộc danh mục -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8 transition-colors duration-300">
        <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4">Sản Phẩm Trong Danh Mục ({{ $products->count() }} mẫu)</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($products as $p)
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-xl p-1 border border-slate-200 dark:border-slate-600 flex items-center justify-center flex-shrink-0">
                            <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=100&q=80' }}" class="max-h-full object-contain" alt="">
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $p->name }}</p>
                            <span class="text-xs font-black text-rose-600 dark:text-rose-400">{{ number_format($p->price) }} đ</span>
                        </div>
                    </div>
                    <a href="{{ route('products.edit', $p->id) }}" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-xl hover:bg-amber-100 transition flex-shrink-0">
                        Sửa
                    </a>
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-slate-400 text-xs">
                    Chưa có sản phẩm nào thuộc danh mục này.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection