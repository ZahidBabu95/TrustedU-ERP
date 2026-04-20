@extends('layouts.app')

@section('title', $module->name . ' — ' . ($settings['site_name'] ?? 'TrustedU ERP'))

@push('styles')
<style>
    /* ── Module Landing Page Styles ────────────────────────── */

    /* Animated gradient border */
    @keyframes gradient-border {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Floating particles */
    @keyframes particle-drift {
        0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.3; }
        25% { transform: translateY(-20px) translateX(10px) rotate(90deg); opacity: 0.6; }
        50% { transform: translateY(-35px) translateX(-5px) rotate(180deg); opacity: 0.4; }
        75% { transform: translateY(-15px) translateX(15px) rotate(270deg); opacity: 0.7; }
    }

    @keyframes slide-up-fade {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes scale-in {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 12px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    @keyframes video-glow {
        0%, 100% { box-shadow: 0 0 30px rgba(239, 68, 68, 0.15), 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        50% { box-shadow: 0 0 50px rgba(239, 68, 68, 0.25), 0 25px 50px -12px rgba(0, 0, 0, 0.35); }
    }

    .slide-up-1 { animation: slide-up-fade 0.7s ease-out 0.1s both; }
    .slide-up-2 { animation: slide-up-fade 0.7s ease-out 0.25s both; }
    .slide-up-3 { animation: slide-up-fade 0.7s ease-out 0.4s both; }
    .slide-up-4 { animation: slide-up-fade 0.7s ease-out 0.55s both; }
    .scale-in-1 { animation: scale-in 0.6s ease-out 0.3s both; }

    .featured-video-glow { animation: video-glow 3s ease-in-out infinite; }

    .shimmer-bg {
        background: linear-gradient(90deg, transparent 33%, rgba(255,255,255,0.1) 50%, transparent 66%);
        background-size: 200% 100%;
        animation: shimmer 2.5s infinite;
    }

    .feature-card {
        transition: all 0.4s cubic-bezier(.22,.68,0,1.2);
    }
    .feature-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .video-card {
        transition: all 0.3s cubic-bezier(.22,.68,0,1.2);
    }
    .video-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
    }

    /* Section dividers */
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.2), transparent);
    }

    /* Scroll-triggered parallax for hero image */
    .hero-image-wrapper {
        perspective: 1000px;
    }
    .hero-image-wrapper img {
        transition: transform 0.3s ease;
    }
    .hero-image-wrapper:hover img {
        transform: rotateY(-2deg) rotateX(2deg) scale(1.02);
    }

    /* Download button shine effect */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(to right, transparent 45%, rgba(255,255,255,0.15) 50%, transparent 55%);
        transform: rotate(25deg);
        animation: shimmer 3s infinite;
    }
</style>
@endpush

@section('content')

@php
    $gradients = [
        'blue'=>['#2563eb','#3b82f6','#1d4ed8'],'indigo'=>['#4f46e5','#818cf8','#3730a3'],
        'purple'=>['#7c3aed','#a78bfa','#6d28d9'],'red'=>['#dc2626','#f87171','#b91c1c'],
        'orange'=>['#d97706','#fb923c','#b45309'],'green'=>['#059669','#34d399','#047857'],
        'teal'=>['#0d9488','#5eead4','#0f766e'],'cyan'=>['#0284c7','#38bdf8','#0369a1'],
        'pink'=>['#db2777','#f472b6','#be185d'],'yellow'=>['#ca8a04','#facc15','#a16207'],
    ];
    $g = $gradients[$module->color] ?? $gradients['blue'];
    $hasDynamicSections = !empty($module->dynamic_sections) && is_array($module->dynamic_sections) && count($module->dynamic_sections) > 0;
@endphp

@if($hasDynamicSections)
    @include('landing.partials.module-dynamic-blocks')
@else
{{-- ═══════════════════════════════════════════════════════════
     SECTION 1: HERO
     ═══════════════════════════════════════════════════════════ --}}
<section class="relative pt-28 pb-20 lg:pb-28 overflow-hidden min-h-[85vh] flex items-center"
         style="background: linear-gradient(135deg, #0d1b3e 0%, #111827 30%, #1a1040 60%, #0d1b3e 100%);">

    {{-- Background effects --}}
    <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(rgba(99,102,241,.5) 1px, transparent 1px); background-size: 32px 32px;"></div>
    <div class="absolute -top-40 -right-40 w-[700px] h-[700px] rounded-full blur-[150px] orb-1" style="background: {{ $g[0] }}15;"></div>
    <div class="absolute -bottom-60 -left-40 w-[600px] h-[600px] rounded-full blur-[130px] orb-2" style="background: {{ $g[1] }}12;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full blur-[200px] orb-3" style="background: {{ $g[0] }}08;"></div>

    {{-- Floating particles --}}
    <div class="absolute top-20 left-[15%] w-2 h-2 rounded-full" style="background: {{ $g[1] }}; animation: particle-drift 8s ease-in-out infinite;"></div>
    <div class="absolute top-40 right-[20%] w-1.5 h-1.5 rounded-full" style="background: {{ $g[1] }}; animation: particle-drift 10s ease-in-out infinite 1s;"></div>
    <div class="absolute bottom-32 left-[25%] w-1 h-1 rounded-full" style="background: {{ $g[1] }}; animation: particle-drift 12s ease-in-out infinite 2s;"></div>
    <div class="absolute top-60 left-[60%] w-2.5 h-2.5 rounded-full" style="background: {{ $g[0] }}; animation: particle-drift 7s ease-in-out infinite 0.5s;"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 w-full">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-10 slide-up-1">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors duration-300 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>
            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('home') }}#modules" class="hover:text-white transition-colors duration-300">Modules</a>
            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white font-semibold">{{ $module->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
            {{-- Left: Text Content --}}
            <div class="flex-1 max-w-2xl">
                {{-- Module badge --}}
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full border mb-6 slide-up-1"
                     style="background: {{ $g[0] }}12; border-color: {{ $g[0] }}30;">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }})">
                        @if($module->icon_image)
                            <img src="{{ asset('storage/' . $module->icon_image) }}" alt="icon" class="w-4 h-4 object-contain filter brightness-0 invert">
                        @else
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                            </svg>
                        @endif
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: {{ $g[1] }}">ERP Module</span>
                </div>

                {{-- Title --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-5 leading-[1.1] slide-up-2">
                    {{ $module->name }}
                </h1>

                {{-- Subtitle / Tagline --}}
                @if($module->hero_subtitle)
                <p class="text-xl sm:text-2xl font-medium mb-5 leading-relaxed slide-up-2" style="color: {{ $g[1] }}">
                    {{ $module->hero_subtitle }}
                </p>
                @endif

                {{-- Description --}}
                <p class="text-base sm:text-lg text-slate-400 leading-relaxed mb-8 slide-up-3">
                    {{ $module->description }}
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap items-center gap-4 mb-8 slide-up-4">
                    @if($module->download_url)
                    <a href="{{ $module->download_url }}" target="_blank"
                       class="btn-shine inline-flex items-center gap-3 px-7 py-3.5 rounded-xl font-bold text-white text-sm shadow-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl"
                       style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }}); box-shadow: 0 10px 40px {{ $g[0] }}40;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ $module->download_label ?? 'Download Now' }}
                    </a>
                    @endif

                    <a href="{{ route('demo.form') }}"
                       class="inline-flex items-center gap-3 px-7 py-3.5 rounded-xl font-bold text-sm border transition-all duration-300 hover:scale-105 group"
                       style="color: {{ $g[1] }}; border-color: {{ $g[0] }}40; background: {{ $g[0] }}10;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Book a Live Demo
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                {{-- Stats / Meta --}}
                <div class="flex flex-wrap items-center gap-5 slide-up-4">
                    @if($module->features && is_array($module->features))
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-white">{{ count($module->features) }}</span> Features
                    </div>
                    @endif

                    @if($videos->count() > 0)
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5">
                            <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                                <polygon fill="white" points="9.545,15.568 15.818,12 9.545,8.432"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-white">{{ $videos->count() }}</span> Tutorials
                    </div>
                    @endif

                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white/5">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        Army Authorized
                    </div>
                </div>
            </div>

            {{-- Right: Hero Image / Featured Video --}}
            <div class="flex-1 max-w-xl w-full scale-in-1">
                @if($featuredVideo)
                {{-- Featured Video Player --}}
                <div class="relative rounded-2xl overflow-hidden featured-video-glow" x-data="{ playing: false }">
                    {{-- Thumbnail state --}}
                    <div x-show="!playing" class="relative aspect-video bg-slate-900 cursor-pointer group" @click="playing = true">
                        <img src="https://img.youtube.com/vi/{{ $featuredVideo['video_id'] }}/maxresdefault.jpg"
                             alt="{{ $featuredVideo['title'] ?? $module->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                             onerror="this.src='https://img.youtube.com/vi/{{ $featuredVideo['video_id'] }}/hqdefault.jpg'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

                        {{-- Play button --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative">
                                <div class="w-20 h-20 rounded-full flex items-center justify-center bg-red-600/90 backdrop-blur-sm group-hover:bg-red-600 group-hover:scale-110 transition-all duration-300 shadow-2xl shadow-red-600/30"
                                     style="animation: pulse-ring 2s infinite;">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <polygon points="8,5 19,12 8,19"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Featured badge --}}
                        <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/15 backdrop-blur-md border border-white/20 text-white text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 soft-pulse"></span>
                            ⭐ Featured Video
                        </div>

                        {{-- Video title --}}
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-white font-bold text-lg leading-snug">{{ $featuredVideo['title'] ?? 'Watch Tutorial' }}</h3>
                            @if(!empty($featuredVideo['description']))
                            <p class="text-white/70 text-sm mt-1 line-clamp-1">{{ $featuredVideo['description'] }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- YouTube iframe (when playing) --}}
                    <div x-show="playing" class="aspect-video bg-black">
                        <template x-if="playing">
                            <iframe
                                :src="'https://www.youtube.com/embed/{{ $featuredVideo['video_id'] }}?autoplay=1&rel=0&modestbranding=1'"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>
                        </template>
                    </div>
                </div>
                @elseif($module->hero_image)
                {{-- Hero Image --}}
                <div class="hero-image-wrapper">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/30 border border-white/10">
                        <img src="{{ asset('storage/' . $module->hero_image) }}"
                             alt="{{ $module->name }}"
                             class="w-full h-auto object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>
                </div>
                @else
                {{-- Default illustration --}}
                <div class="relative aspect-square max-w-md mx-auto">
                    <div class="absolute inset-0 rounded-3xl" style="background: linear-gradient(135deg, {{ $g[0] }}20, {{ $g[1] }}10); border: 1px solid {{ $g[0] }}20;"></div>
                    <div class="absolute inset-8 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, {{ $g[0] }}15, transparent);">
                        <div class="text-center">
                            <div class="w-24 h-24 rounded-2xl mx-auto mb-4 flex items-center justify-center overflow-hidden shadow-lg" style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }});">
                                @if($module->icon_image)
                                    <img src="{{ asset('storage/' . $module->icon_image) }}" alt="icon" class="w-14 h-14 object-contain filter brightness-0 invert">
                                @else
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <p class="text-lg font-bold text-white/60">{{ $module->name }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom gradient fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-slate-50 to-transparent"></div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     SECTION 2: FEATURE BADGES (Horizontal Scroll)
     ═══════════════════════════════════════════════════════════ --}}
@if($module->features && is_array($module->features) && count($module->features) > 0)
<section class="py-6 bg-slate-50 border-b border-slate-200 -mt-1 relative z-10">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-hide">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest shrink-0 mr-2">Features:</span>
            @foreach($module->features as $feat)
            <span class="shrink-0 px-4 py-2 rounded-full text-xs font-bold border transition-all duration-300 hover:scale-105 cursor-default"
                  style="background: {{ $g[0] }}08; border-color: {{ $g[0] }}20; color: {{ $g[0] }};">
                {{ $feat }}
            </span>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════════
     SECTION 3: KEY FEATURES (Beautiful Card Grid)
     ═══════════════════════════════════════════════════════════ --}}
@if($module->features && is_array($module->features) && count($module->features) > 0)
<section class="py-20 bg-white" id="features">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        {{-- Section Header --}}
        <div class="text-center mb-16" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-widest mb-5">
                <svg class="w-4 h-4" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Key Features
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-4">
                Everything You Need in <span style="color: {{ $g[0] }}">{{ $module->name }}</span>
            </h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">
                Powerful sub-modules and features designed for seamless education management.
            </p>
        </div>

        {{-- Feature Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($module->features as $i => $feat)
            <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 group hover:border-transparent relative overflow-hidden"
                 data-aos="fade-up" data-aos-delay="{{ ($i % 6) * 80 }}">
                {{-- Gradient overlay on hover --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"
                     style="background: linear-gradient(135deg, {{ $g[0] }}05, {{ $g[1] }}08);"></div>

                <div class="relative z-10">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300"
                         style="background: linear-gradient(135deg, {{ $g[0] }}15, {{ $g[1] }}10);">
                        <svg class="w-6 h-6" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Feature Name --}}
                    <h3 class="text-base font-bold text-slate-800 group-hover:text-slate-900 transition-colors">{{ $feat }}</h3>

                    {{-- Decorative arrow --}}
                    <div class="mt-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-[-10px] group-hover:translate-x-0">
                        <span class="text-xs font-semibold" style="color: {{ $g[0] }}">Learn more →</span>
                    </div>
                </div>

                {{-- Corner decoration --}}
                <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                     style="background: {{ $g[0] }}06;"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════════
     SECTION 4: ABOUT / DETAILED DESCRIPTION
     ═══════════════════════════════════════════════════════════ --}}
@if($module->long_description)
<section class="py-20 bg-slate-50" id="about">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col lg:flex-row gap-12 items-start">
            {{-- Left: Section info --}}
            <div class="lg:w-1/3 lg:sticky lg:top-28">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-widest mb-5">
                    <svg class="w-4 h-4" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    About This Module
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 mb-4">
                    Detailed Overview
                </h2>
                <p class="text-slate-500 leading-relaxed mb-6">
                    Learn everything about {{ $module->name }} and how it can transform your institution's management.
                </p>

                {{-- Quick links --}}
                <div class="space-y-3">
                    @if($module->download_url)
                    <a href="{{ $module->download_url }}" target="_blank"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: {{ $g[0] }}15;">
                            <svg class="w-4.5 h-4.5" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">{{ $module->download_label ?? 'Download Now' }}</span>
                        <svg class="w-4 h-4 ml-auto text-slate-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endif
                    <a href="{{ route('demo.form') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all group">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-blue-50">
                            <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">Book a Live Demo</span>
                        <svg class="w-4 h-4 ml-auto text-slate-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Right: Long Description --}}
            <div class="lg:w-2/3">
                <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-10 shadow-sm" data-aos="fade-up">
                    <div class="prose prose-slate max-w-none prose-headings:font-extrabold prose-h2:text-2xl prose-h3:text-xl prose-p:leading-relaxed prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline prose-li:text-slate-600 prose-strong:text-slate-800">
                        {!! $module->long_description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════════
     SECTION 5: VIDEO TUTORIALS LIBRARY
     ═══════════════════════════════════════════════════════════ --}}
@if($videos->count() > 0)
<section class="py-20 bg-white" id="tutorials"
         x-data="{
             activeVideo: '{{ $featuredVideo['video_id'] ?? $videos->first()['video_id'] ?? '' }}',
             activeTitle: '{{ addslashes($featuredVideo['title'] ?? $videos->first()['title'] ?? '') }}',
             activeDesc: '{{ addslashes($featuredVideo['description'] ?? $videos->first()['description'] ?? '') }}',
             isPlaying: false
         }">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        {{-- Section Header --}}
        <div class="text-center mb-14" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-600 text-xs font-bold uppercase tracking-widest mb-5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                    <polygon fill="white" points="9.545,15.568 15.818,12 9.545,8.432"/>
                </svg>
                Video Tutorials
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-4">
                Learn <span style="color: {{ $g[0] }}">{{ $module->name }}</span> Step by Step
            </h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">
                Watch our comprehensive video tutorials to master every feature of this module.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Left: Main Video Player --}}
            <div class="flex-1 min-w-0">
                {{-- Video Player --}}
                <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-slate-300/50 bg-black aspect-video mb-5" data-aos="fade-up">
                    <iframe
                        :src="'https://www.youtube.com/embed/' + activeVideo + '?rel=0&modestbranding=1'"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>

                {{-- Now Playing Info --}}
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $g[0] }}15;">
                            <svg class="w-5 h-5" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Now Playing</p>
                            <h3 class="text-lg font-bold text-slate-900" x-text="activeTitle"></h3>
                            <p class="text-sm text-slate-500 mt-1" x-text="activeDesc" x-show="activeDesc"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Playlist --}}
            <div class="lg:w-96 flex-shrink-0">
                <div class="lg:sticky lg:top-24">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                                <polygon fill="white" points="9.545,15.568 15.818,12 9.545,8.432"/>
                            </svg>
                            Playlist
                        </h3>
                        <span class="text-xs font-semibold text-slate-400 px-2 py-1 rounded-lg bg-slate-100">{{ $videos->count() }} videos</span>
                    </div>

                    <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-1 scrollbar-thin">
                        @foreach($videos as $i => $video)
                        <button @click="activeVideo = '{{ $video['video_id'] }}'; activeTitle = '{{ addslashes($video['title'] ?? 'Untitled') }}'; activeDesc = '{{ addslashes($video['description'] ?? '') }}'; document.getElementById('tutorials').scrollIntoView({behavior: 'smooth', block: 'start'})"
                                class="video-card w-full text-left rounded-xl border bg-white overflow-hidden transition-all duration-300"
                                :class="activeVideo === '{{ $video['video_id'] }}' ? 'border-blue-500 shadow-lg shadow-blue-500/10 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex gap-3 p-3">
                                {{-- Thumbnail --}}
                                <div class="relative w-32 flex-shrink-0 aspect-video rounded-lg overflow-hidden bg-slate-100">
                                    <img src="https://img.youtube.com/vi/{{ $video['video_id'] }}/mqdefault.jpg"
                                         alt="{{ $video['title'] ?? '' }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                    {{-- Play overlay --}}
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all"
                                             :class="activeVideo === '{{ $video['video_id'] }}' ? 'bg-blue-600 scale-110' : 'bg-red-600/90'">
                                            <svg class="w-3.5 h-3.5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                <polygon points="8,5 19,12 8,19"/>
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- Now Playing --}}
                                    <div x-show="activeVideo === '{{ $video['video_id'] }}'"
                                         class="absolute top-1 left-1 px-1.5 py-0.5 rounded bg-blue-600 text-white text-[8px] font-bold uppercase tracking-wider">
                                        ▶ Playing
                                    </div>
                                    {{-- Featured badge --}}
                                    @if(!empty($video['is_featured']))
                                    <div class="absolute top-1 right-1 px-1.5 py-0.5 rounded bg-amber-500 text-white text-[8px] font-bold">
                                        ⭐
                                    </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0 py-0.5">
                                    <h4 class="text-[13px] font-bold text-slate-800 line-clamp-2 leading-snug transition-colors"
                                        :class="activeVideo === '{{ $video['video_id'] }}' ? 'text-blue-700' : ''">
                                        {{ $video['title'] ?? 'Untitled Video' }}
                                    </h4>
                                    @if(!empty($video['description']))
                                    <p class="text-[11px] text-slate-500 mt-1.5 line-clamp-2">{{ $video['description'] }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                                              :class="activeVideo === '{{ $video['video_id'] }}' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500'">
                                            #{{ $i + 1 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif


</section>
@endif
{{-- @endif closes the legacy layout condition --}}

{{-- ═══════════════════════════════════════════════════════════
     SECTION 6: OTHER MODULES (Explore More)
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50" id="other-modules">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="text-center mb-14" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-widest mb-5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                </svg>
                Explore More
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-4">Other ERP Modules</h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">Discover the complete suite of modules in TrustedU ERP.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($allModules->where('slug', '!=', $module->slug)->take(8) as $m)
            @php $mg = $gradients[$m->color] ?? $gradients['blue']; @endphp
            <a href="{{ route('module.show', $m->slug) }}"
               class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-transparent hover:shadow-xl transition-all duration-400 relative overflow-hidden"
               data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                {{-- Hover gradient --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"
                     style="background: linear-gradient(135deg, {{ $mg[0] }}08, {{ $mg[1] }}05);"></div>

                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 overflow-hidden shadow-md"
                         style="background: linear-gradient(135deg, {{ $mg[0] }}, {{ $mg[1] }});">
                        @if($m->icon_image)
                            <img src="{{ asset('storage/' . $m->icon_image) }}" alt="icon" class="w-7 h-7 object-contain filter brightness-0 invert">
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 group-hover:text-slate-900 mb-2">{{ $m->name }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ Str::limit($m->description, 80) }}</p>

                    @if($m->youtube_videos && count($m->youtube_videos) > 0)
                    <div class="mt-3 flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-red-500 px-2 py-0.5 rounded-full bg-red-50 border border-red-100">
                            📹 {{ count($m->youtube_videos) }} Videos
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Arrow --}}
                <div class="absolute bottom-4 right-4 w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 group-hover:bg-white opacity-0 group-hover:opacity-100 transition-all duration-300"
                     style="color: {{ $mg[0] }};">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     SECTION 7: BOTTOM CTA
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0c1a3e 70%, #0f172a 100%);">
    <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(rgba(99,102,241,.5) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] rounded-full blur-[120px] orb-1" style="background: {{ $g[0] }}10;"></div>
    <div class="absolute -bottom-40 -left-40 w-[400px] h-[400px] rounded-full blur-[100px] orb-2" style="background: {{ $g[1] }}08;"></div>

    <div class="relative max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 text-center" data-aos="fade-up">
        <div class="text-5xl mb-6">🎯</div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-5">
            Ready to Transform Your Institution?
        </h2>
        <p class="text-lg text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            Experience {{ $module->name }} in action with a personalized live demo. Our team will show you exactly how it works for your institution.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            @if($module->download_url)
            <a href="{{ $module->download_url }}" target="_blank"
               class="btn-shine inline-flex items-center gap-3 px-8 py-4 rounded-xl font-bold text-white text-sm shadow-xl transition-all duration-300 hover:scale-105"
               style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }}); box-shadow: 0 10px 40px {{ $g[0] }}40;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ $module->download_label ?? 'Download Now' }}
            </a>
            @endif

            <a href="{{ route('demo.form') }}"
               class="btn-glow inline-flex items-center gap-3 px-8 py-4 rounded-xl font-bold text-white text-sm bg-blue-600 hover:bg-blue-700 transition-all duration-300 hover:scale-105 group">
                Book a Live Demo
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // GA4: Track module detail page view
    if (typeof gtag === 'function') {
        gtag('event', 'module_detail_view', {
            event_category: 'content',
            module_name: @json($module->name),
            module_slug: @json($module->slug),
            video_count: {{ $videos->count() }}
        });
    }

    // Smooth reveal on scroll for feature cards
    const featureCards = document.querySelectorAll('.feature-card');
    const cardObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    featureCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        cardObserver.observe(card);
    });
</script>
@endpush
