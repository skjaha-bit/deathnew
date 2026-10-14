<?php

namespace Modules\CommercialBank\Http\Requests;

use Illuminate\Validation\Validator;
use Modules\CommercialBank\Models\Bank;
use Modules\CommercialBank\Models\UserCommercialBank;

/**
 * Update takes the same body as create, plus the id of the record to update.
 * `swift` may be left out: the stored value is then kept (see
 * CommercialBankService::update), so it only counts as missing for bank 6
 * when nothing is stored either.
 */
class UpdateUserCommercialBankRequest extends StoreUserCommercialBankRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['swift'] = ['sometimes', ...$rules['swift']];

        return [
            'id' => ['required', 'integer', 'exists:user_commercial_banks,id'],
            ...$rules,
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->has('swift') || (int) $this->input('bank_id') !== Bank::SWIFT_REQUIRED_ID) {
                return;
            }

            $stored = UserCommercialBank::query()->find($this->input('id'))?->swift;

            if (blank($stored)) {
                $validator->errors()->add('swift', 'Enter the SWIFT code for this bank.');
            }
        });
    }
}
