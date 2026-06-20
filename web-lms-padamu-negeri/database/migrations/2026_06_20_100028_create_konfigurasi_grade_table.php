<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_grade', function (Blueprint $table) {
            $table->id();
            $table->enum('grade', ['A', 'B', 'C', 'D'])->unique();
            $table->unsignedTinyInteger('nilai_min');
            $table->unsignedTinyInteger('nilai_max');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_grade');
    }
};
