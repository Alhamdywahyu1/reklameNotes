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
        Schema::create('approval_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_reklame')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            
            $table->enum('status_approval', [
                'Diverifikasi Operator',
                'Disetujui Kepala Seksi',
                'Ditolak Kepala Seksi',
                'Disetujui Kepala Bidang'
            ]);
            
            $table->enum('keputusan', ['Disetujui', 'Ditolak'])->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamp('tanggal_approval');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('permohonan_id');
            $table->index('user_id');
            $table->index('tanggal_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflow');
    }
};
