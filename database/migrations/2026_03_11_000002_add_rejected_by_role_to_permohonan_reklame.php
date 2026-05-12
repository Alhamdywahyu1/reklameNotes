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
            // Tambah kolom untuk tracking role yang menolak
            $table->foreignId('rejected_by_role_id')->nullable()->after('status')->constrained('roles')->onDelete('set null');
            
            // Tambah kolom untuk tracking user yang menolak
            $table->foreignId('rejected_by_user_id')->nullable()->after('rejected_by_role_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_reklame', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['rejected_by_role_id']);
            $table->dropForeignKeyIfExists(['rejected_by_user_id']);
            $table->dropColumn(['rejected_by_role_id', 'rejected_by_user_id']);
        });
    }
};
