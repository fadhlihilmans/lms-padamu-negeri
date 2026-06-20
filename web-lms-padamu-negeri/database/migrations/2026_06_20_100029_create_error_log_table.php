<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_log', function (Blueprint $table) {
            $table->id();
            $table->string('aksi', 255);
            $table->text('pesan_error');
            $table->json('context')->nullable();
            // user_id nullable: error bisa terjadi saat belum login
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('url', 500)->nullable();
            // Hanya created_at, tidak ada updated_at — log tidak pernah diubah
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_log');
    }
};
