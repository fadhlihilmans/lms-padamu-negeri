<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_cbt', function (Blueprint $table) {
            // Override guru: tampilkan nilai ke PD meski tampilkan_nilai_otomatis = false.
            $table->boolean('nilai_ditampilkan')->default(false)->after('status_penilaian');
        });
    }

    public function down(): void
    {
        Schema::table('hasil_cbt', function (Blueprint $table) {
            $table->dropColumn('nilai_ditampilkan');
        });
    }
};
