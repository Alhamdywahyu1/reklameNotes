<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('persyaratan_dokumen', function (Blueprint $table) {
            $table->enum('status', ['Belum Lengkap', 'Lengkap', 'Ditolak'])->default('Belum Lengkap')->after('keterangan');
            $table->text('catatan_penolakan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persyaratan_dokumen', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_penolakan']);
        });
    }
};
