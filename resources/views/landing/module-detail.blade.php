@extends('layouts.app')

@section('title', $module->name . ' — ' . ($settings['site_name'] ?? 'TrustedU ERP'))

@section('content')

{{-- Hero Banner --}}
<section class="relative pt-28 pb-16 overflow-hidden" style="background: linear-gradient(135deg, #0d1b3e 0%, #111827 40%, #1a1040 70%, #0d1b3e 100%);">
    <div class="absolute inset-0 opacity-8" style="background-image: radial-gradient(rgba(99,102,241,.4) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-blue-600/10 blur-[120px] orb-1"></div>
    <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] rounded-full bg-indigo-600/10 blur-[100px] orb-2"></div>

    @php
        $gradients = [
            'blue'=>['#2563eb','#3b82f6'],'indigo'=>['#4f46e5','#818cf8'],'purple'=>['#7c3aed','#a78bfa'],
            'red'=>['#dc2626','#f87171'],'orange'=>['#d97706','#fb923c'],'green'=>['#059669','#34d399'],
            'teal'=>['#0d9488','#5eead4'],'cyan'=>['#0284c7','#38bdf8'],
        ];
        $g = $gradients[$module->color] ?? $gradients['blue'];
    @endphp

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('home') }}#modules" class="hover:text-white transition-colors">Modules</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white font-medium">{{ $module->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row items-start gap-8">
            {{-- Icon --}}
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-2xl flex-shrink-0"
                 style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }})">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>

            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">{{ $module->name }}</h1>
                <p class="text-lg text-slate-400 max-w-3xl leading-relaxed">{{ $module->description }}</p>

                {{-- Feature badges --}}
                @if($module->features && is_array($module->features))
                <div class="flex flex-wrap gap-2 mt-6">
                    @foreach($module->features as $feat)
                    <span class="px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider border"
                          style="background: {{ $g[0] }}15; border-color: {{ $g[0] }}30; color: {{ $g[1] }}">
                        {{ $feat }}
                    </span>
                    @endforeach
                </div>
                @endif

                {{-- Video count badge --}}
                @if($videos->count() > 0)
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm text-white font-semibold">
                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                        <polygon fill="white" points="9.545,15.568 15.818,12 9.545,8.432"/>
                    </svg>
                    {{ $videos->count() }} Tutorial {{ $videos->count() === 1 ? 'Video' : 'Videos' }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="py-16 bg-slate-50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col lg:flex-row gap-10">

            {{-- Left: Video Library + Description --}}
            <div class="flex-1 min-w-0" x-data="{ activeVideo: '{{ $videos->first()['video_id'] ?? '' }}', activeTitle: '{{ addslashes($videos->first()['title'] ?? '') }}' }">

                {{-- Active Video Player --}}
                @if($videos->count() > 0)
                <div class="mb-10">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-slate-300/50 bg-black aspect-video">
                        <iframe
                            :src="'https://www.youtube.com/embed/' + activeVideo + '?rel=0&modestbranding=1'"
                            class="absolute inset-0 w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mt-5" x-text="activeTitle"></h3>
                </div>

                {{-- Video Playlist --}}
                <div class="mb-12">
                    <h2 class="text-lg font-extrabold text-slate-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                            <polygon fill="white" points="9.545,15.568 15.818,12 9.545,8.432"/>
                        </svg>
                        Video Tutorials ({{ $videos->count() }})
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($videos as $i => $video)
                        <button @click="activeVideo = '{{ $video['video_id'] }}'; activeTitle = '{{ addslashes($video['title'] ?? 'Untitled') }}'; window.scrollTo({top: 0, behavior: 'smooth'})"
                                class="group text-left rounded-xl border bg-white overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5"
                                :class="activeVideo === '{{ $video['video_id'] }}' ? 'border-blue-500 shadow-md shadow-blue-500/15 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-blue-300'">
                            {{-- Thumbnail --}}
                            <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                <img src="https://img.youtube.com/vi/{{ $video['video_id'] }}/mqdefault.jpg"
                                     alt="{{ $video['title'] ?? '' }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                                {{-- Play overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors">
                                    <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform"
                                         :class="activeVideo === '{{ $video['video_id'] }}' ? 'bg-blue-600' : 'bg-red-600'">
                                        <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <polygon points="8,5 19,12 8,19"/>
                                        </svg>
                                    </div>
                                </div>
                                {{-- Now Playing badge --}}
                                <div x-show="activeVideo === '{{ $video['video_id'] }}'"
                                     class="absolute top-2 left-2 px-2 py-1 rounded-md bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider">
                                    ▶ Now Playing
                                </div>
                            </div>
                            {{-- Info --}}
                            <div class="p-3">
                                <h4 class="text-[13px] font-bold text-slate-800 line-clamp-2 group-hover:text-blue-600 transition-colors leading-snug">
                                    {{ $video['title'] ?? 'Untitled Video' }}
                                </h4>
                                @if(!empty($video['description']))
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-1">{{ $video['description'] }}</p>
                                @endif
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @else
                {{-- No videos placeholder --}}
                <div class="text-center py-20 bg-white rounded-2xl border border-slate-200 mb-10">
                    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9A2.25 2.25 0 0 0 13.5 5.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Tutorial Videos Coming Soon</h3>
                    <p class="text-sm text-slate-500">Video tutorials for this module will be available shortly.</p>
                </div>
                @endif

                {{-- Long Description --}}
                @if($module->long_description)
                <div class="bg-white rounded-2xl border border-slate-200 p-8 mb-8">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        About This Module
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                        {!! $module->long_description !!}
                    </div>
                </div>
                @endif

                {{-- Features Detail Grid --}}
                @if($module->features && is_array($module->features) && count($module->features) > 0)
                <div class="bg-white rounded-2xl border border-slate-200 p-8">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Key Features & Sub-modules
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($module->features as $feat)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform"
                                 style="background: {{ $g[0] }}15">
                                <svg class="w-4 h-4" style="color: {{ $g[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-[13px] font-semibold text-slate-700 group-hover:text-blue-700 transition-colors">{{ $feat }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Sidebar: All Modules --}}
            <aside class="lg:w-80 flex-shrink-0">
                <div class="lg:sticky lg:top-24 space-y-6">

                    {{-- Module Navigation --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            All Modules
                        </h3>
                        <nav class="space-y-1 max-h-[60vh] overflow-y-auto pr-1">
                            @foreach($allModules as $m)
                            @php $isActive = $m->slug === $module->slug; @endphp
                            <a href="{{ route('module.show', $m->slug) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-blue-50 text-blue-700 border border-blue-200 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                @php $mg = $gradients[$m->color] ?? $gradients['blue']; @endphp
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isActive ? 'shadow-md' : '' }}"
                                     style="background: linear-gradient(135deg, {{ $mg[0] }}, {{ $mg[1] }})">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                                    </svg>
                                </div>
                                <span class="truncate">{{ $m->name }}</span>
                                @if($m->youtube_videos && count($m->youtube_videos) > 0)
                                <span class="ml-auto px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-500 border border-red-100 flex-shrink-0">
                                    {{ count($m->youtube_videos) }} 📹
                                </span>
                                @endif
                            </a>
                            @endforeach
                        </nav>
                    </div>

                    {{-- CTA Card --}}
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-center shadow-xl shadow-blue-500/20">
                        <div class="text-3xl mb-3">🎯</div>
                        <h3 class="text-lg font-bold text-white mb-2">Want a Live Demo?</h3>
                        <p class="text-blue-100 text-sm mb-5">See {{ $module->name }} in action with a personalized demonstration.</p>
                        <a href="{{ route('demo.form') }}"
                           class="inline-flex items-center gap-2 w-full justify-center px-5 py-3 bg-white text-blue-700 font-bold text-sm rounded-xl hover:bg-blue-50 transition-colors group">
                            Book a Demo
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </aside>
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
</script>
@endpush
