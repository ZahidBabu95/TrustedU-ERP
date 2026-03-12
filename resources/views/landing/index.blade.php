@extends('layouts.app')

@section('title', ($settings['site_name'] ?? 'TrustedU ERP') . ' — Education Management Platform')

@push('scripts')
<script>
    /* ── Testimonial slider ─────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        const track  = document.getElementById('testi-track');
        const slides = document.querySelectorAll('.testi-slide');
        const dots   = document.querySelectorAll('.testi-dot');
        let curr = 0, timer;

        function goTo(n) {
            curr = (n + slides.length) % slides.length;
            track.style.transform = `translateX(-${curr * 100}%)`;
            dots.forEach((d,i) => {
                d.classList.toggle('bg-blue-600', i === curr);
                d.classList.toggle('w-6',          i === curr);
                d.classList.toggle('bg-slate-300', i !== curr);
                d.classList.toggle('w-2',          i !== curr);
            });
        }
        function startTimer() { timer = setInterval(() => goTo(curr + 1), 5000); }
        function stopTimer()  { clearInterval(timer); }

        document.getElementById('testi-prev')?.addEventListener('click', () => { stopTimer(); goTo(curr - 1); startTimer(); });
        document.getElementById('testi-next')?.addEventListener('click', () => { stopTimer(); goTo(curr + 1); startTimer(); });
        dots.forEach((d,i) => d.addEventListener('click', () => { stopTimer(); goTo(i); startTimer(); }));
        startTimer();
    });
</script>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════
     §1  HERO
══════════════════════════════════════════════════════ --}}
<section id="home" class="relative min-h-screen flex items-center bg-[#060c1a] hero-grid overflow-hidden pt-20 pb-28">

    {{-- Background orbs --}}
    <div class="absolute -top-40 -right-40  w-[700px] h-[700px] rounded-full bg-blue-600/15 blur-[140px] orb-1 pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] rounded-full bg-indigo-600/15 blur-[120px] orb-2 pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] rounded-full bg-violet-700/5 blur-[160px] orb-3 pointer-events-none"></div>

    {{-- Animated beam lines --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="hero-beam absolute top-1/4 left-0 w-full h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent"></div>
        <div class="hero-beam absolute top-2/3 left-0 w-full h-px bg-gradient-to-r from-transparent via-indigo-500/15 to-transparent" style="animation-delay:2.5s"></div>
    </div>

    {{-- Continuous floating particles --}}
    <div class="absolute inset-0 pointer-events-none hidden lg:block">
        {{-- Triangles --}}
        <div class="hero-particle-1 absolute top-28 left-12 w-5 h-5 border-2 border-blue-400/30 rotate-45"></div>
        <div class="hero-particle-2 absolute top-64 right-20 w-4 h-4 border-2 border-cyan-400/25 rotate-12"></div>
        <div class="hero-particle-3 absolute bottom-48 left-1/3 w-3 h-3 border border-indigo-400/30 rotate-45"></div>
        {{-- Circles --}}
        <div class="hero-particle-2 absolute top-48 left-1/4 w-2.5 h-2.5 rounded-full bg-blue-500/40"></div>
        <div class="hero-particle-1 absolute top-80 right-1/3 w-2 h-2 rounded-full bg-cyan-400/50" style="animation-delay:1.5s"></div>
        <div class="hero-particle-3 absolute bottom-32 right-24 w-3 h-3 rounded-full bg-violet-400/35" style="animation-delay:3s"></div>
        {{-- Squares --}}
        <div class="hero-particle-2 absolute top-36 right-1/4 w-4 h-4 bg-blue-600/10 border border-blue-400/20"></div>
        <div class="hero-particle-1 absolute bottom-56 left-20 w-3 h-3 bg-indigo-600/10 border border-indigo-400/20" style="animation-delay:4s"></div>
        {{-- Glowing dots --}}
        <div class="soft-pulse absolute top-44 left-8 w-1.5 h-1.5 rounded-full bg-blue-400/60"></div>
        <div class="soft-pulse absolute top-72 right-14 w-1.5 h-1.5 rounded-full bg-cyan-400/60" style="animation-delay:0.8s"></div>
        <div class="soft-pulse absolute bottom-44 left-1/4 w-1.5 h-1.5 rounded-full bg-indigo-400/50" style="animation-delay:1.6s"></div>
        <div class="soft-pulse absolute bottom-60 right-1/3 w-1 h-1 rounded-full bg-violet-400/60" style="animation-delay:2.4s"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 w-full">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">

            {{-- Left Content --}}
            <div class="lg:w-[55%] text-center lg:text-left" data-aos="fade-right" data-aos-delay="0">

                {{-- Badge --}}
                <div class="badge-float inline-flex items-center gap-2.5 px-4 py-2 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-[12px] font-semibold uppercase tracking-widest mb-8 shadow-lg shadow-blue-500/10">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-400"></span>
                    </span>
                    Authorized by Bangladesh Army
                </div>

                {{-- H1 — improved contrast --}}
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-[1.08] tracking-tight mb-7">
                    <span class="text-white drop-shadow-[0_2px_20px_rgba(255,255,255,0.1)]">The Smarter Way<br>to Manage<br></span>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-cyan-300 to-indigo-400 animate-gradient-text drop-shadow-none">
                        Educational Institute
                    </span>
                </h1>

                <p class="text-lg text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed mb-10 font-medium">
                    {{ $settings['site_name'] ?? 'TrustedU ERP' }} unifies
                    <span class="text-white font-semibold">63 Cantonment Public Schools &amp; Colleges</span>
                    on one secure, high-performance platform — built for the future of education.
                </p>

                {{-- CTA row --}}
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-14">
                    <a href="{{ route('demo.form') }}"
                       class="btn-glow w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-[14px] font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl transition-all">
                        Book a Free Demo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#modules"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 text-[14px] font-semibold text-slate-300 hover:text-white border border-white/10 hover:border-white/25 rounded-2xl backdrop-blur-sm bg-white/4 transition-all">
                        Explore Modules
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>

                {{-- Stat counters — dynamic from settings --}}
                <div id="stats-counters" class="flex flex-wrap justify-center lg:justify-start gap-5">
                    @php
                        $heroStats = [
                            ['value' => $settings['stats_institutions'] ?? '63',  'label' => 'Institutions',  'suffix' => '', 'color' => 'blue'],
                            ['value' => $settings['stats_active_campuses'] ?? '17','label' => 'Active Campuses','suffix' => '', 'color' => 'indigo'],
                            ['value' => $settings['stats_students'] ?? '50000',   'label' => 'Students',       'suffix' => '+', 'color' => 'cyan'],
                        ];
                        $statColors = ['blue'=>'from-blue-600 to-blue-400','indigo'=>'from-indigo-600 to-violet-500','cyan'=>'from-cyan-600 to-blue-500'];
                    @endphp
                    @foreach($heroStats as $stat)
                    <div class="text-center lg:text-left px-5 py-3.5 rounded-2xl bg-white/5 border border-white/8 backdrop-blur-sm group hover:bg-white/8 hover:border-white/15 transition-all duration-300 min-w-[90px]">
                        <div class="text-2xl font-extrabold text-white stat-counter" data-count="{{ preg_replace('/\D/','',$stat['value']) }}" data-suffix="{{ $stat['suffix'] }}">0</div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Dashboard mockup --}}
            <div class="lg:w-[45%] relative" data-aos="fade-left" data-aos-delay="150">
                <div class="relative group">
                    {{-- Glow halo --}}
                    <div class="absolute inset-0 bg-blue-600/20 rounded-[32px] blur-[80px] group-hover:bg-blue-500/30 transition-all duration-700"></div>

                    {{-- Card --}}
                    <div class="relative rounded-[28px] border border-white/10 bg-white/4 backdrop-blur-2xl p-3 shadow-2xl">
                        {{-- Fake browser chrome --}}
                        <div class="flex items-center gap-1.5 px-4 py-2.5 mb-2 border-b border-white/6">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-400/70"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-400/70"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-400/70"></div>
                            <div class="mx-auto flex items-center gap-2 px-4 py-1 rounded-full bg-white/6 text-[11px] text-slate-500 font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                trusteduerp.edu.bd/dashboard
                            </div>
                        </div>

                        {{-- Dashboard screenshot --}}
                        <div class="rounded-[18px] overflow-hidden bg-[#0d1526] aspect-video relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-indigo-900/40"></div>
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80"
                                 alt="ERP Dashboard"
                                 class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700 group-hover:scale-105">

                            {{-- Overlay mini-cards --}}
                            <div class="absolute bottom-4 left-4 flex gap-3">
                                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl px-3 py-2 text-white text-[11px] font-semibold">
                                    <div class="text-green-400 text-xs mb-0.5">↑ 12.4%</div>
                                    Attendance
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl px-3 py-2 text-white text-[11px] font-semibold">
                                    <div class="text-blue-400 text-xs mb-0.5">🎓 2,340</div>
                                    Students
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating feature badges --}}
                    <div class="badge-float absolute -top-5 -right-5 px-4 py-2 rounded-2xl bg-white shadow-xl text-[12px] font-bold text-slate-800 border border-slate-100 flex items-center gap-2" data-aos="zoom-in" data-aos-delay="400">
                        <span class="w-2 h-2 rounded-full bg-green-500 soft-pulse"></span> Live Sync
                    </div>
                    <div class="badge-float absolute -bottom-5 -left-5 px-4 py-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xl text-[12px] font-bold flex items-center gap-2" data-aos="zoom-in" data-aos-delay="550" style="animation-delay:2s">
                        🔐 Army Authorized
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave section separator --}}
    <div class="section-wave absolute bottom-0 left-0 w-full pointer-events-none">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="height:60px">
            <path d="M0,30 C180,60 360,0 540,30 C720,60 900,0 1080,30 C1260,60 1380,20 1440,30 L1440,60 L0,60 Z" fill="#ffffff" opacity="1"/>
        </svg>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     §2  STRATEGIC FOOTPRINT (Logo Marquee)
══════════════════════════════════════════════════════ --}}
<div id="partners" class="overflow-hidden">

    {{-- Glassmorphic marquee strip --}}
    <div class="relative py-5"
         style="background: linear-gradient(135deg, rgba(239,246,255,0.95) 0%, rgba(238,242,255,0.9) 50%, rgba(245,243,255,0.95) 100%); backdrop-filter: blur(16px); box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), inset 0 -1px 0 rgba(148,163,184,0.15), 0 12px 40px rgba(99,102,241,0.07);"
         x-data="{
            selectedClient: null,
            paused: false,
            openModal(c) { this.selectedClient = c; document.body.style.overflow='hidden'; },
            closeModal()  { this.selectedClient = null; document.body.style.overflow='auto'; },
            scrollMarquee(dir) {
                const track = document.getElementById('marquee-inner');
                if (!track) return;
                this.paused = true;
                track.style.animationPlayState = 'paused';
                const by = dir === 'left' ? -220 : 220;
                let start = null, from = track._offset || 0;
                track._offset = from + by;
                const step = ts => {
                    if (!start) start = ts;
                    const p = Math.min((ts - start) / 350, 1);
                    const ease = p < 0.5 ? 2*p*p : -1+(4-2*p)*p;
                    track.style.transform = `translateX(${from + by * ease}px)`;
                    if (p < 1) requestAnimationFrame(step);
                    else { track._offset = from + by; }
                };
                requestAnimationFrame(step);
            }
         }">

        {{-- Soft top/bottom borders --}}
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-200/60 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-indigo-200/40 to-transparent"></div>

        {{-- Left arrow --}}
        <button @click="scrollMarquee('right')"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white border border-white/60 hover:border-slate-200 shadow-md backdrop-blur-sm flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all duration-200 group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Right arrow --}}
        <button @click="scrollMarquee('left')"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/80 hover:bg-white border border-white/60 hover:border-slate-200 shadow-md backdrop-blur-sm flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all duration-200 group">
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Marquee track --}}
        <div class="relative overflow-hidden px-12">
            <div class="pointer-events-none absolute inset-y-0 left-12 w-16 z-10"
                 style="background: linear-gradient(to right, rgba(239,246,255,1), transparent)"></div>
            <div class="pointer-events-none absolute inset-y-0 right-12 w-16 z-10"
                 style="background: linear-gradient(to left, rgba(245,243,255,1), transparent)"></div>

            <div id="marquee-inner" class="marquee-track items-center py-2">
                @php
                    $colorPairs = [
                        ['#2563eb','#4f46e5'],['#7c3aed','#a855f7'],
                        ['#0ea5e9','#06b6d4'],['#059669','#10b981'],
                        ['#d97706','#f59e0b'],['#dc2626','#f87171'],
                        ['#0284c7','#38bdf8'],['#7c3aed','#8b5cf6'],
                    ];
                    $idx = 0;
                @endphp

                @foreach(array_merge($clients->all(), $clients->all()) as $client)
                @php
                    $pair     = $colorPairs[$idx % count($colorPairs)];
                    $initials = collect(explode(' ', $client->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                    $logoUrl  = $client->logo ? Storage::url($client->logo) : '';
                    $clientName = addslashes($client->name);
                    $idx++;
                @endphp
                <div class="flex-shrink-0 mx-3 cursor-pointer"
                     @mouseenter="document.getElementById('marquee-inner').style.animationPlayState='paused'"
                     @mouseleave="document.getElementById('marquee-inner').style.animationPlayState='running'"
                     @click="openModal({ name:'{{ $clientName }}', logo:'{{ $logoUrl }}', website:'{{ $client->website }}', district:'{{ $client->district }}', type:'{{ Str::title($client->institution_type) }}', initials:'{{ $initials }}', c1:'{{ $pair[0] }}', c2:'{{ $pair[1] }}' })">

                    {{-- Logo card: bigger, full color, hover zoom + name --}}
                    <div class="group relative w-24 h-20 bg-white/80 rounded-2xl border border-white/70 shadow-sm flex items-center justify-center overflow-hidden transition-all duration-300 hover:scale-110 hover:shadow-xl hover:z-10 hover:border-white"
                         style="backdrop-filter: blur(8px);">

                        {{-- Hover color glow bg --}}
                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                             style="background: linear-gradient(135deg, {{ $pair[0] }}15, {{ $pair[1] }}20)"></div>

                        @if($client->logo)
                            {{-- Full color logo image --}}
                            <img src="{{ $logoUrl }}" alt="{{ $client->name }}"
                                 class="max-h-14 max-w-[80px] object-contain relative z-10 transition-transform duration-300 group-hover:scale-105"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            {{-- Fallback initials badge --}}
                            <div style="display:none; background:linear-gradient(135deg,{{ $pair[0] }},{{ $pair[1] }})"
                                 class="w-14 h-14 rounded-xl items-center justify-center shadow-md">
                                <span class="text-xl font-extrabold text-white">{{ $initials }}</span>
                            </div>
                        @else
                            {{-- Initials badge --}}
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center shadow-md relative z-10 transition-transform duration-300 group-hover:scale-105"
                                 style="background:linear-gradient(135deg,{{ $pair[0] }},{{ $pair[1] }})">
                                <span class="text-xl font-extrabold text-white">{{ $initials }}</span>
                            </div>
                        @endif

                        {{-- Hover name tooltip overlay --}}
                        <div class="absolute inset-0 rounded-2xl flex items-end justify-center pb-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none"
                             style="background: linear-gradient(to top, {{ $pair[0] }}cc 0%, transparent 60%)">
                            <span class="text-[9px] font-bold text-white text-center leading-tight px-1 drop-shadow-md line-clamp-2">
                                {{ Str::limit($client->name, 22) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Client modal — teleported to <body> to escape CSS transform/overflow clipping --}}
        <template x-teleport="body">
            <div x-show="selectedClient"
                 style="display:none"
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

                {{-- Modal card --}}
                <div class="relative w-full max-w-sm bg-white rounded-[28px] shadow-2xl overflow-hidden"
                     @click.stop
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-3">

                    {{-- Colored header --}}
                    <div class="h-24 relative overflow-hidden"
                         :style="selectedClient && `background:linear-gradient(135deg,${selectedClient.c1},${selectedClient.c2})`">
                        <div class="absolute inset-0 opacity-15"
                             style="background-image:radial-gradient(white 1px,transparent 1px);background-size:16px 16px"></div>
                        <button @click="closeModal()"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="px-8 pb-8 -mt-10 relative z-10 text-center">
                        {{-- Logo --}}
                        <div class="w-20 h-20 rounded-2xl bg-white shadow-xl border-4 border-white mx-auto mb-4 overflow-hidden flex items-center justify-center">
                            <template x-if="selectedClient && selectedClient.logo">
                                <img :src="selectedClient.logo" :alt="selectedClient.name" class="max-h-full max-w-full object-contain">
                            </template>
                            <template x-if="selectedClient && !selectedClient.logo">
                                <div class="w-full h-full flex items-center justify-center"
                                     :style="`background:linear-gradient(135deg,${selectedClient.c1},${selectedClient.c2})`">
                                    <span class="text-xl font-extrabold text-white" x-text="selectedClient && selectedClient.initials"></span>
                                </div>
                            </template>
                        </div>

                        <h3 class="text-lg font-extrabold text-slate-900 mb-2"
                            x-text="selectedClient && selectedClient.name"></h3>

                        <div class="flex flex-wrap justify-center gap-2 mb-5">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-full border border-blue-100"
                                  x-text="selectedClient && (selectedClient.type || 'Institution')"></span>
                            <template x-if="selectedClient && selectedClient.district">
                                <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[11px] font-bold rounded-full border border-slate-100"
                                      x-text="'📍 ' + selectedClient.district"></span>
                            </template>
                        </div>

                        <p class="text-sm text-slate-500 mb-6">
                            Proudly part of the TrustedU ERP ecosystem — transforming education management across Bangladesh.
                        </p>

                        <template x-if="selectedClient && selectedClient.website">
                            <a :href="selectedClient.website" target="_blank"
                               class="inline-flex items-center justify-center w-full gap-2 bg-slate-900 hover:bg-blue-600 text-white text-[12px] font-bold uppercase tracking-widest px-6 py-3 rounded-xl transition-all duration-300 group">
                                Visit Official Website
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     §3  MODULES / FEATURES
══════════════════════════════════════════════════════ --}}
<section id="modules" class="py-28 bg-white relative overflow-hidden">
    {{-- Subtle grid --}}
    <div class="absolute inset-0 opacity-[0.025]" style="background-image: linear-gradient(#94a3b8 1px, transparent 1px), linear-gradient(90deg,#94a3b8 1px, transparent 1px); background-size: 60px 60px;"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="text-center max-w-2xl mx-auto mb-20" data-aos="fade-up">
            <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-700 text-[11px] font-bold uppercase tracking-widest rounded-full border border-blue-100 mb-5">Enterprise Library</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-5 leading-tight">
                Smart ERP<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600">Core Engine</span>
            </h2>
            <p class="text-slate-500 text-lg">A complete suite of modules designed specifically for Cantonment educational institutions.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse($modules as $i => $module)
            @php
                $gradients = [
                    'blue'   => ['#2563eb','#3b82f6'],
                    'indigo' => ['#4f46e5','#818cf8'],
                    'purple' => ['#7c3aed','#a78bfa'],
                    'red'    => ['#dc2626','#f87171'],
                    'orange' => ['#d97706','#fb923c'],
                    'green'  => ['#059669','#34d399'],
                    'teal'   => ['#0d9488','#5eead4'],
                    'cyan'   => ['#0284c7','#38bdf8'],
                ];
                $g = $gradients[$module->color] ?? $gradients['blue'];
                $iconMap = [
                    'Student Registration'                       => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                    'Student Information System (SIS)'           => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    'Student TC and Drop out'                    => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
                    'Student Fees, Dues, Online Fee Collection'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'Exam and Assessment Management'             => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    'Attendance Tracking'                        => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'SMS Module'                                 => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
                    'Transport management'                       => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2a1 1 0 011-1h1m8-1h2l4 2v6h-6V16zm-1 0H5',
                    'Mobile App for Teacher and Student'         => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                    'User, Role, Access Control'                 => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'Dynamic website'                            => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
                    'Online Admission/ Admission Management'     => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                    'HRM, Leave & Payroll'                       => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'Financial/ Accounts Management'             => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'Learning Management System (LMS)'           => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'Library Management'                         => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'Hostel Management'                          => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'OMR'                                        => 'M4.5 12.75l6 6 9-13.5',
                    'Assets and Inventory Management'            => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'Transport Tracking'                         => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z',
                ];
                $path = $iconMap[$module->name] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
            @endphp
            <div class="module-card group relative rounded-2xl border border-slate-100 bg-white p-6 overflow-hidden cursor-pointer"
                 data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 60 }}"
                 style="transition: border-color 0.3s ease;">
                {{-- Hover gradient --}}
                <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                     style="background: linear-gradient(145deg, {{ $g[0] }}06, {{ $g[1] }}10)"></div>

                {{-- Icon --}}
                <div class="relative z-10 w-12 h-12 rounded-xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300"
                     style="background: linear-gradient(135deg, {{ $g[0] }}, {{ $g[1] }})">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $path }}"/>
                    </svg>
                </div>

                <h3 class="relative z-10 text-[15px] font-bold text-slate-900 mb-2 group-hover:text-blue-700 transition-colors leading-snug">{{ $module->name }}</h3>
                <p class="relative z-10 text-[13px] text-slate-500 leading-relaxed mb-4">{{ Str::limit($module->description, 90) }}</p>

                @if($module->features && is_array($module->features))
                <div class="relative z-10 flex flex-wrap gap-1.5">
                    @foreach(array_slice($module->features, 0, 3) as $feat)
                    <span class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-100 text-[10px] font-semibold text-slate-600 uppercase tracking-wide">{{ $feat }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Arrow indicator --}}
                <div class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-slate-50 group-hover:bg-blue-50 border border-slate-100 group-hover:border-blue-200 flex items-center justify-center transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     §4  KEY METRICS / WHY US
══════════════════════════════════════════════════════ --}}
<section class="py-24 relative overflow-hidden" style="background: linear-gradient(135deg, #0d1b3e 0%, #111827 30%, #1a1040 60%, #0d1b3e 100%)">
    {{-- Animated orbs --}}
    <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-blue-600/12 rounded-full blur-[130px] orb-1"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-violet-600/10 rounded-full blur-[120px] orb-2"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-700/5 rounded-full blur-[160px]"></div>
    <div class="absolute inset-0 opacity-8" style="background-image: radial-gradient(rgba(99,102,241,.4) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Platform in Numbers</h2>
            <p class="text-slate-400 font-medium">Real impact across Bangladesh's cantonment educational institutions</p>
        </div>

        @php
            $platformStats = [
                ['num' => $settings['stats_institutions'] ?? '63',  'label' => 'Institutions',  'sub' => 'Schools & Colleges',      'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5',    'color' => '#3b82f6'],
                ['num' => $settings['stats_students'] ?? '50,000',  'label' => 'Students',      'sub' => 'Enrolled Nationwide',      'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197', 'color' => '#818cf8'],
                ['num' => $settings['stats_active_campuses'] ?? '17','label' => 'Campuses',    'sub' => 'Actively Running',          'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622', 'color' => '#34d399'],
                ['num' => $settings['stats_modules'] ?? '18',       'label' => 'Modules',      'sub' => 'Core ERP Features',         'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'color' => '#f59e0b'],
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
            @foreach($platformStats as $i => $s)
            <div class="stat-card-glow text-center p-7 rounded-2xl border border-white/10 backdrop-blur-sm group hover:border-opacity-50 transition-all duration-500 relative overflow-hidden"
                 style="background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.03)); border-color: {{ $s['color'] }}20"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                {{-- Color glow bg --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"
                     style="background: radial-gradient(circle at 50% 0%, {{ $s['color'] }}15, transparent 70%)"></div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-5 relative"
                     style="background: linear-gradient(135deg, {{ $s['color'] }}22, {{ $s['color'] }}10); border: 1px solid {{ $s['color'] }}30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $s['color'] }}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-extrabold text-white mb-1 relative">{{ $s['num'] }}+</div>
                <div class="text-[13px] font-bold mb-0.5 relative" style="color: {{ $s['color'] }}">{{ $s['label'] }}</div>
                <div class="text-[11px] text-slate-500 relative">{{ $s['sub'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Progress metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
            @foreach([
                ['Attendance Digitization','93%',93,'#3b82f6'],
                ['Fee Collection Rate','88%',88,'#818cf8'],
                ['System Uptime','99.8%',99.8,'#34d399'],
            ] as [$label,$pct,$val,$clr])
            <div class="p-6 rounded-2xl bg-white/5 border border-white/8">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[13px] font-semibold text-white/80">{{ $label }}</span>
                    <span class="text-[14px] font-extrabold" style="color:{{ $clr }}">{{ $pct }}</span>
                </div>
                <div class="h-2 bg-white/8 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar" style="--target-w:{{ $val }}%; background:{{ $clr }}; animation-delay:.3s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     §5  ROADMAP
══════════════════════════════════════════════════════ --}}
<section id="process" class="roadmap-bg py-28 relative overflow-hidden">
    {{-- Decorative floating shapes --}}
    <div class="absolute top-20 right-10 w-64 h-64 rounded-full bg-indigo-200/20 blur-[80px] orb-2 pointer-events-none hidden lg:block"></div>
    <div class="absolute bottom-20 left-10 w-48 h-48 rounded-full bg-blue-200/20 blur-[60px] orb-1 pointer-events-none hidden lg:block"></div>
    <div class="absolute inset-0 opacity-[0.025]" style="background-image: linear-gradient(#6366f1 1px, transparent 1px), linear-gradient(90deg,#6366f1 1px,transparent 1px); background-size: 60px 60px;"></div>

    <div class="relative max-w-6xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="text-center mb-20" data-aos="fade-up">
            <span class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-700 text-[11px] font-bold uppercase tracking-widest rounded-full border border-indigo-100 mb-4">Strategic Roadmap</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
                Journey to <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">Digital Excellence</span>
            </h2>
            <p class="text-slate-500 text-lg max-w-xl mx-auto">How TrustedU ERP transforms an institution from the ground up</p>
        </div>

        <div class="relative">
            {{-- Center line --}}
            <div class="absolute left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-slate-200 to-transparent -translate-x-1/2 hidden lg:block"></div>

            <div class="space-y-16">
                @foreach([
                    ['01','Strategic Planning','Tailored system design based on Army HQ directives and each institution\'s unique needs.','#2563eb','fade-right'],
                    ['02','Data Centralization','Migrating student records into a secure, unified platform with zero data loss.','#7c3aed','fade-left'],
                    ['03','Digital Training','Comprehensive workshops for teachers, principals, and administrative officers.','#0ea5e9','fade-right'],
                    ['04','Live & Scaling','Real-time platform with 99.8% uptime — becoming the hub for all education data.','#f59e0b','fade-left'],
                ] as $step)
                @php [$num,$title,$desc,$clr,$dir] = $step; $isLeft = $dir === 'fade-right'; @endphp
                <div class="relative flex flex-col lg:flex-row {{ !$isLeft ? 'lg:flex-row-reverse' : '' }} items-center gap-8 group" data-aos="{{ $dir }}">
                    <div class="{{ $isLeft ? 'lg:text-right lg:pr-16' : 'lg:text-left lg:pl-16' }} flex-1">
                        <div class="p-8 rounded-2xl border border-slate-100 bg-white shadow-sm group-hover:shadow-xl group-hover:border-slate-200 transition-all duration-300">
                            <h3 class="text-2xl font-extrabold text-slate-900 mb-3 group-hover:text-blue-700 transition-colors">{{ $title }}</h3>
                            <p class="text-slate-500 leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>

                    {{-- Number node --}}
                    <div class="relative z-10 flex-shrink-0 w-16 h-16 rounded-full bg-white border-2 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300"
                         style="border-color: {{ $clr }}; box-shadow: 0 0 0 4px {{ $clr }}20">
                        <div class="absolute inset-2 rounded-full spin-slow opacity-50" style="border: 1px solid {{ $clr }}50"></div>
                        <span class="font-extrabold text-[17px]" style="color: {{ $clr }}">{{ $num }}</span>
                    </div>

                    <div class="flex-1 {{ $isLeft ? 'lg:pl-16' : 'lg:pr-16' }} hidden lg:block"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     §6  TESTIMONIALS
══════════════════════════════════════════════════════ --}}
<section id="testimonials" class="py-28 bg-slate-50 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-[100px]"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col lg:flex-row gap-16 items-start">

            {{-- Left label --}}
            <div class="lg:w-1/3 lg:sticky lg:top-28" data-aos="fade-right">
                <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-700 text-[11px] font-bold uppercase tracking-widest rounded-full border border-blue-100 mb-5">Testimonials</span>
                <h2 class="text-4xl font-extrabold text-slate-900 leading-tight mb-5">
                    Trusted by <span class="text-blue-600">Education Leaders</span>
                </h2>
                <p class="text-slate-500 leading-relaxed mb-8">See what principals, teachers, and administrators say about TrustedU ERP.</p>
                <div class="flex items-center gap-3">
                    <button id="testi-prev" class="w-10 h-10 rounded-full bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 flex items-center justify-center transition-all shadow-sm">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex gap-2" id="testi-dots">
                        @forelse($testimonials as $ti => $t)
                        <button class="testi-dot h-2 rounded-full transition-all duration-300 {{ $ti === 0 ? 'bg-blue-600 w-6' : 'bg-slate-300 w-2' }}"></button>
                        @empty
                        <button class="testi-dot h-2 rounded-full bg-blue-600 w-6"></button>
                        @endforelse
                    </div>
                    <button id="testi-next" class="w-10 h-10 rounded-full bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50 flex items-center justify-center transition-all shadow-sm">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- Slider --}}
            <div class="lg:w-2/3 overflow-hidden" data-aos="fade-left">
                <div id="testi-track" class="testimonial-track">
                    @forelse($testimonials as $testimonial)
                    <div class="testi-slide min-w-full px-1">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 relative">
                            {{-- Quote icon --}}
                            <div class="absolute top-6 right-6 w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                            </div>

                            <p class="text-slate-700 text-lg leading-relaxed font-medium italic mb-8 pr-12">
                                "{{ $testimonial->message }}"
                            </p>

                            {{-- Stars --}}
                            <div class="flex gap-1 mb-5">
                                @for($s=0;$s<5;$s++)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                @endfor
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $testimonial->name }}</div>
                                    <div class="text-[12px] text-blue-600 font-semibold uppercase tracking-wide">{{ $testimonial->designation }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="testi-slide min-w-full px-1">
                        <div class="bg-white rounded-2xl border border-slate-100 p-8">
                            <p class="text-slate-500 italic">"TrustedU ERP has completely transformed how we manage our school's operations."</p>
                            <div class="mt-6 font-bold text-slate-800">School Principal</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     §7  CTA
══════════════════════════════════════════════════════ --}}
<section id="contact" class="cta-section relative py-32 overflow-hidden">
    {{-- Animated glow orbs — distinct from footer --}}
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-indigo-600/15 blur-[100px] orb-1 pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-violet-600/12 blur-[120px] orb-2 pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full bg-blue-600/8 blur-[150px] pointer-events-none"></div>
    {{-- Mesh pattern --}}
    <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(rgba(139,92,246,.6) 1px, transparent 1px); background-size: 24px 24px;"></div>
    {{-- Top glowing line --}}
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>

    <div class="relative max-w-4xl mx-auto px-5 sm:px-8 text-center" data-aos="fade-up">
        <span class="inline-block px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold uppercase tracking-widest rounded-full mb-8">Get Started Today</span>
        <h2 class="text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight tracking-tight">
            Ready to Transform<br>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-cyan-300 to-indigo-400">Your Institution?</span>
        </h2>
        <p class="text-slate-400 text-xl max-w-2xl mx-auto mb-12 leading-relaxed">
            Authorized by Bangladesh Army. Secure, scalable, and centralized for 63 institutions — and growing.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('demo.form') }}"
               class="btn-glow inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold rounded-2xl hover:bg-blue-600 hover:text-white transition-all text-[15px] group">
                Book a Presentation
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 px-8 py-5 text-white/70 hover:text-white border border-white/15 hover:border-white/30 rounded-2xl transition-all text-[15px]">
                Read Our Blog ↗
            </a>
        </div>

        {{-- Trust badges --}}
        <div class="mt-14 flex flex-wrap items-center justify-center gap-5">
            {{-- Bangladesh Army Authorized --}}
            <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-[12px] font-medium text-slate-400">
                🏛 Bangladesh Army Authorized
            </span>
            {{-- Android App badge --}}
            <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-[12px] font-medium text-slate-400">
                <svg class="w-4 h-4 text-green-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.523 15.341a.733.733 0 0 1-.733-.733V9.407a.733.733 0 0 1 1.466 0v5.201a.733.733 0 0 1-.733.733zm-11.046 0a.733.733 0 0 1-.733-.733V9.407a.733.733 0 0 1 1.466 0v5.201a.733.733 0 0 1-.733.733zM8.082 7.324l-.88-1.524a.229.229 0 0 0-.313-.084.229.229 0 0 0-.084.313l.9 1.559A6.263 6.263 0 0 0 5.5 11.5h13a6.263 6.263 0 0 0-2.205-3.912l.9-1.559a.229.229 0 0 0-.084-.313.229.229 0 0 0-.313.084l-.88 1.524A6.23 6.23 0 0 0 12 6.5a6.23 6.23 0 0 0-3.918.824zM10.25 9.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm5 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zM7 18.5v1.766A.734.734 0 0 0 7.734 21h.032A.734.734 0 0 0 8.5 20.266V18.5H7zm9.5 0v1.766a.734.734 0 0 1-.734.734h-.032a.734.734 0 0 1-.734-.734V18.5h1.5zM5.5 12v6h13v-6h-13z"/>
                </svg>
                Android App
            </span>
            {{-- Uptime --}}
            <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-[12px] font-medium text-slate-400">
                ⚡ 99.8% Uptime
            </span>
            {{-- Cloud Native --}}
            <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-[12px] font-medium text-slate-400">
                🌐 Cloud Native
            </span>
        </div>
    </div>
</section>

@endsection
