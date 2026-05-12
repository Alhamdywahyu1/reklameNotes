<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel');

use App\Models\User;

$user = User::where('name', 'like', '%Doni%')->first();
if ($user) {
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role ID: " . $user->role_id . "\n";
    echo "Role: " . ($user->role ? $user->role->name . ' (' . $user->role->slug . ')' : 'NULL') . "\n";
} else {
    echo "Doni not found\n";
}
?>
