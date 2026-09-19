<?php

namespace Modules\Relatives\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Support\ValidationPatterns;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:pets,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'animal_type' => ['sometimes', 'required', 'string', 'max:100', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'breed' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'birth_year' => ['sometimes', 'nullable', 'integer', 'between:1900,'.date('Y')],
            'birth_month' => ['sometimes', 'nullable', 'integer', 'between:1,12'],
            'identification_number' => ['sometimes', 'nullable', 'string', 'max:100', 'regex:'.ValidationPatterns::CODE],

            'vet_clinic_name' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'vet_contact_person' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
            'vet_contact_number' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{4,20}$/'],
            'medical_conditions' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'dietary_needs' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
            'insurance_company_id' => ['sometimes', 'nullable', 'integer', 'exists:insurance_companies,id'],
            'policy_number' => ['sometimes', 'nullable', 'string', 'max:64', 'regex:'.ValidationPatterns::CODE],

            'designated_guardian_id' => ['sometimes', 'nullable', 'integer', 'exists:guardians,id'],
            'guardian_agreed' => ['sometimes', 'nullable', 'boolean'],
            'alternative_guardian_id' => ['sometimes', 'nullable', 'integer', 'exists:guardians,id'],
            'final_wishes' => ['sometimes', 'nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'This field may not contain < or > characters.',
            'animal_type.regex' => 'This field may not contain < or > characters.',
            'breed.regex' => 'This field may not contain < or > characters.',
            'identification_number.regex' => 'The ID may only contain letters, digits, spaces and hyphens.',
            'vet_contact_person.regex' => 'The contact person may only contain letters, spaces, apostrophes, hyphens and periods.',
            'vet_contact_number.regex' => 'The contact number format is invalid.',
            'policy_number.regex' => 'The policy number may only contain letters, digits, spaces and hyphens.',
        ];
    }
}
