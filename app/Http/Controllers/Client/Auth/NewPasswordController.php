<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return Inertia::render('Client/Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $status = Password::broker('clients')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Client $client, string $password) {
                $client->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($client));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('client.login')
                ->with('status', 'Password reset successfully.')
            : back()->withErrors(['email' => __($status)]);
    }
}
