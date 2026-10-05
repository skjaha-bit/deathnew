<?php

namespace Modules\Auth\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Exceptions\LoginException;
use Modules\Auth\Http\Requests\ElectronicIdPollRequest;
use Modules\Auth\Http\Requests\ElectronicIdStartRequest;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Http\Requests\UpdateProfileRequest;
use Modules\Auth\Http\Services\AuthService;
use Modules\Auth\Models\UserEmail;
use Modules\Auth\Models\UserPhone;
use Modules\Core\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->success([
            'user' => $this->formatUser($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Registration successful.', 201);
    }

    /** Kept for older clients: logging in now always goes through electronic ID, see eid/poll. */
    public function login(LoginRequest $request)
    {
        return $this->electronicIdStart($request);
    }

    public function electronicIdStart(ElectronicIdStartRequest $request)
    {
        try {
            $result = $this->authService->startElectronicId($request->validated());
        } catch (LoginException $e) {
            return $this->error($e->getMessage(), $e->status, $e->data);
        }

        return $this->success($result, 'Confirm the sign-in on your phone. Check that it shows the same verification code.');
    }

    public function electronicIdPoll(ElectronicIdPollRequest $request)
    {
        try {
            $result = $this->authService->completeElectronicId($request->validated('auth_request_id'));
        } catch (LoginException $e) {
            return $this->error($e->getMessage(), $e->status, $e->data);
        }

        if (isset($result['pending'])) {
            return $this->success(['pending' => true], 'Waiting for the person to confirm on their phone.', 202);
        }

        return $this->loginResponse($result);
    }

    private function loginResponse(array $result)
    {
        if (! isset($result['user'])) {
            return $this->success(array_filter([
                'registered' => false,
                'person' => $result['person'],
                'registration_token' => $result['registration_token'] ?? null,
            ], fn ($value) => $value !== null), 'Kennitala is valid. No account exists yet.');
        }

        return $this->success([
            'registered' => true,
            'user' => $this->formatUser($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 'Login successful.');
    }

    public function logout()
    {
        $this->authService->logout(auth('api')->user());

        return $this->success(null, 'Logged out successfully.');
    }

    public function show(Request $request)
    {
        return $this->success($this->formatUser($request->user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->authService->updateProfile($request->user(), $request->validated());

        return $this->success($this->formatUser($user), 'Profile updated.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096']]);

        $user = $this->authService->uploadAvatar($request->user(), $request->file('avatar'));

        return $this->success($this->formatUser($user), 'Avatar updated.');
    }

    private function formatUserEmail(UserEmail $userEmail): array
    {
        return [
            'id' => $userEmail->id,
            'email' => $userEmail->email,
            'verified_at' => $userEmail->verified_at?->toIso8601String(),
            'created_at' => $userEmail->created_at?->toIso8601String(),
        ];
    }

    private function formatUserPhone(UserPhone $userPhone): array
    {
        return [
            'id' => $userPhone->id,
            'phone_code' => $userPhone->phone_code,
            'phone_number' => $userPhone->phone_number,
            'verified_at' => $userPhone->verified_at?->toIso8601String(),
            'created_at' => $userPhone->created_at?->toIso8601String(),
        ];
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_code' => $user->phone_code,
            'phone_number' => $user->phone_number,
            'kennitala' => $user->kennitala,
            'avatar' => $user->avatar ? asset('storage/'.$user->avatar) : null,
            'status' => $user->status,
            'deceased' => $user->status === User::STATUS_DECEASED,
            'deceased_at' => $user->deceased_at?->toDateString(),
            'preferred_language_id' => $user->preferred_language_id,
            'address' => $user->address,
            'house_number' => $user->house_number,
            'city' => $user->city,
            'postal_code' => $user->postal_code,
            'country_id' => $user->country_id,
            'marital_status' => $user->marital_status,
            'gender' => $user->gender,
            'newsletter_opt_in' => $user->newsletter_opt_in,
            'emails' => $user->emails->map(fn (UserEmail $e) => $this->formatUserEmail($e))->all(),
            'phones' => $user->phones->map(fn (UserPhone $p) => $this->formatUserPhone($p))->all(),
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}
