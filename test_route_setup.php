<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$user = \App\Models\User::find(2);
echo 'Testing operator user: ' . $user->email . PHP_EOL;
echo 'Has role operator: ' . ($user->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']) ? 'YES' : 'NO') . PHP_EOL;

$permohonan = \App\Models\PermohonanReklame::first();
if ($permohonan) {
    echo 'Found permohonan: ' . $permohonan->nomor_registrasi . ' (ID: ' . $permohonan->id . ')' . PHP_EOL;
    echo 'Testing URL: /permohonan/' . $permohonan->id . '/requirements/check' . PHP_EOL;
} else {
    echo 'No permohonan found in database!' . PHP_EOL;
}
