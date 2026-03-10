<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Test middleware parameter parsing
echo "=== Testing Middleware Parameter Parsing ===" . PHP_EOL;

// Simulate what Laravel does with middleware('role:operator,kepala_seksi,kepala_bidang,admin')
$parameters = 'operator,kepala_seksi,kepala_bidang,admin';

// Laravel splits by comma
$rolesArray = explode(',', $parameters);
echo 'Parameters: ' . $parameters . PHP_EOL;
echo 'Parsed as array: ' . implode(', ', $rolesArray) . PHP_EOL;
echo 'Array count: ' . count($rolesArray) . PHP_EOL;

// Now test the variadic parameter
echo PHP_EOL . "=== Testing Variadic Parameter Handling ===" . PHP_EOL;

// Simulate the middleware handle method
function testVariadicRoles(string ...$roles) {
    echo 'Received ' . count($roles) . ' role parameters:' . PHP_EOL;
    foreach ($roles as $index => $role) {
        echo '  [$' . $index . '] = ' . $role . PHP_EOL;
    }
    
    // Test hasAnyRole
    $bootstrap = require __DIR__ . '/bootstrap/app.php';
    $kernel = $bootstrap->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $user = \App\Models\User::find(2);
    $result = $user->hasAnyRole($roles);
    echo 'hasAnyRole(' . implode(',', $roles) . ') = ' . ($result ? 'TRUE' : 'FALSE') . PHP_EOL;
}

// Test with unpacked array
testVariadicRoles(...$rolesArray);
