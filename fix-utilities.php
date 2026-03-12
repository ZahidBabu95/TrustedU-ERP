<?php
$dirs = [
    __DIR__ . '/app/Filament/Resources/*/Schemas/*.php',
    __DIR__ . '/app/Filament/Resources/*Resource.php',
];

foreach ($dirs as $pattern) {
    if (strpos($pattern, '*') !== false) {
        $files = glob($pattern);
    } else {
        $files = [$pattern];
    }
    
    foreach ($files as $f) {
        $content = file_get_contents($f);
        
        $content = str_replace('use Filament\Forms\Set;', 'use Filament\Schemas\Components\Utilities\Set;', $content);
        $content = str_replace('use Filament\Forms\Get;', 'use Filament\Schemas\Components\Utilities\Get;', $content);
        
        file_put_contents($f, $content);
    }
}
echo "Done fixing utilities";
