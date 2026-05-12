<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new status enum value
        // For MySQL, we need to modify the enum
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `permohonan_reklame` MODIFY COLUMN `status` ENUM(
                'Draft',
                'Diajukan',
                'Diverifikasi Operator',
                'Revisi Menunggu Verifikasi',
                'Ditolak Operator',
                'Disetujui Kepala Seksi',
                'Ditolak Kepala Seksi',
                'Disetujui Kepala Bidang',
                'Ditolak Kepala Bidang'
            ) DEFAULT 'Draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `permohonan_reklame` MODIFY COLUMN `status` ENUM(
                'Draft',
                'Diajukan',
                'Diverifikasi Operator',
                'Ditolak Operator',
                'Disetujui Kepala Seksi',
                'Ditolak Kepala Seksi',
                'Disetujui Kepala Bidang'
            ) DEFAULT 'Draft'");
        }
    }
};
