<?php

namespace Modules\UserSubscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateUserSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:user_subscriptions,id'],
            'subscription_type_id' => ['sometimes', 'nullable', 'integer', 'exists:subscription_types,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME],
            'associated_email' => ['sometimes', 'nullable', 'string', 'email:rfc,filter', 'max:255'],
            'billing_cycle' => ['sometimes', 'nullable', 'string', 'in:monthly,annual'],
            'amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'power_of_attorney_termination' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The subscription name may only contain letters, digits, spaces, periods, commas, ampersands, apostrophes and hyphens.',
        ];
    }
}
