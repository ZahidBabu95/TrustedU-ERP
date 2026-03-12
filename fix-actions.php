<?php
$files = glob(__DIR__ . '/app/Filament/Resources/*/Tables/*.php');
foreach ($files as $f) {
    $content = file_get_contents($f);

    // First fix any previous mistakes
    $content = str_replace([
        'use Filament\\Tables\\Actions\\', 
        'use Filament\\\\Actions\\\\',
        'use Filament\\\\Tables\\\\Actions\\\\'
    ], 'use Filament\\Actions\\', $content);

    // Then update the method names!
    $content = str_replace('->actions([', '->recordActions([', $content);
    $content = str_replace('->bulkActions([', '->toolbarActions([', $content);

    file_put_contents($f, $content);
}
echo "Done replacing use statements for tables";
