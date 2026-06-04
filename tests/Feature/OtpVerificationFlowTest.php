<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\User;

class OtpVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role pemohon required by registration
        Role::create([ 'name' => 'Pemohon', 'slug' => 'pemohon', 'description' => 'Pemohon role' ]);
    }

    public function test_unverified_user_redirected_when_submitting_permohonan()
    {
        // Register user via controller
        $password = 'Password123!';

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Test 1',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertRedirect(route('otp.show'));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        // Attempt to submit permohonan while unverified
        $permohonanData = [
            'nama_pemohon' => 'Test User',
            'alamat_pemohon' => 'Jl. Test 1',
            'nomor_telepon' => '081234567890',
            'nik' => '1234567890123456',
            'npwp' => null,
            'jenis_reklame' => 'Permanen',
            'ukuran_reklame' => '3m x 5m',
            'jumlah_reklame' => 1,
            'narasi_reklame' => 'Iklan produk',
            'lokasi_pemasangan' => 'Depan toko',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ];

        // Acting as the created user
        $this->actingAs($user);

        $storeResponse = $this->post(route('permohonan.store'), $permohonanData);

        // Should redirect to otp.show because user not verified
        $storeResponse->assertRedirect(route('otp.show'));
    }

    public function test_verified_user_can_submit_permohonan()
    {
        $password = 'Password123!';

        $this->post('/register', [
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'phone' => '081234567891',
            'address' => 'Jl. Verified',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'verified@example.com')->first();
        $this->assertNotNull($user);

        // Mark email verified
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        $permohonanData = [
            'nama_pemohon' => 'Verified User',
            'alamat_pemohon' => 'Jl. Verified',
            'nomor_telepon' => '081234567891',
            'nik' => '2234567890123456',
            'npwp' => null,
            'jenis_reklame' => 'Permanen',
            'ukuran_reklame' => '4m x 6m',
            'jumlah_reklame' => 1,
            'narasi_reklame' => 'Iklan layanan',
            'lokasi_pemasangan' => 'Depan kantor',
            'latitude' => -6.210000,
            'longitude' => 106.816000,
        ];

        $storeResponse = $this->post(route('permohonan.store'), $permohonanData);

        // After successful creation, should redirect (to permohonan.index or elsewhere). We'll assert DB contains record
        $this->assertDatabaseHas('permohonan_reklame', [
            'nama_pemohon' => 'Verified User',
            'nik' => '2234567890123456',
        ]);
    }
}
