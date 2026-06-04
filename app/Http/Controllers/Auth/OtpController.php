<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman input OTP
     */
    public function show(): View|RedirectResponse
    {
        // Jika verifikasi OTP dinonaktifkan via env, jangan tampilkan halaman OTP
        if (!env('OTP_VERIFICATION_ENABLED', true)) {
            return redirect()->route('dashboard');
        }

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.otp-verify');
    }

    /**
     * Verifikasi kode OTP yang diinput user
     */
    public function verify(Request $request): RedirectResponse
    {
        // Jika verifikasi OTP dinonaktifkan via env, bypass verifikasi
        if (!env('OTP_VERIFICATION_ENABLED', true)) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Cek apakah sudah mencapai batas attempt (5 kali gagal)
        if ($user->otp_attempts >= 5) {
            return back()->withErrors(['otp' => 'Anda telah mencoba terlalu banyak kali. Silakan minta kode OTP baru.']);
        }

        // Cek apakah OTP kedaluwarsa
        if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Cek apakah OTP cocok
        if ($request->otp !== $user->otp_code) {
            // Increment attempt counter
            $user->increment('otp_attempts');
            $remaining = 5 - $user->fresh()->otp_attempts;
            
            return back()->withErrors(['otp' => "Kode OTP salah. Sisa percobaan: {$remaining}"]);
        }

        // OTP valid — tandai email sebagai terverifikasi & hapus OTP & reset attempts
        $user->forceFill([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'last_otp_sent_at' => null,
        ])->save();

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang.');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        // Jika verifikasi OTP dinonaktifkan via env, jangan proses resend
        if (!env('OTP_VERIFICATION_ENABLED', true)) {
            return redirect()->route('dashboard');
        }

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Cek cooldown - tidak boleh kirim lebih dari 1x dalam 60 detik
        if ($user->last_otp_sent_at && now()->diffInSeconds($user->last_otp_sent_at) < 60) {
            $remaining = 60 - now()->diffInSeconds($user->last_otp_sent_at);
            return back()->withErrors(['resend' => "Silakan tunggu {$remaining} detik sebelum meminta OTP baru."]);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
            'last_otp_sent_at' => now(),
        ])->save();

        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));

        return back()->with('success', 'Kode OTP baru telah dikirim ke email kamu.');
    }
}
