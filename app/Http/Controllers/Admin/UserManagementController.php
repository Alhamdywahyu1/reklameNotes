<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
class UserManagementController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        // Check authorization
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }

        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role_id.required' => 'Role wajib dipilih',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_USER',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Membuat user baru: {$user->name} ({$user->email})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Prevent editing super admin
        if ($user->id === 1 && auth()->id() !== 1) {
            abort(403, 'Anda tidak dapat mengedit super admin');
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        if ($user->id === 1 && auth()->id() !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $oldValues = $user->getAttributes();
        $validated['is_active'] = $request->boolean('is_active');

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_USER',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Mengupdate user: {$user->name}",
            'old_values' => $oldValues,
            'new_values' => $user->getAttributes(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Delete the specified user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Prevent deleting super admin dan current user
        if ($user->id === 1 || $user->id === auth()->id()) {
            abort(403, 'Tidak dapat menghapus user ini');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Soft delete
        $user->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_USER',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Menghapus user: {$userName} ({$userEmail})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Restore deleted user.
     */
    public function restore(User $user): RedirectResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $user->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'RESTORE_USER',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "Restore user: {$user->name}",
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dipulihkan');
    }
}
