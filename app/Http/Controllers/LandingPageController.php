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
        $clients     = Client::active()->ordered()->get();
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
}
