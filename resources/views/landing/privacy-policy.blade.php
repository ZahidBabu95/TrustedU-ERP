@extends('layouts.app')

@section('title', 'Privacy Policy - TrustedU ERP')

@section('content')
<main class="pt-[140px] pb-24 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 bg-white rounded-3xl shadow-sm border border-slate-100 p-8 sm:p-14">
        
        <div class="text-center mb-12 border-b border-slate-100 pb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-4">Privacy Policy</h1>
            <p class="text-slate-500 text-lg">Your data protection is our top priority.</p>
        </div>

        <div class="prose-custom text-slate-700 leading-relaxed space-y-6">
            {!! $content !!}
        </div>
        
    </div>
</main>

<style>
/* Custom typography styles since we may not have tailwind typography plugin */
.prose-custom h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a; /* slate-900 */
    margin-top: 2.5rem;
    margin-bottom: 1rem;
}
.prose-custom p {
    margin-bottom: 1.25rem;
    color: #475569; /* slate-600 */
}
.prose-custom ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
    color: #475569;
}
.prose-custom li {
    margin-bottom: 0.5rem;
}
.prose-custom strong {
    font-weight: 600;
    color: #1e293b; /* slate-800 */
}
.prose-custom a {
    color: #2563eb; /* blue-600 */
    text-decoration: underline;
}
.prose-custom a:hover {
    color: #1d4ed8; /* blue-700 */
}
</style>
@endsection
