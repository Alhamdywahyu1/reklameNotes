<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $pemohonRole = Role::where('slug', 'pemohon')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role_id' => $pemohonRole->id,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Generate OTP 6 digit & kirim ke email
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
            'last_otp_sent_at' => now(),
        ])->save();

        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));

        Auth::login($user);

        return redirect()->route('otp.show');
    }
}
