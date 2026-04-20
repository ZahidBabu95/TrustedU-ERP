<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$module = App\Models\ErpModule::first();
echo json_encode($module->dynamic_sections, JSON_PRETTY_PRINT);
