<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_id')->constrained('cbt')->cascadeOnDelete();
            $table->enum('tipe_soal', ['pilihan_ganda', 'uraian']);
            $table->text('pertanyaan');
            $table->json('pilihan_jawaban')->nullable();
            $table->string('kunci_jawaban', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_soal');
    }
};
