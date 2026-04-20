<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ErpModule;

$modules = ErpModule::all();
$count = 0;

foreach ($modules as $module) {
    if (!empty($module->dynamic_sections)) {
        continue; // Already has builder data
    }

    $blocks = [];

    // 1. Hero Block
    $heroButtons = [];
    if (!empty($module->download_label) && !empty($module->download_url)) {
        $heroButtons[] = [
            'label' => $module->download_label,
            'url' => $module->download_url,
            'style' => 'primary'
        ];
    }
    
    $blocks[] = [
        'type' => 'hero',
        'data' => [
            'title' => $module->name,
            'subtitle' => $module->hero_subtitle ?? $module->description ?? '',
            'image' => $module->hero_image,
            'buttons' => $heroButtons
        ]
    ];

    // 2. Features Grid Block
    if (!empty($module->features) && is_array($module->features)) {
        $featureItems = array_map(function($feat) {
            return [
                'title' => $feat,
                'description' => 'Details about ' . $feat,
                'icon' => 'heroicon-o-check-circle'
            ];
        }, $module->features);

        $blocks[] = [
            'type' => 'features_grid',
            'data' => [
                'section_title' => 'Key Features',
                'section_subtitle' => 'Everything you need in ' . $module->name,
                'features' => $featureItems
            ]
        ];
    }

    // 3. Video Playlist
    if (!empty($module->youtube_videos) && is_array($module->youtube_videos)) {
        $videoItems = array_map(function($vid) {
            // extract video id
            $url = $vid['url'] ?? '';
            $videoId = '';
            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
            if(isset($vars['v'])) {
                $videoId = $vars['v'];
            } else {
                $parts = explode('/', rtrim($url, '/'));
                $videoId = end($parts);
            }
            
            return [
                'title' => $vid['title'] ?? 'Video',
                'youtube_id' => $videoId,
                'description' => $vid['description'] ?? '',
                'is_featured' => $vid['is_featured'] ?? false
            ];
        }, $module->youtube_videos);

        $blocks[] = [
            'type' => 'video_playlist',
            'data' => [
                'section_title' => 'Video Tutorials',
                'videos' => $videoItems
            ]
        ];
    }

    // 4. CTA Banner
    $blocks[] = [
        'type' => 'cta_banner',
        'data' => [
            'title' => 'Ready to Transform Your Institution?',
            'subtitle' => 'Experience ' . $module->name . ' in action with a personalized live demo.',
            'buttons' => [
                [
                    'label' => 'Book a Live Demo',
                    'url' => '/book-demo',
                ]
            ]
        ]
    ];

    $module->dynamic_sections = $blocks;
    $module->save();
    $count++;
}

echo "Migrated $count modules to the new dynamic builder engine.\n";
