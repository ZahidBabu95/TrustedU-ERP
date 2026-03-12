<?php
$dirs = [
    __DIR__ . '/app/Filament/Resources/*/Schemas/*.php',
    __DIR__ . '/app/Filament/Resources/*Resource.php',
];

$schemaComponents = [
    'Section', 'Grid', 'Group', 'Fieldset', 'Tabs', 'Wizard', 'Actions', 'Component',
    'Text', 'Image', 'Icon', 'Livewire', 'RenderHook', 'EmptyState', 'Html', 'Callout', 'View',
    'EmbeddedSchema', 'EmbeddedTable', 'FusedGroup', 'UnorderedList', 'Flex', 'Form'
];

foreach ($dirs as $pattern) {
    if (strpos($pattern, '*') !== false) {
        $files = glob($pattern);
    } else {
        $files = [$pattern];
    }
    
    foreach ($files as $f) {
        $content = file_get_contents($f);
        
        // Match `use Filament\Schemas\Components\([a-zA-Z0-9_]+);` or `use Filament\Forms\Components\([a-zA-Z0-9_]+);`
        $content = preg_replace_callback('/use Filament\\\\(?:Schemas|Forms)\\\\Components\\\\([a-zA-Z0-9_]+);/', function($matches) use ($schemaComponents) {
            $class = $matches[1];
            if (in_array($class, $schemaComponents)) {
                return "use Filament\\Schemas\\Components\\$class;";
            } else {
                return "use Filament\\Forms\\Components\\$class;";
            }
        }, $content);
        
        file_put_contents($f, $content);
    }
}
echo "Done fixing use statements";
