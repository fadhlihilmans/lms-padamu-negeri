<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_submisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('peserta_didik_id')->constrained('peserta_didik')->cascadeOnDelete();
            $table->string('file_path', 255)->nullable();
            $table->text('isi_text')->nullable();
            $table->dateTime('waktu_submit');
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tugas_id', 'peserta_didik_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_submisi');
    }
};
