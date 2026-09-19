<?php

namespace Modules\AfterMyDay\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdateFuneralRequest extends FormRequest
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
            'ceremony_type_id' => ['sometimes', 'nullable', 'integer', 'exists:ceremony_types,id'],
            'arrangement_type_id' => ['sometimes', 'nullable', 'integer', 'exists:arrangement_types,id'],
            'other_ceremony_options' => $this->safeText(),
            'funeral_service_type_id' => ['sometimes', 'nullable', 'integer', 'exists:funeral_service_types,id'],
            'other_thing' => $this->safeText(255),
            'location' => $this->safeText(),
            'laying_place' => $this->safeText(),
            'clothing_request' => $this->safeText(),
            'event_manager' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'guests' => $this->safeText(),
            'pallbearer_request' => $this->safeText(),
            'coffin_design' => $this->safeText(),
            'coffin_decorations' => ['sometimes', 'nullable', 'boolean'],
            'coffin_decorations_detail' => $this->safeText(),
            'charity_case' => $this->safeText(255),
            'another_thing' => $this->safeText(),
            'notes' => $this->safeText(),

            'music' => ['sometimes', 'nullable', 'array'],
            'music.*.description' => ['required_with:music', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'music.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.regex' => 'This field may not contain < or > characters.',
            'event_manager.regex' => 'The event manager name may only contain letters, spaces, apostrophes, hyphens and periods.',
        ];
    }
}
