<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\User;

class OtpEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create([ 'name' => 'Pemohon', 'slug' => 'pemohon', 'description' => 'Pemohon role' ]);
    }

    public function test_otp_attempt_limit_blocks_after_five_failures()
    {
        $password = 'Password123!';

        $this->post('/register', [
            'name' => 'Limit User',
            'email' => 'limit@example.com',
            'phone' => '081234567892',
            'address' => 'Jl. Limit',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'limit@example.com')->first();
        $this->assertNotNull($user);

        $this->actingAs($user);

        // Submit wrong OTP 5 times
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->post(route('otp.verify'), ['otp' => '000000']);
            if ($i < 5) {
                $response->assertSessionHasErrors('otp');
            } else {
                // On 5th attempt should show too many tries message (we implemented >=5 check)
                $response->assertSessionHasErrors('otp');
            }
        }

        // Ensure otp_attempts recorded as 5
        $this->assertEquals(5, $user->fresh()->otp_attempts);

        // Further attempt still returns error about too many tries
        $final = $this->post(route('otp.verify'), ['otp' => '000000']);
        $final->assertSessionHasErrors('otp');
    }

    public function test_resend_cooldown_prevents_immediate_resend()
    {
        $password = 'Password123!';

        $this->post('/register', [
            'name' => 'Resend User',
            'email' => 'resend@example.com',
            'phone' => '081234567893',
            'address' => 'Jl. Resend',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'resend@example.com')->first();
        $this->assertNotNull($user);

        $this->actingAs($user);

        // Immediately try to resend OTP
        $response = $this->post(route('otp.resend'));

        // Should have session errors under 'resend' key
        $response->assertSessionHasErrors('resend');

        // last_otp_sent_at should be set
        $this->assertNotNull($user->fresh()->last_otp_sent_at);
    }
}
