<?php

namespace Modules\Auth\Http\Requests;

/**
 * /auth/login starts an electronic ID sign-in, like /auth/eid/start, but keeps
 * accepting the phone_code + phone_number pair older clients send.
 */
class LoginRequest extends ElectronicIdStartRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'phone_code' => ['nullable', 'in:+354,354'],
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'phone_code.in' => 'Electronic ID sign-in is only available for Icelandic (+354) phone numbers.',
        ];
    }
}
