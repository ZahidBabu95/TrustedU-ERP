<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\ChatBotController;
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

// ── Impersonation (outside /admin to avoid Filament route conflicts) ──
Route::middleware('auth')->get('/impersonate/{user}', [App\Http\Controllers\ImpersonationController::class, 'startImpersonating'])
    ->name('impersonation.start');
Route::middleware('auth')->get('/stop-impersonating', [App\Http\Controllers\ImpersonationController::class, 'stopImpersonating'])
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

// ── Lead Contact Report (Printable A4) ────────────────────────
Route::middleware('auth')->get('/admin/leads/{lead}/report', function (\App\Models\Lead $lead) {
    $lead->load('team', 'assignee');
    $report = $lead->contact_report ?? [];
    $erpModules = \App\Models\ErpModule::active()->ordered()->pluck('name', 'slug')->toArray();
    return view('reports.lead-contact-report', compact('lead', 'report', 'erpModules'));
})->name('lead.contact-report');

// ── Lead Proposal Report (Printable A4) ───────────────────────
Route::middleware('auth')->get('/admin/leads/{lead}/proposal', function (\App\Models\Lead $lead) {
    $lead->load('team', 'assignee');
    $erpModules = \App\Models\ErpModule::active()->ordered()->pluck('name', 'slug')->toArray();
    return view('reports.lead-proposal-report', compact('lead', 'erpModules'));
})->name('lead.proposal-report');

// ── CRM Invoice Print ─────────────────────────────────────────
Route::middleware('auth')->get('/admin/crm-invoices/{invoice}/print', function (\App\Models\CrmInvoice $invoice) {
    $invoice->load('client', 'creator');
    $invoice->calculateTotals();
    return view('reports.crm-invoice-print', compact('invoice'));
})->name('crm.invoice.print');

// ── CRM Deed / Agreement Print ────────────────────────────────
Route::middleware('auth')->get('/admin/deals/{deal}/deed-print', function (\App\Models\Deal $deal) {
    $deal->load('client', 'lead');
    $client = $deal->client;
    $company = $deal->deed_company_info ?? [
        'name'         => 'Amar School Management Software Company',
        'tagline'      => 'Manage School Easily',
        'phone'        => '+88 01793661417',
        'email'        => 'hello.amarschool@gmail.com',
        'website'      => 'www.amarschool.co',
        'address'      => 'House #192, Road #2, Avenue #3, Mirpur DOHS, Dhaka 1216, Bangladesh',
        'ceo_name'     => 'Md. Aminul Islam',
        'ceo_title'    => 'CEO',
        'product_name' => 'Amar School',
    ];
    return view('reports.crm-deed-print', compact('deal', 'client', 'company'));
})->name('crm.deed.print');

// ── Chatbot API ────────────────────────────────────────────────
Route::prefix('chatbot')->group(function () {
    Route::post('/start', [ChatBotController::class, 'startConversation']);
    Route::post('/message', [ChatBotController::class, 'sendMessage']);
    Route::post('/visitor-info', [ChatBotController::class, 'updateVisitorInfo']);
});
