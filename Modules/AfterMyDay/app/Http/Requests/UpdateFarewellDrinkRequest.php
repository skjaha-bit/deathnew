<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateFarewellDrinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function safeText(int $max = 2000): array
    {
        return ['sometimes', 'nullable', 'string', "max:{$max}", 'regex:'.ValidationPatterns::SAFE_TEXT];
    }

    public function rules(): array
    {
        return [
            'preference' => ['sometimes', 'nullable', 'string', 'in:yes_please,no_thank_you,relatives_choose,other_wishes'],
            'location' => $this->safeText(),
            'guests' => $this->safeText(),
            'catering' => $this->safeText(),
            'arrangement' => $this->safeText(),
            'comments' => $this->safeText(),
            'other_wishes' => $this->safeText(),
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
        ];
    }
}
