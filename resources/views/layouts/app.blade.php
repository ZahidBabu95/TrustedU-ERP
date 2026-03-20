<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TrustedU ERP - Bangladesh Army Authorized Education Platform')</title>
    <meta name="description" content="TrustedU ERP is an authorized education management system by Bangladesh Army, unifying 63 Cantonment Public Schools & Colleges under a centralized digital platform.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- CDN Tailwind + AlpineJS + AOS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --brand-light: #60a5fa;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background: #ffffff;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── Animated gradient text ─────────────────────────── */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50%       { background-position: 100% 50%; }
        }
        .animate-gradient-text {
            background-size: 200% auto;
            animation: gradient-shift 6s ease infinite;
        }

        /* ── Floating orbs ──────────────────────────────────── */
        @keyframes orb-float {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.18; }
            50%       { transform: translateY(-30px) scale(1.08); opacity: 0.28; }
        }
        @keyframes orb-float-r {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.15; }
            50%       { transform: translateY(20px) scale(1.05); opacity: 0.22; }
        }
        .orb-1 { animation: orb-float   18s ease-in-out infinite; }
        .orb-2 { animation: orb-float-r 14s ease-in-out infinite; }
        .orb-3 { animation: orb-float   22s ease-in-out infinite 3s; }

        /* ── Subtle ping / pulse variants ───────────────────── */
        @keyframes soft-pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.5; }
        }
        .soft-pulse { animation: soft-pulse 2.5s ease-in-out infinite; }

        /* ── Marquee ────────────────────────────────────────── */
        .marquee-track {
            display: flex;
            width: max-content;
            animation: marqueeScroll 40s linear infinite;
        }
        .marquee-track:hover { animation-play-state: paused; }
        @keyframes marqueeScroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* ── Card hover lift ────────────────────────────────── */
        .card-lift {
            transition: transform 0.3s cubic-bezier(.22,.68,0,1.2), box-shadow 0.3s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px -12px rgba(37,99,235,.18);
        }

        /* ── Glow buttons ───────────────────────────────────── */
        .btn-glow {
            box-shadow: 0 0 0 0 rgba(37,99,235,.6);
            transition: box-shadow .3s ease, transform .2s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 0 28px 6px rgba(37,99,235,.35);
            transform: translateY(-2px);
        }

        /* ── Navbar scroll blur (JS-toggled) ────────────────── */
        #site-nav.scrolled {
            background: rgba(255,255,255,.92) !important;
            box-shadow: 0 1px 32px rgba(15,23,42,.08);
        }

        /* ── Module tab micro-interactions ─────────────────── */
        .module-tab {
            position: relative;
            overflow: hidden;
        }
        .module-tab::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(37,99,235,.1), rgba(79,70,229,.08));
            opacity: 0;
            transition: opacity .3s ease;
            border-radius: inherit;
        }
        .module-tab:hover::before {
            opacity: 1;
        }

        /* ── AOS pointer fix ────────────────────────────────── */
        [data-aos] { pointer-events: none; }
        .aos-animate { pointer-events: auto; }

        /* ── Stat counter ───────────────────────────────────── */
        .stat-counter { font-variant-numeric: tabular-nums; }

        /* ── Spin slow ──────────────────────────────────────── */
        @keyframes spin-slow   { to { transform: rotate(360deg); } }
        @keyframes spin-slow-r { to { transform: rotate(-360deg); } }
        .spin-slow   { animation: spin-slow   9s  linear infinite; }
        .spin-slow-r { animation: spin-slow-r 11s linear infinite; }

        /* ── Grid noise overlay ─────────────────────────────── */
        .noise-bg::before {
            content:'';
            position:absolute;inset:0;
            background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events:none;
            opacity: .4;
            z-index: 0;
        }

        /* ── Hero grid ──────────────────────────────────────── */
        .hero-grid {
            background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* ── Section wave divider ───────────────────────────── */
        .wave-divider svg path { fill: inherit; }

        /* ── Testimonial slider ─────────────────────────────── */
        .testimonial-track {
            display: flex;
            transition: transform .5s cubic-bezier(.4,0,.2,1);
        }
        .testimonial-slide {
            min-width: 100%;
        }

        /* ── Progress bar ───────────────────────────────────── */
        @keyframes progress-fill {
            from { width: 0%; }
            to   { width: var(--target-w); }
        }
        .progress-bar { animation: progress-fill 1.8s cubic-bezier(.4,0,.2,1) both; }

        /* ── Hero continuous animations ──────────────────────── */
        @keyframes hero-float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            33%       { transform: translateY(-18px) rotate(3deg); opacity: 1; }
            66%       { transform: translateY(-8px) rotate(-2deg); opacity: 0.8; }
        }
        @keyframes hero-float-2 {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.4; }
            50%       { transform: translateY(-25px) rotate(-4deg) scale(1.15); opacity: 0.85; }
        }
        @keyframes beam-glow {
            0%, 100% { opacity: 0.06; transform: scaleX(1); }
            50%       { opacity: 0.14; transform: scaleX(1.04); }
        }
        @keyframes badge-float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }
        @keyframes cursor-blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0; }
        }
        .hero-particle-1 { animation: hero-float   7s ease-in-out infinite; }
        .hero-particle-2 { animation: hero-float-2 9s ease-in-out infinite 1s; }
        .hero-particle-3 { animation: hero-float   11s ease-in-out infinite 2s; }
        .hero-beam       { animation: beam-glow    5s ease-in-out infinite; }
        .badge-float     { animation: badge-float  4s ease-in-out infinite; }

        /* ── Module card shake + zoom on hover ──────────────── */
        @keyframes module-shake {
            0%,100% { transform: translateY(-8px) scale(1.03) rotate(0deg); }
            20%      { transform: translateY(-8px) scale(1.03) rotate(-1.2deg); }
            40%      { transform: translateY(-8px) scale(1.03) rotate(1.2deg); }
            60%      { transform: translateY(-8px) scale(1.03) rotate(-0.8deg); }
            80%      { transform: translateY(-8px) scale(1.03) rotate(0.8deg); }
        }
        .module-card:hover {
            animation: module-shake 0.55s ease-in-out forwards;
            box-shadow: 0 20px 60px -10px rgba(37,99,235,.22);
            z-index: 10;
        }

        /* ── Stats section animated glow shadow ──────────────── */
        @keyframes glow-pulse {
            0%,100% { box-shadow: 0 0 30px rgba(99,102,241,.08), inset 0 1px 0 rgba(255,255,255,.08); }
            50%      { box-shadow: 0 0 60px rgba(99,102,241,.18), inset 0 1px 0 rgba(255,255,255,.12); }
        }
        .stat-card-glow { animation: glow-pulse 3.5s ease-in-out infinite; }

        /* ── Roadmap animated gradient ────────────────────────── */
        @keyframes roadmap-bg {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .roadmap-bg {
            background: linear-gradient(-45deg, #f0f9ff, #eff6ff, #f5f3ff, #faf5ff, #f0fdf4);
            background-size: 400% 400%;
            animation: roadmap-bg 12s ease infinite;
        }

        /* ── Animated wave separator (Hero → Partners) ──────── */
        @keyframes wave-flow {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animated-wave {
            will-change: transform;
        }
        .wave-back  { animation: wave-flow 8s linear infinite; }
        .wave-mid   { animation: wave-flow 6s linear infinite; }
        .wave-front { animation: wave-flow 4s linear infinite; }

        /* ── CTA section — distinct from footer ──────────────── */
        .cta-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0c1a3e 70%, #0f172a 100%);
        }
    </style>

    {{-- ═══════════════════ Google Analytics 4 ═══════════════════ --}}
    @php
        $gaEnabled = \App\Models\SystemSetting::get('ga_enabled', false);
        $gaMeasurementId = \App\Models\SystemSetting::get('ga_measurement_id');
    @endphp
    @if($gaEnabled && $gaMeasurementId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaMeasurementId }}', {
            page_title: document.title,
            page_location: window.location.href,
            send_page_view: true
        });
    </script>
    @endif
</head>
<body class="antialiased overflow-x-hidden">

    @php $settings = \App\Models\Setting::pluck('value','key')->toArray(); @endphp

    {{-- ═══════════════════════ NAVBAR ═══════════════════════ --}}
    <nav id="site-nav"
         class="fixed top-0 inset-x-0 z-50 transition-all duration-500 border-b border-transparent"
         x-data="{ open: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'bg-white/90 backdrop-blur-xl shadow-lg shadow-slate-200/50 border-slate-100' : 'bg-slate-900/10 backdrop-blur-md'">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
            <div class="flex items-center justify-between h-[68px]">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2 group shrink-0">
                    <div class="w-9 h-9 rounded-xl overflow-hidden shadow-md shadow-blue-500/20 group-hover:scale-110 transition-transform flex-shrink-0">
                        <img src="{{ asset('images/logo/favicon.png') }}" alt="TrustedU ERP" class="w-full h-full object-cover">
                    </div>
                    <span class="text-[17px] font-bold tracking-tight transition-colors duration-300"
                          :class="scrolled ? 'text-slate-900' : 'text-white'">
                        TrustedU<span class="transition-colors duration-300" :class="scrolled ? 'text-blue-600' : 'text-blue-400'"> ERP</span>
                    </span>
                </a>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center gap-1">
                    @foreach([['#home','Home'],['#modules','Modules'],['#partners','Schools'],['#process','Roadmap'],['#testimonials','Reviews']] as [$href,$label])
                    <a href="{{ $href }}"
                       class="px-3.5 py-2 text-[13px] font-medium rounded-lg transition-all"
                       :class="scrolled ? 'text-slate-600 hover:text-blue-600 hover:bg-blue-50' : 'text-white/80 hover:text-white hover:bg-white/10'">{{ $label }}</a>
                    @endforeach
                </div>

                {{-- CTAs --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/admin') }}" class="hidden sm:flex text-[12px] font-semibold text-slate-600 hover:text-slate-900 px-4 py-2 rounded-lg border border-slate-200 hover:border-slate-300 transition-all">Dashboard</a>
                    @else
                        <a href="{{ url('/admin/login') }}" class="hidden sm:flex text-[12px] font-semibold text-slate-600 hover:text-slate-900 px-4 py-2 rounded-lg border border-slate-200 hover:border-slate-300 transition-all">Sign In</a>
                    @endauth
                    <a href="{{ route('demo.form') }}" class="btn-glow inline-flex items-center gap-2 px-5 py-2.5 text-[12px] font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all">
                        Book a Demo
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    {{-- Mobile hamburger --}}
                    <button @click="open = !open" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="open"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="open" x-transition class="md:hidden pb-4 border-t border-slate-100 mt-1">
                <div class="flex flex-col gap-1 pt-3">
                    @foreach([['#home','Home'],['#modules','Modules'],['#partners','Schools'],['#process','Roadmap'],['#testimonials','Reviews']] as [$href,$label])
                    <a href="{{ $href }}" @click="open=false" class="px-4 py-2.5 text-[14px] font-medium text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">{{ $label }}</a>
                    @endforeach
                    <div class="mt-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('demo.form') }}" class="block px-4 py-2.5 text-[14px] font-semibold text-white bg-blue-600 rounded-xl text-center">Book a Demo →</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>@yield('content')</main>

    {{-- ═══════════════════════ FOOTER ═══════════════════════ --}}
    <footer class="bg-[#0a0f1e] text-slate-400 pt-24 pb-10 relative overflow-hidden">
        {{-- Background orbs --}}
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[120px] orb-1"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-indigo-600/5 rounded-full blur-[120px] orb-2"></div>

        <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-9 h-9 rounded-xl overflow-hidden shadow-lg shadow-blue-500/20 flex-shrink-0">
                            <img src="{{ asset('images/logo/favicon.png') }}" alt="TrustedU ERP" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xl font-bold text-white">TrustedU <span class="text-blue-400">ERP</span></span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-500 max-w-sm mb-8">
                        Authorized by Bangladesh Army. Serving 63 Cantonment Public Schools & Colleges under a unified, secure, and high-performance digital ecosystem.
                    </p>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-full text-[11px] font-semibold text-blue-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 soft-pulse"></span>
                            Live on 17 Campuses
                        </span>
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/10 rounded-full text-[11px] font-semibold text-slate-400">
                            🏛 Bangladesh Army
                        </span>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-5">Platform</h4>
                    <ul class="space-y-3">
                        @foreach(['#modules'=>'ERP Modules','#process'=>'Strategic Roadmap','#testimonials'=>'Success Stories',route('demo.form')=>'Request Demo'] as $href=>$label)
                        <li><a href="{{ $href }}" class="text-sm text-slate-500 hover:text-blue-400 transition-colors">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Contact</h4>

                    {{-- White logo --}}
                    <div class="mb-5">
                        <img src="{{ asset('images/logo/white-logo.png') }}"
                             alt="TrustedU ERP"
                             class="h-10 w-auto object-contain opacity-90">
                    </div>

                    <div class="space-y-3 text-sm text-slate-500">
                        <div>{{ $settings['contact_address'] ?? 'Trust Bank Tower, Gulshan, Dhaka-1212' }}</div>
                        <div><span class="text-slate-400">Email:</span> <span class="text-white/70">{{ $settings['contact_email'] ?? 'info@tilbd.net' }}</span></div>
                        <div><span class="text-slate-400">Phone:</span> <span class="text-white/70">{{ $settings['contact_phone'] ?? '+880 1234 567890' }}</span></div>
                        <div class="pt-2 text-[11px] text-slate-600 uppercase tracking-widest font-semibold">Powered by Trust Innovation Ltd.</div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-[12px] text-slate-600">&copy; {{ date('Y') }} TrustedU ERP. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="{{ route('privacy-policy') }}" class="text-[12px] text-slate-600 hover:text-blue-400 transition-colors">Privacy Policy</a>
                    <a href="{{ $settings['company_website'] ?? '#' }}" target="_blank" class="text-[12px] text-slate-600 hover:text-blue-400 transition-colors">TILBD Official ↗</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- AOS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 700, once: true, offset: 60, easing: 'ease-out-cubic' });

        // Animated stat counters
        function animateCounters() {
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseInt(el.dataset.count);
                const suffix = el.dataset.suffix || '';
                const dur  = 2000;
                const step = target / (dur / 16);
                let cur = 0;
                const timer = setInterval(() => {
                    cur = Math.min(cur + step, target);
                    el.textContent = Math.floor(cur).toLocaleString() + (cur >= target ? suffix : '');
                    if (cur >= target) clearInterval(timer);
                }, 16);
            });
        }
        // Trigger when stats section enters viewport
        const statsObs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { animateCounters(); statsObs.disconnect(); } });
        }, { threshold: 0.3 });
        document.addEventListener('DOMContentLoaded', () => {
            const sc = document.getElementById('stats-counters');
            if (sc) statsObs.observe(sc);
        });
    </script>

    @stack('scripts')

    {{-- ═══════════════════ GA4 Custom Event Tracking ═══════════════════ --}}
    @php
        $gaTrackEvents = \App\Models\SystemSetting::get('ga_track_events', false);
    @endphp
    @if($gaEnabled && $gaMeasurementId && $gaTrackEvents)
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helper: safe gtag event
        function trackEvent(eventName, params) {
            if (typeof gtag === 'function') {
                gtag('event', eventName, params);
            }
        }

        // ── Track "Book a Demo" button clicks ──
        document.querySelectorAll('a[href*="demo"], a[href*="Demo"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                trackEvent('demo_request_click', {
                    event_category: 'engagement',
                    event_label: btn.textContent.trim(),
                    link_url: btn.href
                });
            });
        });

        // ── Track Contact Form submissions ──
        var contactForm = document.querySelector('form[action*="contact"]');
        if (contactForm) {
            contactForm.addEventListener('submit', function() {
                trackEvent('contact_form_submit', {
                    event_category: 'conversion',
                    event_label: 'Contact Form'
                });
            });
        }

        // ── Track Demo Form submissions ──
        var demoForm = document.querySelector('form[action*="demo"]');
        if (demoForm) {
            demoForm.addEventListener('submit', function() {
                trackEvent('demo_form_submit', {
                    event_category: 'conversion',
                    event_label: 'Demo Request Form'
                });
            });
        }

        // ── Track Module card clicks (landing page) ──
        document.querySelectorAll('.module-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var moduleName = card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : 'Unknown';
                trackEvent('module_view', {
                    event_category: 'content',
                    event_label: moduleName,
                    module_name: moduleName
                });
            });
        });

        // ── Track "Sign In" clicks ──
        document.querySelectorAll('a[href*="login"], a[href*="admin"]').forEach(function(link) {
            link.addEventListener('click', function() {
                trackEvent('sign_in_click', {
                    event_category: 'engagement',
                    event_label: link.textContent.trim()
                });
            });
        });

        // ── Track scroll depth (25%, 50%, 75%, 100%) ──
        var scrollMarks = {25: false, 50: false, 75: false, 100: false};
        window.addEventListener('scroll', function() {
            var scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            [25, 50, 75, 100].forEach(function(mark) {
                if (scrollPercent >= mark && !scrollMarks[mark]) {
                    scrollMarks[mark] = true;
                    trackEvent('scroll_depth', {
                        event_category: 'engagement',
                        event_label: mark + '% scrolled',
                        value: mark
                    });
                }
            });
        });
    });
    </script>
    @endif
</body>
</html>
