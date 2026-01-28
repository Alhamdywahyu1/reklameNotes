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
        Schema::create('persyaratan_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_reklame')->onDelete('cascade');
            
            $table->enum('jenis_persyaratan', [
                'Fotocopy KTP berwarna',
                'Fotocopy NPWP berwarna',
                'Fotocopy Akta Pendirian',
                'Fotocopy Retribusi Pajak Reklame',
                'Data Isian Pemohon',
                'Surat Pernyataan Pertanggungjawaban Konstruksi',
                'Foto kondisi & visualisasi reklame',
                'Gambar konstruksi bidang',
                'Surat Kuasa'
            ]);
            
            $table->boolean('is_lengkap')->default(false);
            $table->string('file_dokumen')->nullable();
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('permohonan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persyaratan_dokumen');
    }
};
