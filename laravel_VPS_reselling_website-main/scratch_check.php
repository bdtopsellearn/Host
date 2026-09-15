<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Null users: " . App\Models\User::whereNull('name')->count() . "\n";
echo "Null packages: " . App\Models\Package::whereNull('name')->count() . "\n";
