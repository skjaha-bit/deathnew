<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ElectronicIdStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('phone_number')) {
            // Auðkenni wants the 7-digit local number, so drop spaces and a leading 354.
            $digits = preg_replace('/\D/', '', (string) $this->input('phone_number'));
            $this->merge(['phone_number' => strlen($digits) === 10 && str_starts_with($digits, '354') ? substr($digits, 3) : $digits]);
        }
    }

    public function rules(): array
    {
        return [
            'phone_number' => ['required_without:kennitala', 'nullable', 'digits:7'],
            'kennitala' => ['required_without:phone_number', 'nullable', 'string'],
            'context' => ['nullable', 'in:Sim,App'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required_without' => 'Enter your phone number or your kennitala to sign in with electronic ID.',
            'phone_number.digits' => 'Enter an Icelandic phone number of 7 digits.',
            'context.in' => 'Context must be Sim or App.',
        ];
    }
}
