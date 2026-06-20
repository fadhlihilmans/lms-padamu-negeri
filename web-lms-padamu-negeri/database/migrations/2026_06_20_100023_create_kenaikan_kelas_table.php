<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('rombel_asal_id')->constrained('rombel')->restrictOnDelete();
            $table->enum('status_keputusan', ['naik', 'tinggal', 'lulus', 'pindah_paket', 'pindah_wilayah']);
            // rombel_tujuan_id: nullable hingga Admin assign di TA baru
            $table->unsignedBigInteger('rombel_tujuan_id')->nullable();
            $table->foreign('rombel_tujuan_id')->references('id')->on('rombel')->nullOnDelete();
            // diputuskan_oleh → users (Wali Kelas)
            $table->unsignedBigInteger('diputuskan_oleh');
            $table->foreign('diputuskan_oleh')->references('id')->on('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
