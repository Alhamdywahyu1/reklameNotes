<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit(): View
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    /**
     * Update general profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'nik'     => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah digunakan oleh akun lain',
            'phone.max'      => 'Nomor telepon maksimal 20 karakter',
        ]);

        // If email changed, reset verification
        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.required'      => 'Password saat ini wajib diisi',
            'current_password.current_password' => 'Password saat ini tidak sesuai',
            'password.required'              => 'Password baru wajib diisi',
            'password.confirmed'             => 'Konfirmasi password tidak sesuai',
            'password.min'                   => 'Password minimal 8 karakter',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('password_success', 'Password berhasil diperbarui.');
    }
}
