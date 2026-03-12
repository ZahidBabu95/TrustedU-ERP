@extends('layouts.app')

@section('title', 'Request a Demo - TrustedU ERP')

@section('content')
<main class="relative bg-slate-50 min-h-screen pt-[120px] pb-24 overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute top-0 inset-x-0 h-[500px] bg-gradient-to-b from-blue-100/50 via-slate-50 to-slate-50"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400/10 blur-[100px] rounded-full point-events-none"></div>
    <div class="absolute top-20 -left-20 w-72 h-72 bg-indigo-400/10 blur-[80px] rounded-full point-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
        <div class="grid lg:grid-cols-5 gap-12 lg:gap-16 items-start">
            
            {{-- Left column: Info & Value prop --}}
            <div class="lg:col-span-2 lg:sticky lg:top-32 text-center lg:text-left pt-8">
                <span class="inline-block px-4 py-1.5 bg-blue-100/50 border border-blue-200 text-blue-700 text-[12px] font-bold uppercase tracking-widest rounded-full mb-6 relative">
                    <span class="absolute inset-0 rounded-full bg-blue-400/20 animate-ping"></span>
                    Get a Live Tour
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-6 leading-tight tracking-tight">
                    Transform your institution with <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">TrustedU ERP</span>
                </h1>
                <p class="text-slate-600 text-lg mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    See exactly how our platform can streamline your administration, boost fee collections, and improve parent-teacher communication in a personalized demo.
                </p>

                <div class="space-y-6 text-left max-w-md mx-auto lg:mx-0 hidden md:block">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-slate-900 font-semibold">1-on-1 Expert Guidance</h4>
                            <p class="text-slate-500 text-sm mt-1">Our specialists will tailor the demo to your specific institutional needs and challenges.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-slate-900 font-semibold">Fast & Secure Migration</h4>
                            <p class="text-slate-500 text-sm mt-1">Learn how we securely transition your existing data with zero downtime.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: The Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-[2rem] shadow-2xl shadow-blue-900/10 border border-slate-100 p-6 sm:p-10 relative overflow-hidden backdrop-blur-xl">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
                    
                    <h3 class="text-2xl font-bold text-slate-800 mb-8">Request your free demo</h3>

                    @if(session('success'))
                        <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h4 class="font-bold">Request Submitted!</h4>
                                <p class="text-sm mt-1">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('demo.store') }}" method="POST" class="space-y-8">
                        @csrf
                        
                        {{-- 1. Contact Person --}}
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-3">
                                <span class="w-6 h-px bg-slate-200"></span> Contact Details
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Your Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" required
                                           class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all">
                                    @error('contact_name') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required
                                           class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all">
                                    @error('phone') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                           class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all">
                                    @error('email') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 2. Institution --}}
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-3">
                                <span class="w-6 h-px bg-slate-200"></span> Institution Info
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Institution Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="institution_name" value="{{ old('institution_name') }}" required
                                           class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all">
                                    @error('institution_name') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Institution Type <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="institution_type" class="w-full appearance-none bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 pr-10 text-slate-700 transition-all font-medium">
                                            <option value="school" {{ old('institution_type') == 'school' ? 'selected' : '' }}>School</option>
                                            <option value="college" {{ old('institution_type') == 'college' ? 'selected' : '' }}>College</option>
                                            <option value="madrasha" {{ old('institution_type') == 'madrasha' ? 'selected' : '' }}>Madrasha</option>
                                            <option value="university" {{ old('institution_type') == 'university' ? 'selected' : '' }}>University</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    @error('institution_type') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">District</label>
                                    <input type="text" name="district" value="{{ old('district') }}"
                                           class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- 3. Needs --}}
                        <div>
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-5 flex items-center gap-3">
                                <span class="w-6 h-px bg-slate-200"></span> Interested Modules
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @if(isset($modules) && $modules->count() > 0)
                                    @foreach($modules as $module)
                                    <label class="relative flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-blue-50 hover:border-blue-200 cursor-pointer transition-all group has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                                        <input type="checkbox" name="interested_modules[]" value="{{ $module->name }}" 
                                               class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 focus:ring-offset-0"
                                               {{ (is_array(old('interested_modules')) && in_array($module->name, old('interested_modules'))) ? 'checked' : '' }}>
                                        <span class="ml-3 text-[13px] font-semibold text-slate-700 group-hover:text-blue-700">{{ $module->name }}</span>
                                    </label>
                                    @endforeach
                                @else
                                    <p class="text-sm text-slate-500 col-span-full">No modules configured right now.</p>
                                @endif
                            </div>
                            @error('interested_modules') <p class="mt-2 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Any specific requirements or notes?</label>
                            <textarea name="notes" rows="3"
                                      class="w-full bg-slate-50 border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 rounded-xl px-4 py-3 text-slate-700 transition-all resize-none">{{ old('notes') }}</textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 px-8 rounded-xl text-white font-bold text-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transform hover:-translate-y-0.5 transition-all">
                                Request Live Demo
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                            <p class="text-center text-xs text-slate-400 mt-4">By submitting, you agree to our <a href="{{ route('privacy-policy') }}" class="underline hover:text-slate-600">Privacy Policy</a>.</p>
                        </div>
                    </form>

                </div>
            </div>
            
        </div>
    </div>
</main>

<style>
/* Polyfill for :has selector in older tailwind/browsers if not natively supported, 
   though modern browsers support it well. The 'has-[:checked]' relies on TW arbitrary variants 
   which is supported in Tailwind v3.2+. */
</style>
@endsection
