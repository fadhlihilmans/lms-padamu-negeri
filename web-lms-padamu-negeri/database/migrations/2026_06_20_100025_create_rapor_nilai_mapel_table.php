<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_nilai_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapel')->restrictOnDelete();
            // diinput_oleh → users (Guru Mapel)
            $table->unsignedBigInteger('diinput_oleh');
            $table->foreign('diinput_oleh')->references('id')->on('users')->restrictOnDelete();
            $table->text('catatan_mapel')->nullable();
            $table->timestamps();

            $table->unique(['rapor_id', 'mapel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_nilai_mapel');
    }
};
