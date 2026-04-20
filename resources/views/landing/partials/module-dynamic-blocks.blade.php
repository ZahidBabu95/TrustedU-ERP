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
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Features' }}</h2>
                    @if(!empty($data['section_subtitle']))
                        <p class="max-w-2xl mx-auto text-lg {{ empty($data['text_color']) ? 'text-slate-500' : 'opacity-80' }}">{{ $data['section_subtitle'] }}</p>
                    @endif
                </div>
                
                @if(!empty($data['features']))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($data['features'] as $feature)
                    <div class="bg-white/90 backdrop-blur rounded-2xl border border-slate-200 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $feature['title'] ?? '' }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $feature['description'] ?? '' }}</p>
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
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #ffffff;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <h2 class="text-3xl font-extrabold text-center mb-10 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Screenshots' }}</h2>
                
                @if(!empty($data['images']))
                <div class="flex overflow-x-auto gap-6 pb-6 scrollbar-hide snap-x">
                    @foreach($data['images'] as $img)
                    <div class="shrink-0 w-11/12 md:w-[600px] snap-center group">
                        <img src="{{ Storage::disk($storageDisk)->url($img) }}" class="w-full h-[300px] md:h-[400px] rounded-2xl shadow-xl border border-slate-200 object-contain bg-slate-50 group-hover:scale-105 transition-transform duration-500 ease-out" alt="Screenshot">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

    @elseif($block['type'] === 'video_playlist')
        <section x-data="{ shown: false }" x-intersect.half.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 ease-out {{ $paddingY }} {{ $customClasses }}" style="{{ $wrapperStyle ?: 'background-color: #f8fafc;' }}">
            <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
                <h2 class="text-3xl font-extrabold text-center mb-10 {{ empty($data['text_color']) ? 'text-slate-900' : '' }}">{{ $data['section_title'] ?? 'Video Tutorials' }}</h2>
                
                @if(!empty($data['videos']))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($data['videos'] as $vid)
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="aspect-video">
                            <iframe src="https://www.youtube.com/embed/{{ $vid['youtube_id'] }}?rel=0" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-slate-800 line-clamp-1">{{ $vid['title'] ?? 'Video' }}</h4>
                            @if(!empty($vid['description']))
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $vid['description'] }}</p>
                            @endif
                        </div>
                    </div>
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
