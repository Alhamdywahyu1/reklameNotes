<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Rate limiting: max 5 attempts per minute per email
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]); 

        // Check if user exists
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        if (!$user) {
            // Increment failed attempts
            RateLimiter::hit($this->throttleKey($request));
            
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar dalam sistem',
            ]);
        }

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Increment failed attempts
            RateLimiter::hit($this->throttleKey($request));
            
            throw ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah',
            ]);
        }

        // Clear rate limiting on successful login
        RateLimiter::clear($this->throttleKey($request));

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'LOGIN',
            'model_type' => 'User',
            'model_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'keterangan' => 'User login berhasil',
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'LOGOUT',
            'model_type' => 'User',
            'model_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'keterangan' => 'User logout',
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'Terlalu banyak percobaan login gagal. Silakan coba lagi dalam ' . $seconds . ' detik.',
        ])->status(429);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return 'login_attempts:' . $request->ip();
    }
}
