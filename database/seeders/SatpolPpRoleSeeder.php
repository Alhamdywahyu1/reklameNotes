<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SatpolPpRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tambahkan Role Satpol PP jika belum ada
        $roleId = DB::table('roles')->where('slug', 'satpol_pp')->value('id');
        
        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'Satpol PP',
                'slug' => 'satpol_pp',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Buat Akun Dummy untuk Satpol PP (Untuk Testing)
        if (!DB::table('users')->where('email', 'satpolpp@example.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Petugas Satpol PP',
                'email' => 'satpolpp@example.com',
                'password' => Hash::make('password'),
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}