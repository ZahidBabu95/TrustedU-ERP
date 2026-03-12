<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function store(ContactFormRequest $request): RedirectResponse
    {
        ContactMessage::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('contact_success', 'আপনার বার্তা পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।');
    }
}
