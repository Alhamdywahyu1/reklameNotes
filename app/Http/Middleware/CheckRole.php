<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Step 1: Check authentication
        if (!auth()->check()) {
            \Log::warning('CheckRole: User not authenticated, redirecting to login');
            return redirect('login');
        }

        // Step 2: Get user and verify it exists
        $user = auth()->user();
        if (!$user) {
            \Log::warning('CheckRole: auth()->user() returned null despite auth()->check() being true');
            return redirect('login');
        }

        \Log::debug('CheckRole: User authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);

        // Step 3: Ensure role relationship is loaded from database
        // Even if cached in session, force reload to get latest data
        if ($user->relationLoaded('role')) {
            \Log::debug('CheckRole: Role already loaded');
            $roleSlug = $user->role?->slug;
        } else {
            \Log::debug('CheckRole: Loading role relationship');
            $user->load('role');
            $roleSlug = $user->role?->slug;
        }

        \Log::debug('CheckRole: Role check', [
            'required_roles' => $roles,
            'user_role_slug' => $roleSlug,
            'role_id' => $user->role_id,
        ]);

        // Step 4: Check if user has required role
        if (empty($roles)) {
            \Log::warning('CheckRole: No roles specified in middleware');
            abort(403, 'No roles specified');
        }

        // Explicit check - user role must be one of the required roles
        if (!in_array($roleSlug, $roles, true)) {
            \Log::warning('CheckRole: User does not have required role', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_slug' => $roleSlug,
                'required_roles' => $roles,
            ]);
            abort(403, 'You do not have permission to access this resource');
        }

        \Log::debug('CheckRole: Access granted', [
            'user_id' => $user->id,
            'role_slug' => $roleSlug,
        ]);

        return $next($request);
    }
}

