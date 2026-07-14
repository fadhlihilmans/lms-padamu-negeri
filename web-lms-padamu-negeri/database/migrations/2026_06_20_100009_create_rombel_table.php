<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombel', function (Blueprint $table) {
            $table->id();
            // STRUKTUR → terikat TAHUN AJARAN, bukan semester (database.md; Revisi Tahap 3).
            // Dengan begini rombel TIDAK lahir ulang tiap semester, sehingga peserta didik
            // tidak perlu di-clone ganjil→genap (akar bug "1 siswa banyak rombel").
            $table->string('tahun_ajaran', 9)->index();
            $table->foreignId('wilayah_id')->constrained('wilayah')->restrictOnDelete();
            $table->foreignId('paket_id')->constrained('paket')->restrictOnDelete();
            $table->foreignId('tingkat_id')->constrained('tingkat')->restrictOnDelete();
            // wali_kelas_id → guru.id; nullable, null saat rombel baru dibuat
            $table->unsignedBigInteger('wali_kelas_id')->nullable();
            $table->foreign('wali_kelas_id')->references('id')->on('guru')->nullOnDelete();
            $table->string('nama', 150);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rombel');
    }
};
