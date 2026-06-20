<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_jawaban_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_cbt_id')->constrained('hasil_cbt')->cascadeOnDelete();
            $table->foreignId('cbt_soal_id')->constrained('cbt_soal')->cascadeOnDelete();
            $table->text('jawaban')->nullable();
            $table->unsignedTinyInteger('skor_uraian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_jawaban_peserta');
    }
};
