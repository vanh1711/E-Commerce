<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recipient_name');
            $table->string('phone', 20);
            $table->string('address_line'); // Số nhà, tên đường
            $table->integer('to_province_id')->nullable();
            $table->integer('to_district_id')->nullable();
            $table->string('to_ward_code', 20)->nullable();
            $table->string('province_name', 100)->nullable();
            $table->string('district_name', 100)->nullable();
            $table->string('ward_name', 100)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
