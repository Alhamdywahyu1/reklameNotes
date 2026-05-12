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
        Schema::create('surat_pernyataan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permohonan_id')->unique();
            $table->unsignedBigInteger('user_id');
            
            // Data Pemohon
            $table->string('nama_pemohon');
            $table->string('pekerjaan')->nullable();
            $table->text('alamat_pemohon');
            $table->string('no_ktp');
            
            // Status Pernyataan
            $table->enum('status', ['draft', 'submitted', 'verified', 'rejected'])->default('draft');
            $table->boolean('setuju_syarat_1')->default(false);
            $table->boolean('setuju_syarat_2')->default(false);
            $table->boolean('setuju_syarat_3')->default(false);
            $table->boolean('setuju_syarat_4')->default(false);
            $table->boolean('setuju_syarat_5')->default(false);
            $table->boolean('setuju_syarat_6')->default(false);
            $table->boolean('setuju_syarat_7')->default(false);
            $table->boolean('setuju_syarat_8')->default(false);
            
            // Dokumen Pendukung
            $table->string('file_tanda_tangan')->nullable();
            $table->string('file_materai')->nullable();
            $table->date('tanggal_pernyataan')->nullable();
            
            // Audit
            $table->text('keterangan_penolakan')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('permohonan_id')
                ->references('id')
                ->on('permohonan_reklame')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pernyataan');
    }
};
