<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->handleRequest(\Illuminate\Http\Request::capture());

echo "Messages: " . \App\Models\ContactMessage::count() . PHP_EOL;
echo "Leads: " . \App\Models\Lead::count() . PHP_EOL;
echo "Deals: " . \App\Models\Deal::count() . PHP_EOL;
echo "Clients: " . \App\Models\Client::count() . PHP_EOL;
