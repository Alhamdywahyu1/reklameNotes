<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->string('klasifikasi_lokasi')->nullable()->after('lokasi_pemasangan');
            $table->string('keperluan_reklame')->nullable()->after('klasifikasi_lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->dropColumn(['klasifikasi_lokasi', 'keperluan_reklame']);
        });
    }
};
