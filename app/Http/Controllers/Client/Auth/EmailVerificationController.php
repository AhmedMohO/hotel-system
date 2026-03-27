<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return Inertia::render('Client/Auth/VerifyEmail', [
            'status' => session('status'),
        ]);
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user('client')->hasVerifiedEmail()) {
            return redirect()->route('client.dashboard');
        }

        if ($request->user('client')->markEmailAsVerified()) {
            event(new Verified($request->user('client')));
        }

        return redirect()->route('client.dashboard');
    }
}
