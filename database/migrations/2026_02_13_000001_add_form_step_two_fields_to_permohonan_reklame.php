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
            // Form Step 2 Fields
            $table->string('pekerjaan')->nullable()->after('npwp');
            $table->enum('status_reklame', ['Baru', 'Perpanjangan'])->nullable()->after('pekerjaan');
            $table->string('nama_reklame')->nullable()->after('status_reklame');
            $table->text('alamat_perusahaan')->nullable()->after('nama_reklame');
            $table->integer('jumlah_warna')->nullable()->after('alamat_perusahaan');
            $table->string('rata_rata')->nullable()->after('jumlah_warna');
            $table->date('masa_berlaku')->nullable()->after('rata_rata');
            
            // Track form completion
            $table->integer('form_step')->default(1)->after('masa_berlaku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->dropColumn([
                'pekerjaan',
                'status_reklame',
                'nama_reklame',
                'alamat_perusahaan',
                'jumlah_warna',
                'rata_rata',
                'masa_berlaku',
                'form_step'
            ]);
        });
    }
};
