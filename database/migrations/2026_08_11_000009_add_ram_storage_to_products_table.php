<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'ram')) {
                $table->string('ram')->nullable()->after('badge');
            }
            if (!Schema::hasColumn('products', 'storage')) {
                $table->string('storage')->nullable()->after('ram');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'storage')) {
                $table->dropColumn('storage');
            }
            if (Schema::hasColumn('products', 'ram')) {
                $table->dropColumn('ram');
            }
        });
    }
};
