<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id jika belum ada (user sudah register manual sebelumnya)
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Cek apakah akun aktif
            if (!$user->is_active) {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda dinonaktifkan. Hubungi administrator.');
            }

            // Set email verified
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }

            Auth::login($user, true);
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('dashboard'));
        }

        // User baru - buat akun otomatis sebagai pemohon
        $pemohonRole = Role::where('slug', 'pemohon')->firstOrFail();

        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => Hash::make(Str::random(24)),
            'role_id' => $pemohonRole->id,
            'is_active' => true,
            'email_verified_at' => now(), // Google sudah verifikasi email
        ]);

        Auth::login($user, true);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang! Akun Anda berhasil dibuat melalui Google.');
    }
}
