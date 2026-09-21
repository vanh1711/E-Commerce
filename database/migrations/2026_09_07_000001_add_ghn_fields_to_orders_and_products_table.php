<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'to_province_id')) {
                $table->integer('to_province_id')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('orders', 'to_district_id')) {
                $table->integer('to_district_id')->nullable()->after('to_province_id');
            }
            if (!Schema::hasColumn('orders', 'to_ward_code')) {
                $table->string('to_ward_code')->nullable()->after('to_district_id');
            }
            if (!Schema::hasColumn('orders', 'province_name')) {
                $table->string('province_name')->nullable()->after('to_ward_code');
            }
            if (!Schema::hasColumn('orders', 'district_name')) {
                $table->string('district_name')->nullable()->after('province_name');
            }
            if (!Schema::hasColumn('orders', 'ward_name')) {
                $table->string('ward_name')->nullable()->after('district_name');
            }
            if (!Schema::hasColumn('orders', 'ghn_order_code')) {
                $table->string('ghn_order_code')->nullable()->index()->after('shipping_status');
            }
            if (!Schema::hasColumn('orders', 'ghn_total_fee')) {
                $table->integer('ghn_total_fee')->default(0)->after('shipping_fee');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'weight')) {
                $table->integer('weight')->default(200)->after('price'); // Trọng lượng gram
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'to_province_id',
                'to_district_id',
                'to_ward_code',
                'province_name',
                'district_name',
                'ward_name',
                'ghn_order_code',
                'ghn_total_fee',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight']);
        });
    }
};
