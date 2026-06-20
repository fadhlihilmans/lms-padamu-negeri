<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('periode_ajaran_id')->constrained('periode_ajaran')->restrictOnDelete();
            $table->foreignId('rombel_id')->constrained('rombel')->restrictOnDelete();
            $table->text('catatan_wali_kelas')->nullable();
            $table->enum('status', ['draft', 'terbit'])->default('draft');
            // diterbitkan_oleh → users (Wali Kelas); nullable hingga diterbitkan
            $table->unsignedBigInteger('diterbitkan_oleh')->nullable();
            $table->foreign('diterbitkan_oleh')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['peserta_didik_id', 'periode_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor');
    }
};
