<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    if (isset($u->role)) {
        echo $u->email . ' - ' . $u->role . "\n";
    } else {
        echo $u->email . ' - (no role field?)' . "\n";
    }
}
