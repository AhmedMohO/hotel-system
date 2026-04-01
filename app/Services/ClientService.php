<?php

namespace App\Services;

use App\Models\Client;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    // ─── Approval ─────────────────────────────────────────────────────────────

    public function approve(Client $client): void
    {
        $client->approved_by = Auth::id();
        $client->approved_at = now();
        $client->save();

        // Dispatch queued notification
        $client->notify(new ClientApprovedNotification);
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function create(array $data): Client
    {
        $data['password'] = Hash::make($data['password']);
        $data['avatar_image'] = $this->handleAvatarUpload($data['avatar_image'] ?? null);

        return Client::create($data);
    }

    public function update(Client $client, array $data): Client
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (! empty($data['avatar_image']) && $data['avatar_image'] instanceof UploadedFile) {
            $this->deleteOldAvatar($client->avatar_image);
            $data['avatar_image'] = $this->handleAvatarUpload($data['avatar_image']);
        } else {
            unset($data['avatar_image']);
        }

        $client->update($data);

        return $client->fresh();
    }

    public function delete(Client $client): void
    {
        $this->deleteOldAvatar($client->avatar_image);
        $client->delete();
    }

    // ─── Avatar Helpers ───────────────────────────────────────────────────────

    private function handleAvatarUpload(?UploadedFile $file): string
    {
        if (! $file) {
            return 'avatars/default.png';
        }

        return $file->store('avatars', 'public');
    }

    private function deleteOldAvatar(?string $path): void
    {
        if ($path && $path !== 'avatars/default.png') {
            Storage::disk('public')->delete($path);
        }
    }
}
