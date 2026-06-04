<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['slug' => 'satpol_pp'],
            [
                'name' => 'Satpol PP',
                'description' => 'Petugas Satpol PP untuk pengawasan reklame di lapangan',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank to avoid removing role data from live systems.
    }
};
