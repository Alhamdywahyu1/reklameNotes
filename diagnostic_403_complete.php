<?php

/**
 * COMPREHENSIVE 403 DIAGNOSTIC TEST
 * 
 * This script tests every component of the role checking system
 * to identify exactly where the 403 Forbidden error is coming from.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "════════════════════════════════════════════════════════════════" . PHP_EOL;
echo "  COMPREHENSIVE 403 FORBIDDEN DIAGNOSTIC TEST" . PHP_EOL;
echo "════════════════════════════════════════════════════════════════" . PHP_EOL;

// ============================================================================
// TEST 1: Database - Roles Table
// ============================================================================
echo PHP_EOL . "[TEST 1] Database - Checking Roles Table" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

$roles = \App\Models\Role::all();
echo "✓ Roles found: " . $roles->count() . PHP_EOL;

if ($roles->count() === 0) {
    echo "✗ ERROR: No roles in database! This is the root cause." . PHP_EOL;
} else {
    foreach ($roles as $role) {
        echo "  - ID: {$role->id}, Slug: {$role->slug}, Name: {$role->name}" . PHP_EOL;
    }
}

// ============================================================================
// TEST 2: Database - Users Table
// ============================================================================
echo PHP_EOL . "[TEST 2] Database - Checking Users with Roles" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

$users = \App\Models\User::whereNotNull('role_id')->get();
echo "✓ Users with role_id: " . $users->count() . PHP_EOL;

if ($users->count() === 0) {
    echo "✗ ERROR: No users have assigned roles! Need to assign roles to users." . PHP_EOL;
} else {
    foreach ($users as $user) {
        $role = \App\Models\Role::find($user->role_id);
        echo "  - User ID: {$user->id}, Email: {$user->email}, Role: " . ($role?->slug ?? 'NULL') . PHP_EOL;
    }
}

// ============================================================================
// TEST 3: Operator User Detailed Check
// ============================================================================
echo PHP_EOL . "[TEST 3] Detailed Check - Operator User (ID: 2)" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

$operator = \App\Models\User::find(2);

if (!$operator) {
    echo "✗ ERROR: User ID 2 does not exist!" . PHP_EOL;
} else {
    echo "✓ User found: {$operator->email}" . PHP_EOL;
    echo "  - role_id: " . ($operator->role_id ?? 'NULL') . PHP_EOL;
    
    if (is_null($operator->role_id)) {
        echo "  ✗ ERROR: This user has NO role_id assigned! Cannot access staff pages." . PHP_EOL;
    } else {
        $role = $operator->role;
        if ($role) {
            echo "  ✓ Role loaded: {$role->name} (slug: {$role->slug})" . PHP_EOL;
        } else {
            echo "  ✗ ERROR: role_id={$operator->role_id} but role doesn't exist in database!" . PHP_EOL;
        }
    }
}

// ============================================================================
// TEST 4: hasAnyRole() Method
// ============================================================================
echo PHP_EOL . "[TEST 4] Testing hasAnyRole() Method" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

if ($operator) {
    $rolesRequired = ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'];
    echo "✓ Testing: hasAnyRole(['" . implode("', '", $rolesRequired) . "'])" . PHP_EOL;
    
    // Test with fresh load
    $operator = \App\Models\User::find(2);
    $operator->load('role');
    
    $result = $operator->hasAnyRole($rolesRequired);
    
    echo "  - Result: " . ($result ? "TRUE ✓" : "FALSE ✗") . PHP_EOL;
    echo "  - User role slug: " . ($operator->role?->slug ?? 'NULL') . PHP_EOL;
    
    if (!$result) {
        echo "  ✗ ERROR: User has wrong role slug or role is null!" . PHP_EOL;
    }
}

// ============================================================================
// TEST 5: Permohonan Data
// ============================================================================
echo PHP_EOL . "[TEST 5] Checking Permohonan Data" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

$permohonan = \App\Models\PermohonanReklame::first();

if (!$permohonan) {
    echo "✗ WARNING: No permohonan found in database!" . PHP_EOL;
    echo "  You need to create a permohonan first to test the check page." . PHP_EOL;
} else {
    echo "✓ Permohonan found:" . PHP_EOL;
    echo "  - ID: {$permohonan->id}" . PHP_EOL;
    echo "  - Nomor Registrasi: {$permohonan->nomor_registrasi}" . PHP_EOL;
    echo "  - Status: {$permohonan->status}" . PHP_EOL;
    echo "  - Test URL: /permohonan/{$permohonan->id}/requirements/check" . PHP_EOL;
}

// ============================================================================
// TEST 6: Middleware Logic Simulation
// ============================================================================
echo PHP_EOL . "[TEST 6] Simulating Middleware Logic" . PHP_EOL;
echo "──────────────────────────────────────────────────────────────" . PHP_EOL;

if ($operator) {
    echo "✓ Simulating: Route middleware('role:operator,kepala_seksi,kepala_bidang,admin')" . PHP_EOL;
    
    $user = \App\Models\User::find(2);
    $requiredRoles = ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'];
    
    // Step 1: Check authentication
    \Illuminate\Support\Facades\Auth::setUser($user);
    $isAuthenticated = auth()->check();
    echo "  [Step 1] auth()->check(): " . ($isAuthenticated ? "TRUE ✓" : "FALSE ✗") . PHP_EOL;
    
    if (!$isAuthenticated) {
        echo "  ✗ ERROR: User is not authenticated!" . PHP_EOL;
    } else {
        // Step 2: Load role
        $authUser = auth()->user();
        if (!$authUser->relationLoaded('role')) {
            $authUser->load('role');
        }
        echo "  [Step 2] Role loaded: " . ($authUser->role ? "YES ✓" : "NO ✗") . PHP_EOL;
        echo "  [Step 2] Role slug: " . ($authUser->role?->slug ?? 'NULL') . PHP_EOL;
        
        // Step 3: Check role
        $roleSlug = $authUser->role?->slug;
        $hasRole = in_array($roleSlug, $requiredRoles, true);
        echo "  [Step 3] in_array('{$roleSlug}', [...required roles...]): " . ($hasRole ? "TRUE ✓" : "FALSE ✗") . PHP_EOL;
        
        if ($hasRole) {
            echo "  ✓ MIDDLEWARE WOULD PASS" . PHP_EOL;
        } else {
            echo "  ✗ MIDDLEWARE WOULD RETURN 403" . PHP_EOL;
        }
    }
}

// ============================================================================
// SUMMARY
// ============================================================================
echo PHP_EOL . "════════════════════════════════════════════════════════════════" . PHP_EOL;
echo "  TEST SUMMARY" . PHP_EOL;
echo "════════════════════════════════════════════════════════════════" . PHP_EOL;

$issues = [];

if ($roles->count() === 0) {
    $issues[] = "No roles in database - need to seed roles";
}

if ($users->count() === 0) {
    $issues[] = "No users have assigned roles - need to assign roles to users";
}

if ($operator && is_null($operator->role_id)) {
    $issues[] = "Operator user (ID 2) has no role_id - need to assign role";
}

if ($operator && $operator->role && !in_array($operator->role->slug, ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
    $issues[] = "Operator from database has wrong role ({$operator->role->slug}) - should be 'operator'";
}

if (empty($issues)) {
    echo "✓ ALL TESTS PASSED!" . PHP_EOL;
    echo "The system is configured correctly. The 403 error must be coming" . PHP_EOL;
    echo "from somewhere else (check logs or session issues)." . PHP_EOL;
} else {
    echo "✗ ISSUES FOUND:" . PHP_EOL;
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". $issue" . PHP_EOL;
    }
}

echo PHP_EOL;
