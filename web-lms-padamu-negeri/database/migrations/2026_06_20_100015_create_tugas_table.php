<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_rombel_id')->constrained('guru_mapel_rombel')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('lampiran_path', 255)->nullable();
            $table->dateTime('deadline');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
