<?php

namespace Modules\CommercialBank\Http\Requests;

/**
 * Update takes the same body as create, plus the id of the record to update.
 */
class UpdateUserCommercialBankRequest extends StoreUserCommercialBankRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:user_commercial_banks,id'],
            ...parent::rules(),
        ];
    }
}
