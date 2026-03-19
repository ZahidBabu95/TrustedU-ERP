<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Stop impersonating and return to original super admin account.
     */
    public function stopImpersonating(Request $request)
    {
        $impersonatorId = session()->get('impersonator_id');

        if (!$impersonatorId) {
            return redirect('/admin');
        }

        $originalUser = \App\Models\User::find($impersonatorId);

        if ($originalUser && $originalUser->isSuperAdmin()) {
            session()->forget('impersonator_id');
            session()->forget('impersonator_name');
            Auth::login($originalUser);
        }

        return redirect('/admin');
    }
}
