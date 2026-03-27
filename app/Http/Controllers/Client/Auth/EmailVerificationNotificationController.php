<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user('client')->hasVerifiedEmail()) {
            return redirect()->route('client.dashboard');
        }

        $request->user('client')->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent.');
    }
}
