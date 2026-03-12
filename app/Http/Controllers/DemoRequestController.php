<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemoRequestFormRequest;
use App\Models\DemoRequest;
use Illuminate\Http\RedirectResponse;

class DemoRequestController extends Controller
{
    public function store(DemoRequestFormRequest $request): RedirectResponse
    {
        DemoRequest::create($request->validated());

        return back()->with('demo_success', 'আপনার ডেমো রিকোয়েস্ট পাঠানো হয়েছে! আমাদের টিম শীঘ্রই যোগাযোগ করবে।');
    }
}
