<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration (tạo bảng users)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->string('role')->default('patient'); // admin, doctor, patient
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('avatar_path')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Rollback (xóa bảng users)
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
