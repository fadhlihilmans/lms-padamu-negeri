<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 9);
            $table->enum('semester', ['ganjil', 'genap']);
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();

            $table->unique(['tahun_ajaran', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_ajaran');
    }
};
