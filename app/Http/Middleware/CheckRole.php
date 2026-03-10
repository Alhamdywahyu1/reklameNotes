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
        // Log the initial check
        \Log::debug('CheckRole Middleware - Request path: ' . $request->getPathInfo());
        \Log::debug('CheckRole Middleware - Required roles: ' . implode(',', $roles));
        
        if (!auth()->check()) {
            \Log::warning('CheckRole Middleware - User not authenticated');
            return redirect('login');
        }

        // Reload user with role relationship to ensure role is loaded
        $user = auth()->user();
        \Log::debug('CheckRole Middleware - User ID: ' . $user->id . ', Email: ' . $user->email);
        
        if (!$user->relationLoaded('role')) {
            \Log::debug('CheckRole Middleware - Role not loaded, calling load()');
            $user->load('role');
        } else {
            \Log::debug('CheckRole Middleware - Role already loaded');
        }
        
        \Log::debug('CheckRole Middleware - User role_id: ' . $user->role_id . ', Role slug: ' . ($user->role?->slug ?? 'NULL'));
        
        $hasRole = $user->hasAnyRole($roles);
        \Log::debug('CheckRole Middleware - hasAnyRole result: ' . ($hasRole ? 'TRUE' : 'FALSE'));
        
        if (!$hasRole) {
            \Log::warning('CheckRole Middleware - Access denied for user ' . $user->email . ' (role: ' . ($user->role?->slug ?? 'NULL') . ')');
            abort(403, 'Unauthorized access');
        }

        \Log::debug('CheckRole Middleware - Access granted');
        return $next($request);
    }
}
