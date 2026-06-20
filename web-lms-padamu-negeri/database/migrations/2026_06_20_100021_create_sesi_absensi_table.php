<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_rombel_id')->constrained('guru_mapel_rombel')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status_sesi', ['terbuka', 'ditutup'])->default('terbuka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_absensi');
    }
};
