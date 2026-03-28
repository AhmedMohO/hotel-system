<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisteredClientController extends Controller
{
    public function create()
    {
        $countries = Cache::remember(
            'countries',
            3600,
            fn () => collect((new \Monarobase\CountryList\CountryList)->getList('en'))
                ->map(fn ($name) => [
                    'name' => $name,
                ])->values()->toArray()
        );

        return Inertia::render('Client/Auth/Register', [
            'countries' => $countries,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:clients,email'],
            'password'     => ['required', 'min:6', 'confirmed'],
            'avatar_image' => \App\Services\AvatarService::rules(),
            'mobile'       => ['required', 'string'],
            'country'      => ['required', 'string'],
            'gender'       => ['required', 'in:Male,Female'],
        ]);

        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')
                ->store('avatars', 'public');
        }

        Client::create($data);

        return redirect()
            ->route('client.login')
            ->with('success', 'Registration successful. Please wait for approval.');
    }
}
