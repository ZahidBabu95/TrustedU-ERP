<?php

namespace App\Http\Controllers;

use App\Models\HeroSection;
use App\Models\SiteSection;
use App\Models\ErpModule;
use App\Models\Testimonial;
use App\Models\Client;
use App\Models\Partner;
use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $hero        = HeroSection::active()->ordered()->first();
        $modules     = ErpModule::active()->ordered()->get();
        $testimonials = Testimonial::active()->ordered()->get();
        $clients     = Client::live()->ordered()->get();
        $partners    = Partner::active()->ordered()->get();
        $blogPosts   = BlogPost::published()->latest()->take(3)->with('category', 'author')->get();
        $about       = SiteSection::getByKey('about');
        $whyUs       = SiteSection::getByKey('why_us');
        $stats       = SiteSection::getByKey('stats');
        
        // Settings for dynamic branding and footer
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('landing.index', compact(
            'hero', 'modules', 'testimonials', 'clients',
            'partners', 'blogPosts', 'about', 'whyUs', 'stats', 'settings'
        ));
    }

    public function showModule(string $slug): View
    {
        $module = ErpModule::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Parse YouTube video IDs from URLs
        $videos = collect($module->youtube_videos ?? [])->map(function ($video) {
            $video['video_id'] = $this->extractYouTubeId($video['url'] ?? '');
            return $video;
        })->filter(fn($v) => !empty($v['video_id']));

        // Extract featured video (first one marked as featured, or first video)
        $featuredVideo = $videos->firstWhere('is_featured', true) ?? $videos->first();

        // Remaining videos (exclude featured from the list to avoid duplication)
        $otherVideos = $videos->filter(function ($v) use ($featuredVideo) {
            if (!$featuredVideo) return true;
            return $v['video_id'] !== $featuredVideo['video_id'];
        });

        // All other active modules for sidebar navigation
        $allModules = ErpModule::active()->ordered()->get();

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('landing.module-detail', compact(
            'module', 'videos', 'featuredVideo', 'otherVideos', 'allModules', 'settings'
        ));
    }

    /**
     * Extract YouTube video ID from various URL formats.
     */
    private function extractYouTubeId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
            '/^([a-zA-Z0-9_-]{11})$/', // raw ID
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
