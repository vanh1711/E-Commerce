<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku', 100)->nullable();
            $table->string('version_name', 100)->nullable(); // Tiêu chuẩn, Pro, Pro Max
            $table->string('color', 100)->nullable();         // Titan Sa Mạc, Đen, Trắng
            $table->string('color_code', 20)->nullable();     // #D1C7B7
            $table->string('storage', 50)->nullable();         // 128GB, 256GB, 512GB, 1TB
            $table->string('ram', 50)->nullable();             // 8GB, 12GB, 16GB
            $table->decimal('price', 15, 2);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->integer('stock')->default(0);              // Tồn kho riêng từng bản
            $table->integer('weight')->default(200);           // Cân nặng (gram)
            $table->string('image')->nullable();               // Ảnh riêng phiên bản
            $table->timestamps();
        });

        // Thêm cột variant_id vào order_items để liên kết đơn hàng với biến thể cụ thể
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variant_id');
        });

        Schema::dropIfExists('product_variants');
    }
};
