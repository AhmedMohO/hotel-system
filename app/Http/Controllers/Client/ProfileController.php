<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProfileUpdateRequest;
use App\Http\Requests\ClientProfileUpdateRequest;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private readonly AvatarService $avatarService)
    {
    }

    public function edit(Request $request): Response
    {
        $client = $request->user('client');

        return Inertia::render('Client/Profile/Edit', [
            'client' => $client->only('id', 'name', 'email', 'mobile', 'country', 'gender', 'avatar_image'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $client = $request->user('client');
        $validated = $request->validated();

        if (empty($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $validated['avatar_image'] = $this->avatarService->handle(
            $request->file('avatar_image'),
            $client->avatar_image,
            $request->boolean('remove_avatar')
        );

        $client->fill($validated);
        $client->save();

        return to_route('client.profile.edit')->with('success', 'Profile updated successfully.');
    }
}


