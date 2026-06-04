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
     * Role petugas yang boleh dikelola oleh admin.
     * Pemohon dan admin tidak termasuk.
     */
    private const ALLOWED_ROLES = ['operator', 'kepala_seksi', 'kepala_bidang', 'satpol_pp'];

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini');
        }

        $search = $request->query('search', '');

        // Hanya tampilkan akun petugas (bukan pemohon / admin)
        $query = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('slug', self::ALLOWED_ROLES));

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Hanya role petugas yang bisa dipilih
        $roles = Role::whereIn('slug', self::ALLOWED_ROLES)->get();
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

        $allowedRoleIds = Role::whereIn('slug', self::ALLOWED_ROLES)->pluck('id')->toArray();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id'  => ['required', 'exists:roles,id', 'in:' . implode(',', $allowedRoleIds)],
            'is_active' => 'boolean',
        ], [
            'name.required'     => 'Nama wajib diisi',
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'password.confirmed'=> 'Konfirmasi password tidak sesuai',
            'role_id.required'  => 'Role wajib dipilih',
            'role_id.in'        => 'Role yang dipilih tidak diizinkan',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'CREATE_USER',
            'model_type'  => 'User',
            'model_id'    => $user->id,
            'description' => "Membuat user baru: {$user->name} ({$user->email})",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
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

        // Hanya boleh edit akun petugas
        if (!in_array($user->role?->slug, self::ALLOWED_ROLES)) {
            abort(403, 'Anda tidak dapat mengedit akun ini');
        }

        // Hanya role petugas yang bisa dipilih
        $roles = Role::whereIn('slug', self::ALLOWED_ROLES)->get();
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

        // Hanya boleh update akun petugas
        if (!in_array($user->role?->slug, self::ALLOWED_ROLES)) {
            abort(403, 'Anda tidak dapat mengubah akun ini');
        }

        $allowedRoleIds = Role::whereIn('slug', self::ALLOWED_ROLES)->pluck('id')->toArray();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role_id'   => ['required', 'exists:roles,id', 'in:' . implode(',', $allowedRoleIds)],
            'is_active' => 'boolean',
        ]);

        $oldValues = $user->getAttributes();
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'UPDATE_USER',
            'model_type'  => 'User',
            'model_id'    => $user->id,
            'description' => "Mengupdate user: {$user->name}",
            'old_values'  => $oldValues,
            'new_values'  => $user->getAttributes(),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
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

        // Hanya boleh hapus akun petugas
        if (!in_array($user->role?->slug, self::ALLOWED_ROLES)) {
            abort(403, 'Tidak dapat menghapus akun ini');
        }

        if ($user->id === auth()->id()) {
            abort(403, 'Tidak dapat menghapus akun Anda sendiri');
        }

        $userName  = $user->name;
        $userEmail = $user->email;

        $user->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'DELETE_USER',
            'model_type'  => 'User',
            'model_id'    => $user->id,
            'description' => "Menghapus user: {$userName} ({$userEmail})",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
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
