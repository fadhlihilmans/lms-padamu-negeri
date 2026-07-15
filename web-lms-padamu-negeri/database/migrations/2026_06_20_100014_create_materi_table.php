<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_mapel_rombel_id')->constrained('guru_mapel_rombel')->cascadeOnDelete();
            // TRANSAKSI → terikat PERIODE (TA + semester). Wajib karena guru_mapel_rombel
            // kini per-TA sehingga tidak lagi membawa info semester (Revisi Tahap 3).
            $table->foreignId('periode_ajaran_id')->constrained('periode_ajaran')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->longText('isi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('materi_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->enum('tipe', ['file', 'gambar', 'link_video']);
            $table->string('file_path', 255)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('nama_asli', 255)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_lampiran');
        Schema::dropIfExists('materi');
    }
};
