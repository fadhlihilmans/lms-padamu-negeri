<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_mapel_rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapel')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombel')->cascadeOnDelete();
            // STRUKTUR → terikat TAHUN AJARAN. Plotting cukup 1x per TA; semester genap
            // otomatis memakai data yang sama (tanpa clone). Lihat keputusan-revisi.md.
            $table->string('tahun_ajaran', 9)->index();
            $table->timestamps();

            $table->unique(['guru_id', 'mapel_id', 'rombel_id', 'tahun_ajaran'], 'gmrp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel_rombel');
    }
};
