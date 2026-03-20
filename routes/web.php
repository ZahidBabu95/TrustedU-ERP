<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DemoRequestController;
use Illuminate\Support\Facades\Route;

// ── Public Landing ─────────────────────────────────────────────
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/modules/{slug}', [LandingPageController::class, 'showModule'])->name('module.show');

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

// ── Impersonation ──
Route::middleware('auth')->get('/admin/stop-impersonating', [App\Http\Controllers\ImpersonationController::class, 'stopImpersonating'])
    ->name('impersonation.stop');

// ── Notification Count API (internal, session auth) ──
Route::middleware('auth')->get('/admin/api/notification-count', function () {
    $user = auth()->user();
    $unreadCount = \App\Models\AppNotification::where('user_id', $user->id)->whereNull('read_at')->count();
    $latest = \App\Models\AppNotification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->orderByDesc('created_at')
        ->first(['id', 'title', 'message', 'action_url', 'type', 'color']);

    return response()->json([
        'unread_count' => $unreadCount,
        'latest' => $latest,
    ]);
});

// ── Dashboard Global Search API ──
Route::middleware('auth')->get('/admin/api/dashboard-search', function (\Illuminate\Http\Request $request) {
    $q = trim($request->get('q', ''));
    if (strlen($q) < 2) return response()->json([]);

    $results = [];

    // Search Clients
    $clients = \App\Models\Client::where(function ($qr) use ($q) {
        $qr->where('client_id', 'like', "%{$q}%")
           ->orWhere('name', 'like', "%{$q}%")
           ->orWhere('email', 'like', "%{$q}%")
           ->orWhere('phone', 'like', "%{$q}%");
    })->limit(5)->get();

    if ($clients->count() > 0) {
        $results[] = [
            'type' => 'Clients',
            'items' => $clients->map(fn($c) => [
                'title' => $c->name,
                'subtitle' => "ID: {$c->client_id}" . ($c->email ? " • {$c->email}" : ''),
                'url' => route('filament.admin.resources.clients.edit', $c->id),
                'icon' => '🏢',
                'iconBg' => '#ecfdf5',
                'iconColor' => '#059669',
                'badge' => $c->is_active ? 'Active' : 'Inactive',
                'badgeBg' => $c->is_active ? '#ecfdf5' : '#f1f5f9',
                'badgeColor' => $c->is_active ? '#059669' : '#64748b',
            ])->toArray(),
        ];
    }

    // Search Support Tickets
    $tickets = \App\Models\SupportTicket::where(function ($qr) use ($q) {
        $qr->where('ticket_number', 'like', "%{$q}%")
           ->orWhere('subject', 'like', "%{$q}%");
    })->limit(5)->get();

    if ($tickets->count() > 0) {
        $results[] = [
            'type' => 'Support Tickets',
            'items' => $tickets->map(fn($t) => [
                'title' => $t->subject,
                'subtitle' => $t->ticket_number . ' • ' . ucfirst($t->priority ?? 'normal'),
                'url' => route('filament.admin.resources.support-tickets.edit', $t->id),
                'icon' => '🎫',
                'iconBg' => '#fef2f2',
                'iconColor' => '#dc2626',
                'badge' => ucfirst(str_replace('_', ' ', $t->status)),
                'badgeBg' => ($t->status === 'open' ? '#eff6ff' : ($t->status === 'resolved' ? '#ecfdf5' : '#fefce8')),
                'badgeColor' => ($t->status === 'open' ? '#3b82f6' : ($t->status === 'resolved' ? '#059669' : '#ca8a04')),
            ])->toArray(),
        ];
    }

    // Search Leads
    $leads = \App\Models\Lead::where(function ($qr) use ($q) {
        $qr->where('name', 'like', "%{$q}%")
           ->orWhere('email', 'like', "%{$q}%")
           ->orWhere('phone', 'like', "%{$q}%");
    })->limit(5)->get();

    if ($leads->count() > 0) {
        $results[] = [
            'type' => 'Leads',
            'items' => $leads->map(fn($l) => [
                'title' => $l->name,
                'subtitle' => $l->email ?? $l->phone ?? '—',
                'url' => route('filament.admin.resources.leads.index'),
                'icon' => '👤',
                'iconBg' => '#f5f3ff',
                'iconColor' => '#6c5ce7',
                'badge' => ucfirst($l->status),
                'badgeBg' => '#f5f3ff',
                'badgeColor' => \App\Models\Lead::STATUS_COLORS[$l->status] ?? '#64748b',
            ])->toArray(),
        ];
    }

    return response()->json($results);
});
