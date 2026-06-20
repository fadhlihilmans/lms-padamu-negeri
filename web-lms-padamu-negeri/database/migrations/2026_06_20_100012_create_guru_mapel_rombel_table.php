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
            $table->foreignId('periode_ajaran_id')->constrained('periode_ajaran')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guru_id', 'mapel_id', 'rombel_id', 'periode_ajaran_id'], 'gmrp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel_rombel');
    }
};
