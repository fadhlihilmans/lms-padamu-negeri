<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_cbt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_id')->constrained('cbt')->cascadeOnDelete();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_submit')->nullable();
            $table->unsignedTinyInteger('nilai_pg')->nullable();
            $table->unsignedTinyInteger('nilai_uraian')->nullable();
            $table->unsignedTinyInteger('nilai_akhir')->nullable();
            $table->enum('status_penilaian', ['otomatis', 'menunggu_koreksi', 'selesai_dinilai']);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['cbt_id', 'peserta_didik_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_cbt');
    }
};
