<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Client/Auth/Login', [
            'canResetPassword' => true,
            'canRegister' => true,
            'status' => session('status'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('client')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $client = Auth::guard('client')->user();

        if (!$client->approved_by) {
            Auth::guard('client')->logout();
            return back()->withErrors([
                'email' => 'Your account is pending approval.',
            ]);
        }

        $client->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}
