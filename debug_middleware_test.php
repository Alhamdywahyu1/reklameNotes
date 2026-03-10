<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing Middleware Logic ===" . PHP_EOL;

// Get operator user
$user = \App\Models\User::find(2);
echo 'Testing user: ' . $user->email . ' (role_slug: ' . $user->role?->slug . ')' . PHP_EOL;

// Simulate the middleware roles parameter (from route: role:operator,kepala_seksi,kepala_bidang,admin)
$middlewareRoles = ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'];

echo 'Middleware roles: ' . implode(', ', $middlewareRoles) . PHP_EOL;
echo 'User role slug: ' . $user->role?->slug . PHP_EOL;

// Test hasAnyRole
$result = $user->hasAnyRole($middlewareRoles);
echo 'hasAnyRole result: ' . ($result ? 'TRUE (PASS)' : 'FALSE (FAIL)') . PHP_EOL;

echo PHP_EOL . "=== Testing Direct Role Check ===" . PHP_EOL;
echo 'in_array check: ' . (in_array($user->role?->slug, $middlewareRoles) ? 'TRUE' : 'FALSE') . PHP_EOL;

// Also test with authenticated session
echo PHP_EOL . "=== Testing with Auth Session ===" . PHP_EOL;

// Create a request context as if user is logged in
\Illuminate\Support\Facades\Auth::setUser($user);

$authUser = \Illuminate\Support\Facades\Auth::user();
if ($authUser) {
    echo 'Auth user email: ' . $authUser->email . PHP_EOL;
    echo 'Auth user role: ' . $authUser->role?->slug . PHP_EOL;
    echo 'Auth hasAnyRole: ' . ($authUser->hasAnyRole($middlewareRoles) ? 'TRUE' : 'FALSE') . PHP_EOL;
} else {
    echo 'Auth::user() returned null' . PHP_EOL;
}
