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
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Cek apakah OTP kedaluwarsa
        if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Cek apakah OTP cocok
        if ($request->otp !== $user->otp_code) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan coba lagi.']);
        }

        // OTP valid — tandai email sebagai terverifikasi & hapus OTP
        $user->forceFill([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang.');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));

        return back()->with('success', 'Kode OTP baru telah dikirim ke email kamu.');
    }
}
