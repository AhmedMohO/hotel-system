<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConfirmablePasswordController extends Controller
{
    public function show()
    {
        return Inertia::render('Client/Auth/ConfirmPassword');
    }

    public function store(Request $request)
    {
        if (!Auth::guard('client')->validate([
            'email'    => $request->user()->email,
            'password' => $request->password,
        ])) {
            return back()->withErrors([
                'password' => 'The provided password was incorrect.',
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('client.dashboard'));
    }
}
