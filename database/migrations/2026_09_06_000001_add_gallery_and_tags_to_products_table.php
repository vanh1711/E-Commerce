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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'gallery')) {
                $table->json('gallery')->nullable()->after('image');
            }
            if (!Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('gallery');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('products', 'gallery')) {
                $table->dropColumn('gallery');
            }
        });
    }
};
