<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisteredClientController extends Controller
{
    public function create()
    {
        $countriesList = (new \Monarobase\CountryList\CountryList)->getList('en');
        $countries = collect($countriesList)->map(function ($name, $iso2) {
            return [
                'name' => $name,
                'iso2' => $iso2,
                'emoji' => implode('', array_map(fn($c) => mb_chr(mb_ord($c) + 127397), str_split(strtoupper($iso2)))),
            ];
        })->values();
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
