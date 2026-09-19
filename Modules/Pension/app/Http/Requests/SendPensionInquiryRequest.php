<?php

namespace Modules\Pension\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendPensionInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pension_fund_ids' => ['required', 'array'],
            'pension_fund_ids.*' => ['integer', 'exists:pension_funds,id'],
            'category' => ['required', 'string', 'in:private,obligatory'],
        ];
    }
}
