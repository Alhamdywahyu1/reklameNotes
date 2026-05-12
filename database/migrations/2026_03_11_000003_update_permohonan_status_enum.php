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
        // Update enum status untuk mendukung revisi yang routing ke petugas tertentu
        // Mengubah dari generic "Revisi Menunggu Verifikasi" menjadi spesifik ke role yang menolak
        
        // Only execute MODIFY COLUMN for MySQL (ENUM type support)
        if (DB::getDriverName() === 'mysql') {
            Schema::table('permohonan_reklame', function (Blueprint $table) {
                // Pertama, alter table untuk include BOTH old dan new enum values
                // Ini memungkinkan kita update data tanpa error
                DB::statement("ALTER TABLE permohonan_reklame MODIFY COLUMN status ENUM(
                    'Draft',
                    'Diajukan',
                    'Revisi Menunggu Verifikasi',
                    'Revisi Menunggu Operator',
                    'Diverifikasi Operator',
                    'Ditolak Operator',
                    'Revisi Menunggu Kepala Seksi',
                    'Disetujui Kepala Seksi',
                    'Ditolak Kepala Seksi',
                    'Disetujui Kepala Bidang',
                    'Ditolak Kepala Bidang'
                ) DEFAULT 'Draft'");
            });
        }
        
        // Update old status values ke yang baru (works for both MySQL and SQLite)
        DB::statement("UPDATE permohonan_reklame SET status = 'Revisi Menunggu Operator' WHERE status = 'Revisi Menunggu Verifikasi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            DB::statement("ALTER TABLE permohonan_reklame MODIFY COLUMN status ENUM(
                'Draft',
                'Diajukan',
                'Diverifikasi Operator',
                'Ditolak Operator',
                'Disetujui Kepala Seksi',
                'Ditolak Kepala Seksi',
                'Disetujui Kepala Bidang',
                'Revisi Menunggu Verifikasi'
            ) DEFAULT 'Draft'");
        });
    }
};
