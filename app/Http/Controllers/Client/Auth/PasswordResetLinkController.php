<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return Inertia::render('Client/Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::broker('clients')->sendResetLink(
            $request->only('email')
        );

        // Always return success — don't reveal if email exists
        return back()->with('status', 'If your email exists, a reset link has been sent.');
    }
}
