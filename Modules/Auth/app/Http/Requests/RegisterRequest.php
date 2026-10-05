<?php

namespace Modules\Auth\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Support\RegistrationToken;
use Modules\Core\Support\ValidationPatterns;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** The kennitala and verified phone come from the electronic ID token, never from the client. */
    protected function prepareForValidation(): void
    {
        $claims = RegistrationToken::read($this->input('registration_token'));

        if ($claims) {
            $this->merge(array_filter($claims));
        }
    }

    public function rules(): array
    {
        return [
            'registration_token' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail) {
                if (! RegistrationToken::read($value)) {
                    $fail('Your electronic ID sign-in has expired or is invalid. Please sign in again.');
                }
            }],
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'phone_code' => ['required', 'string', 'max:8', 'regex:'.ValidationPatterns::PHONE_CODE],
            'phone_number' => ['required', 'string', 'max:20', 'regex:'.ValidationPatterns::PHONE_NUMBER],
            'kennitala' => ['required', 'string', 'max:20', 'regex:'.ValidationPatterns::KENNITALA, 'unique:users,kennitala'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'terms_accepted' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_token.required' => 'Sign in with electronic ID before creating an account.',
            'name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
            'phone_code.regex' => 'The phone code must be a valid dialing code, e.g. +354.',
            'phone_number.regex' => 'The phone number may only contain digits.',
            'kennitala.regex' => 'The kennitala must be 10 digits, e.g. 1234567890 or 123456-7890.',
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions.',
        ];
    }
}
