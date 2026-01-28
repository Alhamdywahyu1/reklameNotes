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
        Schema::create('permohonan_reklame', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Data Pemohon
            $table->string('nama_pemohon');
            $table->text('alamat_pemohon');
            $table->string('nomor_telepon');
            $table->string('nik', 16);
            $table->string('npwp')->nullable();
            
            // Data Reklame
            $table->enum('jenis_reklame', ['Permanen', 'Non Permanen']);
            $table->string('ukuran_reklame');
            $table->integer('jumlah_reklame')->default(1);
            $table->text('narasi_reklame');
            $table->text('lokasi_pemasangan');
            
            // File/Dokumen
            $table->string('file_ktp')->nullable();
            $table->string('file_npwp')->nullable();
            $table->string('file_desain')->nullable();
            
            // Status
            $table->enum('status', [
                'Draft',
                'Diajukan',
                'Diverifikasi Operator',
                'Ditolak Operator',
                'Disetujui Kepala Seksi',
                'Ditolak Kepala Seksi',
                'Disetujui Kepala Bidang'
            ])->default('Draft');
            
            $table->text('keterangan_penolakan')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('user_id');
            $table->index('nomor_registrasi');
            $table->index('status');
            $table->index('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_reklame');
    }
};
