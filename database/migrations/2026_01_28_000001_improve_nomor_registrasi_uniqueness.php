<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Improvements:
     * 1. Ensure nomor_registrasi has unique constraint (already in place)
     * 2. Add sequence counter table for sequential generation option
     * 3. Add index for faster lookups
     */
    public function up(): void
    {
        // Create sequence counter table for alternative generation method
        Schema::create('registrasi_sequences', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->unsignedBigInteger('counter')->default(1);
            $table->timestamps();
            
            // Unique constraint: satu counter per tahun
            $table->unique('tahun');
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrasi_sequences');
    }
};
