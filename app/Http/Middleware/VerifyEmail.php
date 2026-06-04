<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow disabling OTP verification via environment for testing
        if (!env('OTP_VERIFICATION_ENABLED', true)) {
            return $next($request);
        }

        $user = auth()->user();

        // Jika user sudah terverifikasi atau tidak login, lanjutkan
        if (!$user || $user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Redirect ke OTP verification page jika belum verified
        return redirect()->route('otp.show')->with('warning', 'Silakan verifikasi email Anda terlebih dahulu.');
    }
}
