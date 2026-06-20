<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_nilai_komponen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_nilai_mapel_id')->constrained('rapor_nilai_mapel')->cascadeOnDelete();
            $table->string('nama_komponen', 50);
            // nilai_referensi: dihitung ulang saat form dibuka, tidak di-cache permanen
            $table->unsignedTinyInteger('nilai_referensi')->nullable();
            $table->unsignedTinyInteger('nilai_akhir');
            $table->enum('grade', ['A', 'B', 'C', 'D']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_nilai_komponen');
    }
};
