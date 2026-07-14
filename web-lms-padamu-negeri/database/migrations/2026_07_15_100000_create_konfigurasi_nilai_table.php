<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bobot penilaian (Revisi Tahap 4).
 *
 * Sengaja TERPISAH dari tabel `settings` — pengecualian resmi CLAUDE.md #9a:
 * bobot dikelola bersama rentang grade di halaman "Konfigurasi Nilai".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();          // mis. bobot_cbt_pg
            $table->string('value', 50);                    // persen, disimpan string
            $table->enum('type', ['integer'])->default('integer');
            $table->string('grup', 50)->index();            // cbt | komponen_tugas | rapor
            $table->string('label', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_nilai');
    }
};
