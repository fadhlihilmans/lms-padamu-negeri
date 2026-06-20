<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_didik_rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombel')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['peserta_didik_id', 'rombel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_didik_rombel');
    }
};
