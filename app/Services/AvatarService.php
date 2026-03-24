<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public const DEFAULT_AVATAR = 'avatars/default.jpg';

    public function handle(UploadedFile $file = null, ?string $currentAvatar = null, bool $remove = false): ?string
    {
        if ($remove || $file) {
            // Delete old avatar if it's not the default one
            if ($currentAvatar && $currentAvatar !== self::DEFAULT_AVATAR && Storage::disk('public')->exists($currentAvatar)) {
                Storage::disk('public')->delete($currentAvatar);
            }
        }

        if ($file) {
            return $file->store('avatars', 'public');
        }

        if ($remove) {
            return null;
        }

        return $currentAvatar ?? self::DEFAULT_AVATAR;
    }
}
