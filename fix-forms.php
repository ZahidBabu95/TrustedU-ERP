<?php
$dirs = [
    __DIR__ . '/app/Filament/Resources/*/Schemas/*.php',
    __DIR__ . '/app/Filament/Resources/*Resource.php',
];

foreach ($dirs as $pattern) {
    $files = glob($pattern);
    foreach ($files as $f) {
        $content = file_get_contents($f);
        
        // Filament v5 uses Schemas instead of Forms
        $content = str_replace('use Filament\\Forms\\Components\\', 'use Filament\\Schemas\\Components\\', $content);
        
        file_put_contents($f, $content);
    }
}
echo "Done replacing Forms\Components with Schemas\Components";
