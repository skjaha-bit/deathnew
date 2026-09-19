<?php

namespace Modules\UserSubscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class StoreUserSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_type_id' => ['nullable', 'integer', 'exists:subscription_types,id'],
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'associated_email' => ['nullable', 'string', 'email:rfc,filter', 'max:255'],
            'billing_cycle' => ['nullable', 'string', 'in:monthly,annual'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'power_of_attorney_termination' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The subscription name may only contain letters, digits, spaces, periods, commas, ampersands, apostrophes and hyphens.',
        ];
    }
}
