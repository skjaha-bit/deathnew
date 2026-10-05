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

        // e-ID on the SIM needs the phone number; with only a kennitala, Taktikal can only reach the Auðkenni app.
        if (! $this->filled('context')) {
            $this->merge(['context' => $this->filled('phone_number') ? 'Sim' : 'App']);
        }
    }

    public function rules(): array
    {
        return [
            'phone_number' => ['required_without:kennitala', 'required_if:context,Sim', 'nullable', 'digits:7'],
            'kennitala' => ['required_without:phone_number', 'nullable', 'string'],
            'context' => ['nullable', 'in:Sim,App'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required_without' => 'Enter your phone number or your kennitala to sign in with electronic ID.',
            'phone_number.required_if' => 'Enter your phone number to sign in with the e-ID on your SIM card.',
            'phone_number.digits' => 'Enter an Icelandic phone number of 7 digits.',
            'context.in' => 'Context must be Sim or App.',
        ];
    }
}
