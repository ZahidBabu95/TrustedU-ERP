@foreach($module->dynamic_sections as $block)
    @php
        $storageDisk = 'public'; // Landing page assets always loaded from local storage

        $data = $block['data'];
        
        // Dynamic Design Settings Processor
        $bgColor = !empty($data['bg_color']) ? "background-color: {$data['bg_color']};" : '';
        $textColor = !empty($data['text_color']) ? "color: {$data['text_color']};" : '';
        $paddingY = $data['padding_y'] ?? 'py-20';
        $customClasses = $data['custom_classes'] ?? '';
        $wrapperStyle = trim($bgColor . ' ' . $textColor);
    @endphp

    @if($block['type'] === 'hero')
        <section class="relative pt-28 {{ $paddingY }} lg:pb-32 overflow-hidden flex items-center {{ $customClasses }}"
                 style="{{ $wrapperStyle ?: 'background: linear-gradient(135deg, #0d1b3e 0%, #111827 30%, #1a1040 60%, #0d1b3e 100%);' }}">
            @if(empty($data['bg_color']))
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(rgba(99,102,241,.5) 1px, transparent 1px); background-size: 32px 32px;"></div>
            @endif
            
            <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 w-full z-10">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                    <div class="flex-1 max-w-2xl text-center lg:text-left">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl lg:leading-tight font-extrabold mb-5 leading-[1.1] slide-up-1 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 hover:scale-[1.02] transition-transform duration-500">
                            {{ $data['title'] ?? '' }}
                        </h1>
                        @if(!empty($data['subtitle']))
                        <p class="text-xl sm:text-2xl font-medium mb-8 leading-relaxed slide-up-2 {{ empty($data['text_color']) ? 'text-blue-200' : 'opacity-80' }}">
                            {{ $data['subtitle'] }}
                        </p>
                        @endif

                        @if(!empty($data['buttons']))
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 slide-up-3">
                            @foreach($data['buttons'] as $btn)
                                @php
                                    $btnCustomColor = !empty($btn['button_color']) ? "background-color: {$btn['button_color']} !important; color: white;" : "";
                                @endphp
                                @if(isset($btn['style']) && $btn['style'] === 'outline')
                                    <a href="{{ $btn['url'] }}" style="{{ $btnCustomColor ? 'border-color: '.$btn['button_color'].'; color: '.$btn['button_color'].';' : '' }}" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm border border-blue-400/50 text-blue-300 hover:bg-blue-500/10 transition-all shadow-md">
                                        {{ $btn['label'] }}
                                    </a>
                                @else
                                    <a href="{{ $btn['url'] }}" style="{{ $btnCustomColor }}" class="btn-shine inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-white text-sm bg-blue-600 hover:bg-blue-700 hover:scale-105 transition-all shadow-xl shadow-blue-600/30">
                                        {{ $btn['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    
                    @if(!empty($data['image']))
                    <div class="flex-1 max-w-xl w-full scale-in-1 group">
                        <img src="{{ Storage::disk($storageDisk)->url($data['image']) }}" class="w-full h-auto rounded-3xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.7)] border border-white/20 group-hover:scale-[1.07] group-hover:-rotate-1 transition-all duration-700 ease-in-out" alt="Hero Image">
                    </div>
                    @endif
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-slate-50/10 to-transparent"></div>
        </section>

    @elseif($block['type'] === 'features_grid')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out relative overflow-hidden {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <!-- Background Decoration -->
            <div class="absolute inset-0 z-0 pointer-events-none opacity-50" style="background-image: radial-gradient(rgba(100, 116, 139, 0.08) 1px, transparent 1px); background-size: 32px 32px;"></div>
            
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-extrabold mb-4 tracking-tight {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Key Features' }}</h2>
                    @if(!empty($data['section_subtitle']))
                        <p class="max-w-2xl mx-auto text-lg md:text-xl {{ empty($data['text_color']) ? 'text-slate-500' : 'opacity-80' }} leading-relaxed">{{ $data['section_subtitle'] }}</p>
                    @endif
                </div>
                
                @if(!empty($data['features']))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($data['features'] as $feature)
                    <div class="group relative bg-white rounded-3xl border border-slate-100 p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(37,99,235,0.08)] hover:-translate-y-2 transition-all duration-500 overflow-hidden">
                        <!-- Card Hover Decoration -->
                        <div class="absolute -top-12 -right-12 w-40 h-40 bg-gradient-to-br from-blue-100 to-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 blur-2xl"></div>
                        
                        <div class="relative z-10">
                            <!-- Icon -->
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-gradient-to-tr from-blue-600 to-indigo-500 text-white mb-8 shadow-lg shadow-blue-500/30 transform group-hover:rotate-6 group-hover:scale-105 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            
                            <!-- Text -->
                            <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">{{ $feature['title'] ?? '' }}</h3>
                            <p class="text-slate-500 text-sm md:text-base leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'rich_content')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #ffffff;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="flex flex-col {{ ($data['image_position'] ?? 'right') === 'left' ? 'lg:flex-row-reverse' : (($data['image_position'] ?? 'right') === 'top' ? '' : 'lg:flex-row') }} gap-12 items-center">
                    
                    <div class="flex-1 w-full prose prose-lg prose-blue max-w-none {{ empty($data['text_color']) ? '' : 'prose-headings:text-inherit prose-p:text-inherit' }}">
                        @if(!empty($data['section_title']))
                            <h2 class="text-3xl sm:text-4xl font-extrabold mb-6 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] }}</h2>
                        @endif
                        {!! $data['content'] ?? '' !!}
                    </div>

                    @if(!empty($data['image']))
                    <div class="flex-1 w-full {{ ($data['image_position'] ?? 'right') === 'top' ? 'mb-10 w-full max-w-4xl mx-auto' : '' }}">
                        <img src="{{ Storage::disk($storageDisk)->url($data['image']) }}" class="w-full h-auto rounded-2xl shadow-xl" alt="Content Image">
                    </div>
                    @endif
                </div>
            </div>
        </section>

    @elseif($block['type'] === 'pricing')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Pricing Plans' }}</h2>
                    @if(!empty($data['section_subtitle']))
                        <p class="max-w-2xl mx-auto text-lg {{ empty($data['text_color']) ? 'text-slate-500' : 'opacity-80' }}">{{ $data['section_subtitle'] }}</p>
                    @endif
                </div>
                
                @if(!empty($data['plans']))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ count($data['plans']) > 3 ? 3 : count($data['plans']) }} gap-8 justify-center">
                    @foreach($data['plans'] as $plan)
                    <div class="relative bg-white rounded-3xl border {{ !empty($plan['is_popular']) ? 'border-blue-500 shadow-2xl shadow-blue-500/20 scale-105 z-10' : 'border-slate-200 shadow-lg' }} p-8 flex flex-col">
                        @if(!empty($plan['is_popular']))
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                            Most Popular
                        </div>
                        @endif
                        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $plan['name'] ?? '' }}</h3>
                        <div class="text-4xl font-extrabold text-slate-900 mb-2">{{ $plan['price'] ?? '' }}</div>
                        <p class="text-sm text-slate-500 mb-8">{{ $plan['subtext'] ?? '' }}</p>

                        @if(!empty($plan['features']))
                        <ul class="space-y-4 mb-8 flex-1">
                            @foreach($plan['features'] as $feat)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-slate-700 text-sm">{{ $feat }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <a href="{{ $plan['button_url'] ?? '#' }}" class="block w-full text-center px-6 py-3 rounded-xl font-bold transition-all {{ !empty($plan['is_popular']) ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-slate-100 text-slate-900 hover:bg-slate-200' }}">
                            {{ $plan['button_label'] ?? 'Get Started' }}
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'gallery')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out relative z-10 {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #fafbfc;' }}">
            <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 relative">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-center mb-14 tracking-tight {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Templates & Previews' }}</h2>
                
                @if(!empty($data['images']))
                <div class="relative">
                    <!-- Smooth Gradients for edges -->
                    <div class="absolute left-0 top-0 bottom-0 w-8 md:w-20 bg-gradient-to-r from-[{{ $data['bg_color'] ?? '#fafbfc' }}] to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-8 md:w-20 bg-gradient-to-l from-[{{ $data['bg_color'] ?? '#fafbfc' }}] to-transparent z-10 pointer-events-none"></div>
                    
                    <div class="flex overflow-x-auto gap-8 md:gap-12 pb-12 pt-6 px-4 scrollbar-hide snap-x snap-mandatory">
                        @foreach($data['images'] as $img)
                        <div class="shrink-0 w-[85vw] sm:w-[600px] lg:w-[700px] snap-center group relative cursor-pointer">
                            <!-- Premium Glass Container -->
                            <div class="relative bg-white/60 backdrop-blur-md rounded-3xl p-3 sm:p-5 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-slate-200 hover:shadow-[0_20px_50px_rgb(37,99,235,0.15)] hover:-translate-y-2 transition-all duration-700 ease-out overflow-hidden flex justify-center items-center">
                                <img src="{{ Storage::disk($storageDisk)->url($img) }}" class="w-full h-[400px] md:h-[600px] rounded-2xl object-contain bg-white" alt="Template Preview">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'video_playlist')
        @php
            $videos = $data['videos'] ?? [];
            $featuredVideo = null;
            $playlistVideos = [];
            foreach($videos as $vid) {
                if(!empty($vid['is_featured']) && !$featuredVideo) {
                    $featuredVideo = $vid;
                } else {
                    $playlistVideos[] = $vid;
                }
            }
            if(!$featuredVideo && count($videos) > 0) {
                $featuredVideo = $videos[0];
                $playlistVideos = array_slice($videos, 1);
            }
            $isDark = empty($data['bg_color']); 
            
            // Smart Fallback: if user put text inside video description instead of the section subtitle
            $subtitleToUse = !empty($data['section_subtitle']) ? $data['section_subtitle'] : ($featuredVideo['description'] ?? '');
            $vidDesc = ($subtitleToUse === ($featuredVideo['description'] ?? '')) ? null : ($featuredVideo['description'] ?? '');

            if (!function_exists('getYoutubeIdFromUrl')) {
                function getYoutubeIdFromUrl($url) {
                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
                    return $match[1] ?? $url;
                }
            }
        @endphp

        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }} relative overflow-hidden" style="{{ $wrapperStyle ?: 'background-color: #050b14; color: white;' }}">
            
            <!-- Premium Background Effects -->
            @if($isDark)
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-1/4 left-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl mix-blend-screen"></div>
                <div class="absolute bottom-1/4 right-0 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl mix-blend-screen"></div>
                <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 32px 32px;"></div>
            </div>
            @endif

            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 relative z-10">
                
                @if($featuredVideo)
                <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 items-center mb-24">
                    <!-- Left Content -->
                    <div class="flex-1 w-full text-center lg:text-left {{ empty($data['text_color']) ? ($isDark ? 'text-white' : 'text-slate-900') : '' }}">
                        
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-semibold mb-6 shadow-inner shadow-blue-500/10">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            Tutorial Showcase
                        </div>

                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-8 leading-[1.15] tracking-tight text-transparent bg-clip-text bg-gradient-to-r {{ $isDark ? 'from-white via-slate-200 to-slate-400' : 'from-slate-900 via-slate-800 to-slate-600' }}">
                            {{ $data['section_title'] ?? 'Experience the Platform' }}
                        </h2>
                        
                        @if(!empty($subtitleToUse))
                        <p class="text-lg md:text-xl mb-10 {{ empty($data['text_color']) ? ($isDark ? 'text-slate-300' : 'text-slate-600') : 'opacity-80' }} leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            <!-- Parse Checkmarks roughly if they pasted it here -->
                            {!! str_replace('✓', '<br><span class="text-blue-400 font-bold">✓</span>', e($subtitleToUse)) !!}
                        </p>
                        @endif

                        @if(!empty($data['features']) && count($data['features']) > 0)
                        <ul class="space-y-5 mb-10 mx-auto lg:mx-0 inline-block text-left w-full max-w-sm">
                            @foreach($data['features'] as $feature)
                            <li class="flex items-start gap-4">
                                <div class="mt-1 w-7 h-7 rounded-full bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/40 border border-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-lg font-medium {{ empty($data['text_color']) ? ($isDark ? 'text-slate-200' : 'text-slate-700') : 'opacity-90' }}">{{ $feature['text'] ?? '' }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @if(!empty($data['button_label']) && !empty($data['button_url']))
                        <div class="flex justify-center lg:justify-start">
                            <a href="{{ $data['button_url'] }}" class="group relative inline-flex items-center gap-3 px-8 py-4 rounded-xl font-bold text-white text-base overflow-hidden bg-blue-600 transition-all hover:scale-105 hover:shadow-[0_0_40px_rgba(37,99,235,0.4)] border border-blue-500/50">
                                <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 transition-all group-hover:scale-110"></span>
                                <svg class="relative w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="relative">{{ $data['button_label'] }}</span>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Right Featured Video -->
                    <div class="flex-1 w-full max-w-3xl relative">
                        <div class="relative z-10 p-2 sm:p-3 rounded-[2rem] bg-slate-800/40 border border-slate-700/50 backdrop-blur-xl shadow-2xl">
                            <!-- Premium Glow behind iframe -->
                            <div class="absolute -inset-4 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600 rounded-[3rem] opacity-20 blur-2xl group-hover:opacity-40 transition duration-700 pointer-events-none"></div>
                            
                            <div class="relative bg-black/80 rounded-[1.5rem] overflow-hidden aspect-video shadow-inner shadow-white/10 ring-1 ring-white/10">
                                <iframe src="https://www.youtube.com/embed/{{ getYoutubeIdFromUrl($featuredVideo['youtube_id']) }}?rel=0" class="w-full h-full absolute inset-0" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                        @if(!empty($vidDesc))
                        <div class="mt-6 text-center lg:text-left ml-4">
                            <p class="{{ empty($data['text_color']) ? ($isDark ? 'text-slate-400' : 'text-slate-600') : 'opacity-80' }} text-sm flex items-center justify-center lg:justify-start gap-2">
                                <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {!! str_replace('✓', '<br><span class="text-blue-400 font-bold">✓</span>', e($vidDesc)) !!}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                    <!-- Fallback if no videos exist but title does -->
                    <h2 class="text-3xl font-extrabold text-center mb-10 {{ empty($data['text_color']) ? ($isDark ? 'text-white' : 'text-slate-900') : '' }}">{{ $data['section_title'] ?? 'Video Tutorials' }}</h2>
                @endif
                
                <!-- Playlist Sub-grid -->
                @if(count($playlistVideos) > 0)
                    <div class="border-t {{ $isDark ? 'border-slate-800/80' : 'border-slate-200' }} pt-12 mt-12 mb-6">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl font-bold {{ empty($data['text_color']) ? ($isDark ? 'text-white' : 'text-slate-900') : '' }}">More Video Tutorials</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($playlistVideos as $vid)
                            <div class="{{ $isDark ? 'bg-white/5 border-slate-700/50' : 'bg-white border-slate-200' }} border rounded-2xl overflow-hidden shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all group">
                                <div class="aspect-video relative overflow-hidden bg-black">
                                    <iframe src="https://www.youtube.com/embed/{{ getYoutubeIdFromUrl($vid['youtube_id']) }}?rel=0" class="w-full h-full relative z-10" frameborder="0" allowfullscreen loading="lazy"></iframe>
                                </div>
                                <div class="p-5">
                                    <h4 class="font-bold text-lg mb-2 {{ empty($data['text_color']) ? ($isDark ? 'text-white' : 'text-slate-900') : '' }} group-hover:text-blue-500 transition-colors line-clamp-1">{{ $vid['title'] ?? 'Video' }}</h4>
                                    @if(!empty($vid['description']))
                                    <p class="text-sm {{ empty($data['text_color']) ? ($isDark ? 'text-slate-400' : 'text-slate-500') : 'opacity-80' }} line-clamp-2">{{ $vid['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'resources')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }} relative overflow-hidden" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <!-- Background mesh -->
            <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%230f172a\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>

            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-extrabold mb-4 tracking-tight {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Download Resources' }}</h2>
                    @if(!empty($data['section_subtitle']))
                        <p class="max-w-2xl mx-auto text-lg md:text-xl {{ empty($data['text_color']) ? 'text-slate-500' : 'opacity-80' }} leading-relaxed">{{ $data['section_subtitle'] }}</p>
                    @endif
                </div>
                
                @if(!empty($data['items']))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($data['items'] as $item)
                        @php
                            $isExternal = !empty($item['external_url']);
                            $url = $isExternal ? $item['external_url'] : (!empty($item['file']) ? Storage::disk($storageDisk)->url($item['file']) : '#');
                            $isPdf = !$isExternal && !empty($item['file']) && Str::endsWith($item['file'], '.pdf');
                            $isZip = !$isExternal && !empty($item['file']) && Str::endsWith($item['file'], '.zip');
                        @endphp
                        <a href="{{ $url }}" target="_blank" class="group relative flex items-center p-6 bg-white rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgba(37,99,235,0.08)] hover:-translate-y-2 transition-all duration-500 overflow-hidden z-10">
                            <!-- Hover Gradient Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                            
                            <!-- Premium Icon Box -->
                            <div class="w-16 h-16 rounded-[1.25rem] flex items-center justify-center shrink-0 mr-6 transition-all duration-500 border border-white shadow-inner shadow-slate-200 {{ $isExternal ? 'bg-gradient-to-br from-green-100 to-green-50 text-green-600 group-hover:from-green-500 group-hover:to-emerald-600 group-hover:text-white group-hover:shadow-green-500/30' : ($isPdf ? 'bg-gradient-to-br from-red-100 to-red-50 text-red-600 group-hover:from-red-500 group-hover:to-rose-600 group-hover:text-white group-hover:shadow-red-500/30' : 'bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white group-hover:shadow-blue-500/30') }}">
                                @if($isExternal)
                                    <svg class="w-8 h-8 transform group-hover:-rotate-12 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @elseif($isPdf)
                                    <svg class="w-8 h-8 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg class="w-8 h-8 group-hover:translate-y-1 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                @endif
                            </div>
                            
                            <!-- Text Details -->
                            <div class="flex-1 overflow-hidden">
                                <h4 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition-colors truncate">{{ $item['title'] ?? 'Download File' }}</h4>
                                @if(!empty($item['description']))
                                    <p class="text-sm font-medium text-slate-500 mt-1 truncate">{{ $item['description'] }}</p>
                                @elseif($isExternal)
                                    <p class="text-sm font-medium text-slate-400 mt-1 truncate">External Resource Link</p>
                                @else
                                    <p class="text-sm font-medium text-slate-400 mt-1 truncate uppercase flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isPdf ? 'bg-red-400' : 'bg-blue-400' }}"></span>
                                        {{ pathinfo($item['file'], PATHINFO_EXTENSION) }} Format
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Download Arrow / Link Arrow -->
                            <div class="w-10 flex items-center justify-end text-slate-300 group-hover:text-blue-600 transition-colors">
                                @if($isExternal)
                                    <svg class="w-6 h-6 group-hover:translate-x-1 group-hover:-translate-y-1 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                @else
                                    <svg class="w-6 h-6 group-hover:translate-y-1 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'testimonials')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #ffffff;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <h2 class="text-3xl font-extrabold text-center mb-10 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Testimonials' }}</h2>
                
                @if(!empty($data['reviews']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($data['reviews'] as $rev)
                    <div class="bg-slate-50/80 backdrop-blur rounded-2xl p-8 border border-slate-100 shadow-sm relative">
                        <div class="absolute top-6 right-8 text-6xl text-slate-200">"</div>
                        <p class="text-slate-700 italic text-lg mb-6 relative z-10">"{{ $rev['review'] ?? '' }}"</p>
                        <div class="flex items-center gap-4 relative z-10">
                            @if(!empty($rev['avatar']))
                                <img src="{{ Storage::disk($storageDisk)->url($rev['avatar']) }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-full bg-indigo-100 border-2 border-white shadow-sm flex items-center justify-center text-indigo-600 font-bold text-xl">
                                    {{ substr($rev['client_name'] ?? 'C', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $rev['client_name'] ?? '' }}</h4>
                                <p class="text-sm text-slate-500">{{ $rev['designation'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'faqs')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <div class="max-w-3xl mx-auto px-5 sm:px-8">
                <h2 class="text-3xl font-extrabold text-center mb-10 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'FAQs' }}</h2>
                
                @if(!empty($data['questions']))
                <div class="space-y-4" x-data="{ active: null }">
                    @foreach($data['questions'] as $index => $faq)
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <button @click="active === {{ $index }} ? active = null : active = {{ $index }}" class="w-full px-6 py-5 flex items-center justify-between font-bold text-slate-800 focus:outline-none hover:bg-slate-50 transition-colors text-left">
                            <span class="text-lg">{{ $faq['question'] ?? '' }}</span>
                            <svg class="w-5 h-5 text-indigo-500 transform transition-transform" :class="active === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === {{ $index }}" x-collapse class="px-6 pb-5 text-slate-600 text-base">
                            {{ $faq['answer'] ?? '' }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'cta_banner')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out relative overflow-hidden {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0c1a3e 70%, #0f172a 100%);' }}">
            @if(empty($data['bg_color']))
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(rgba(99,102,241,.5) 1px, transparent 1px); background-size: 28px 28px;"></div>
            @endif
            <div class="relative max-w-4xl mx-auto px-5 text-center">
                <h2 class="text-4xl font-extrabold mb-5 {{ empty($data['text_color']) ? 'text-white' : '' }}">{{ $data['title'] ?? '' }}</h2>
                @if(!empty($data['subtitle']))
                <p class="text-xl mb-10 max-w-2xl mx-auto {{ empty($data['text_color']) ? 'text-slate-300' : 'opacity-90' }}">{{ $data['subtitle'] }}</p>
                @endif
                
                @if(!empty($data['buttons']))
                <div class="flex flex-wrap items-center justify-center gap-4">
                    @foreach($data['buttons'] as $btn)
                        @php
                            $btnCustomColor = !empty($btn['button_color']) ? "background-color: {$btn['button_color']} !important; color: white;" : "";
                        @endphp
                        <a href="{{ $btn['url'] }}" style="{{ $btnCustomColor }}" class="btn-shine inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-white text-base bg-blue-600 hover:bg-blue-700 hover:scale-105 transition-all shadow-xl shadow-blue-600/30">
                            {{ $btn['label'] }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
    @endif
@endforeach
