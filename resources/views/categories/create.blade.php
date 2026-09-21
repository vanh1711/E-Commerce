@extends('layouts.admin')

@section('title', 'Thêm Danh Mục Mới')
@section('page_title', 'Danh Mục')
@section('page_heading', 'Tạo Danh Mục Mới')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Thêm Danh Mục Mới</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tạo nhóm thương hiệu mới cho hệ sinh thái sản phẩm PhoneStore.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            ← Quay Lại
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 rounded-2xl text-xs space-y-1">
            <p class="font-bold">⚠️ Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Tên danh mục <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ví dụ: iPhone, Samsung Galaxy, Xiaomi Flagship..."
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Mô tả ngắn
                </label>
                <textarea name="description" rows="4" placeholder="Nhập mô tả chi tiết cho danh mục sản phẩm này..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('categories.index') }}" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition">Hủy</a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/30 transition cursor-pointer">
                    Lưu Danh Mục
                </button>
            </div>
        </form>
    </div>

</div>
@endsection