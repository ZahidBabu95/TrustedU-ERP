<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user. Only super admins can do this.
     */
    public function startImpersonating(User $user)
    {
        $currentUser = Auth::user();

        // Only super admins can impersonate
        if (!$currentUser || !$currentUser->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Cannot impersonate yourself or another super admin
        if ($user->id === $currentUser->id || $user->isSuperAdmin()) {
            return redirect('/admin')->with('error', 'Cannot impersonate this user.');
        }

        // Check target user can access panel
        if (!$user->is_active) {
            return redirect('/admin')->with('error', 'Cannot impersonate an inactive user.');
        }

        // Store original admin info
        session()->put('impersonator_id', $currentUser->id);
        session()->put('impersonator_name', $currentUser->name);

        // Switch the authenticated user WITHOUT session regeneration
        // Session regeneration causes AuthenticateSession middleware to invalidate
        session()->put(Auth::guard('web')->getName(), $user->getKey());

        // Update the password hash in session so AuthenticateSession doesn't invalidate
        session()->put('password_hash_web', $user->getAuthPassword());

        session()->save();

        return redirect('/admin');
    }

    /**
     * Stop impersonating and return to original super admin account.
     */
    public function stopImpersonating(Request $request)
    {
        $impersonatorId = session()->get('impersonator_id');

        if (!$impersonatorId) {
            return redirect('/admin');
        }

        $originalUser = User::find($impersonatorId);

        if ($originalUser && $originalUser->isSuperAdmin()) {
            session()->forget('impersonator_id');
            session()->forget('impersonator_name');

            // Switch back without session regeneration
            session()->put(Auth::guard('web')->getName(), $originalUser->getKey());
            session()->put('password_hash_web', $originalUser->getAuthPassword());
            session()->save();
        }

        return redirect('/admin');
    }
}
