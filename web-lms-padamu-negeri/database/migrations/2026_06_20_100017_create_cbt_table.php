<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_rombel_id')->constrained('guru_mapel_rombel')->cascadeOnDelete();
            $table->string('nama_ujian', 200);
            $table->unsignedTinyInteger('kkm');
            $table->dateTime('tanggal_mulai');
            $table->unsignedSmallInteger('durasi_menit');
            $table->boolean('tampilkan_nilai_otomatis')->default(false);
            $table->enum('jenis_cbt', ['pilihan_ganda', 'uraian', 'campuran']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt');
    }
};
