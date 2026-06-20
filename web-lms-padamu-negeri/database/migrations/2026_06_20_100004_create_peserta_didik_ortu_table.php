<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_didik_ortu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->enum('jenis', ['ayah', 'ibu', 'wali']);
            $table->string('nama', 150)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('hubungan_wali', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_didik_ortu');
    }
};
