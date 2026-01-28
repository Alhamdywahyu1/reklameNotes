<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Roles
        $roles = [
            [
                'name' => 'Pengguna (Pemohon)',
                'slug' => 'pemohon',
                'description' => 'Pengguna yang mengajukan permohonan reklame',
            ],
            [
                'name' => 'Operator',
                'slug' => 'operator',
                'description' => 'Operator yang memverifikasi dokumen dan melakukan approval tahap pertama',
            ],
            [
                'name' => 'Kepala Seksi',
                'slug' => 'kepala_seksi',
                'description' => 'Kepala Seksi yang melakukan approval tahap kedua',
            ],
            [
                'name' => 'Kepala Bidang',
                'slug' => 'kepala_bidang',
                'description' => 'Kepala Bidang yang melakukan approval final',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin sistem',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        // Buat User Test untuk setiap role
        $testUsers = [
            [
                'name' => 'Budi Pemohon',
                'email' => 'pemohon@dpmptsp.local',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('slug', 'pemohon')->first()->id,
                'nik' => '1234567890123456',
                'phone' => '081234567890',
                'address' => 'Jl. Pemohon No. 1',
                'is_active' => true,
            ],
            [
                'name' => 'Ani Operator',
                'email' => 'operator@dpmptsp.local',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('slug', 'operator')->first()->id,
                'phone' => '082234567890',
                'address' => 'Jl. DPMPTSP No. 1',
                'is_active' => true,
            ],
            [
                'name' => 'Citra Kepala Seksi',
                'email' => 'kepala.seksi@dpmptsp.local',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('slug', 'kepala_seksi')->first()->id,
                'phone' => '083234567890',
                'address' => 'Jl. DPMPTSP No. 1',
                'is_active' => true,
            ],
            [
                'name' => 'Doni Kepala Bidang',
                'email' => 'kepala.bidang@dpmptsp.local',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('slug', 'kepala_bidang')->first()->id,
                'phone' => '084234567890',
                'address' => 'Jl. DPMPTSP No. 1',
                'is_active' => true,
            ],
            [
                'name' => 'Eka Admin',
                'email' => 'admin@dpmptsp.local',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('slug', 'admin')->first()->id,
                'phone' => '085234567890',
                'address' => 'Jl. DPMPTSP No. 1',
                'is_active' => true,
            ],
        ];

        foreach ($testUsers as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('Roles dan Users berhasil dibuat!');
    }
}
