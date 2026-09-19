<?php

namespace Modules\Auth\Http\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Models\UserEmail;
use Modules\Auth\Models\UserPhone;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'phone_code' => $data['phone_code'],
            'phone_number' => $data['phone_number'],
            'kennitala' => $data['kennitala'],
            'country_id' => $data['country_id'] ?? null,
        ]);

        $token = $user->createToken('FYRIRHYGGJA')->accessToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Look the user up by kennitala or phone number and log them straight in.
     * No password, no OTP for now.
     */
    public function login(array $data): array
    {
        $query = User::query();

        if (! empty($data['kennitala'])) {
            $query->where('kennitala', $data['kennitala']);
        } else {
            $query->where('phone_code', $data['phone_code'])
                ->where('phone_number', $data['phone_number']);
        }

        $user = $query->first();

        if (! $user) {
            throw new AuthenticationException('Doesn\'t match to any existing account.');
        }

        $token = $user->createToken('FYRIRHYGGJA')->accessToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->token()?->revoke();
    }

    public function updateProfile(User $user, array $data): User
    {
        $emails = $data['emails'] ?? null;
        $phones = $data['phones'] ?? null;
        unset($data['emails'], $data['phones']);

        $user->update($data);

        if ($emails !== null) {
            $this->syncEmails($user, $emails);
        }

        if ($phones !== null) {
            $this->syncPhones($user, $phones);
        }

        return $user->fresh();
    }

    /**
     * Replace the user's additional emails with exactly this set, skipping
     * the primary email and anything already claimed by another user.
     */
    public function syncEmails(User $user, array $emails): Collection
    {
        $emails = array_values(array_unique(array_diff($emails, [$user->email])));

        $user->emails()->whereNotIn('email', $emails)->delete();

        $existing = $user->emails()->pluck('email')->all();
        $taken = UserEmail::query()->where('user_id', '!=', $user->id)->pluck('email')->all();

        foreach (array_diff($emails, $existing, $taken) as $email) {
            $user->emails()->create(['email' => $email]);
        }

        return $user->emails()->get();
    }

    /**
     * Replace the user's additional phones with exactly this set, skipping
     * the primary phone and anything already claimed by another user.
     */
    public function syncPhones(User $user, array $phones): Collection
    {
        $isSame = fn (array $a, $b) => $a['phone_code'] === $b->phone_code && $a['phone_number'] === $b->phone_number;
        $isPrimary = fn (array $p) => $p['phone_code'] === $user->phone_code && $p['phone_number'] === $user->phone_number;

        $wanted = array_values(array_filter($phones, fn (array $p) => ! $isPrimary($p)));

        foreach ($user->phones as $existing) {
            $stillWanted = collect($wanted)->contains(fn (array $p) => $isSame($p, $existing));

            if (! $stillWanted) {
                $existing->delete();
            }
        }

        $current = $user->phones()->get();

        foreach ($wanted as $phone) {
            $alreadyMine = $current->contains(fn (UserPhone $existing) => $isSame($phone, $existing));
            $takenByOther = UserPhone::query()
                ->where('user_id', '!=', $user->id)
                ->where('phone_code', $phone['phone_code'])
                ->where('phone_number', $phone['phone_number'])
                ->exists();

            if (! $alreadyMine && ! $takenByOther) {
                $user->phones()->create($phone);
            }
        }

        return $user->phones()->get();
    }

    public function uploadAvatar(User $user, UploadedFile $file): User
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->storeResizedAvatar($file);
        $user->update(['avatar' => $path]);

        return $user->fresh();
    }

    private function storeResizedAvatar(UploadedFile $file, int $maxSize = 400): string
    {
        $realPath = $file->getRealPath();
        [$width, $height, $type] = getimagesize($realPath);

        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG => imagecreatefrompng($realPath),
            IMAGETYPE_GIF => imagecreatefromgif($realPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($realPath),
            default => throw ValidationException::withMessages(['avatar' => 'Unsupported image type.']),
        };

        $ratio = min($maxSize / $width, $maxSize / $height, 1);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF], true)) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $extension = match ($type) {
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
            default => 'jpg',
        };

        ob_start();
        match ($type) {
            IMAGETYPE_PNG => imagepng($resized),
            IMAGETYPE_GIF => imagegif($resized),
            IMAGETYPE_WEBP => imagewebp($resized),
            default => imagejpeg($resized, null, 85),
        };
        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($resized);

        $filename = 'avatars/'.Str::uuid().'.'.$extension;
        Storage::disk('public')->put($filename, $contents);

        return $filename;
    }
}
