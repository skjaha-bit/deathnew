<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kennitala' => ['required_without_all:phone_code,phone_number', 'nullable', 'string'],
            'phone_code' => ['required_with:phone_number', 'nullable', 'string'],
            'phone_number' => ['required_with:phone_code', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kennitala.required_without_all' => 'Enter your kennitala or your phone number to log in.',
        ];
    }
}
