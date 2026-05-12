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
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->date('tanggal_berlaku')->nullable()->after('status');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_berlaku');
            $table->enum('status_kedaluwarsa', ['Aktif', 'Kedaluwarsa', 'Dicabut'])->default('Aktif')->after('tanggal_berakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->dropColumn(['tanggal_berlaku', 'tanggal_berakhir', 'status_kedaluwarsa']);
        });
    }
};
