<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing GlobalScope Application ===" . PHP_EOL;

// Method 1: Query with User::find() - should apply GlobalScope
$user1 = \App\Models\User::find(2);
echo 'Method 1 - User::find(2):' . PHP_EOL;
echo '  Role relation loaded: ' . ($user1->relationLoaded('role') ? 'YES' : 'NO') . PHP_EOL;
echo '  Role slug: ' . ($user1->role?->slug ?? 'NULL') . PHP_EOL;

// Method 2: Query directly without scope
$user2 = \App\Models\User::withoutGlobalScopes()->find(2);
echo PHP_EOL . 'Method 2 - User::withoutGlobalScopes()->find(2):' . PHP_EOL;
echo '  Role relation loaded: ' . ($user2->relationLoaded('role') ? 'YES' : 'NO') . PHP_EOL;
echo '  Role slug: ' . ($user2->role?->slug ?? 'NULL') . PHP_EOL;

// Method 3: Simulate what auth()->user() might do
echo PHP_EOL . "=== Simulating Auth User ===" . PHP_EOL;

\Illuminate\Support\Facades\Auth::setUser(\App\Models\User::find(2));
$authUser = \Illuminate\Support\Facades\Auth::user();
echo 'Auth::user():' . PHP_EOL;
echo '  Role relation loaded: ' . ($authUser->relationLoaded('role') ? 'YES' : 'NO') . PHP_EOL;
echo '  Role slug: ' . ($authUser->role?->slug ?? 'NULL') . PHP_EOL;

// Try to manually reload
$authUser->load('role');
echo PHP_EOL . 'After manual load(role):' . PHP_EOL;
echo '  Role relation loaded: ' . ($authUser->relationLoaded('role') ? 'YES' : 'NO') . PHP_EOL;
echo '  Role slug: ' . ($authUser->role?->slug ?? 'NULL') . PHP_EOL;
