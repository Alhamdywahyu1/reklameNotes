<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Checking Roles in Database ===" . PHP_EOL;
$roles = \App\Models\Role::all();
echo 'Total Roles: ' . $roles->count() . PHP_EOL;
foreach ($roles as $role) {
    echo 'ID: ' . $role->id . ', Name: ' . $role->name . ', Slug: ' . $role->slug . PHP_EOL;
}

echo PHP_EOL . "=== Checking Users with role_id ===" . PHP_EOL;
$users = \App\Models\User::whereNotNull('role_id')->with('role')->get();
echo 'Total Users with role_id: ' . $users->count() . PHP_EOL;
foreach ($users as $user) {
    $roleSlug = $user->role?->slug ?? 'NULL';
    echo 'User #' . $user->id . ' (' . $user->email . '): role_id=' . $user->role_id . ', role_slug=' . $roleSlug . PHP_EOL;
}

echo PHP_EOL . "=== Testing hasAnyRole for User #2 ===" . PHP_EOL;
$user = \App\Models\User::find(2);
if ($user) {
    echo 'User found: ' . $user->email . PHP_EOL;
    echo 'Role ID: ' . $user->role_id . PHP_EOL;
    echo 'Role Slug: ' . ($user->role?->slug ?? 'NULL') . PHP_EOL;
    $result = $user->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']);
    echo 'hasAnyRole result: ' . ($result ? 'TRUE' : 'FALSE') . PHP_EOL;
} else {
    echo 'User #2 not found' . PHP_EOL;
}
