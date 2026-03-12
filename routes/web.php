<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DemoRequestController;
use Illuminate\Support\Facades\Route;

// ── Public Landing ─────────────────────────────────────────────
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// ── Blog ────────────────────────────────────────────────────────
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/demo-request', function () {
    $modules = \App\Models\ErpModule::where('is_active', true)->orderBy('sort_order')->get();
    return view('landing.demo-form', compact('modules'));
})->name('demo.form');

Route::get('/privacy-policy', function () {
    $content = \App\Models\Setting::where('key', 'privacy_policy')->value('value') ?? 'Privacy Policy content not set.';
    return view('landing.privacy-policy', compact('content'));
})->name('privacy-policy');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo.store');
});
