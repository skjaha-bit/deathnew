<?php

namespace Modules\Property\Http\Requests\Concerns;

/**
 * Map the ownership value the client sends (e.g. "Own by me", "own-by-me", "Co-owned", "coowned")
 * onto the stored keys own_by_me / co_owned, so label-style values pass validation.
 */
trait NormalizesOwnership
{
    protected function prepareForValidation(): void
    {
        if (! is_string($this->ownership)) {
            return;
        }

        $key = preg_replace('/[^a-z]/', '', strtolower($this->ownership));

        $normalized = match ($key) {
            '' => null,
            'ownbyme', 'ownedbyme', 'own', 'owned', 'sole', 'solelyowned', 'self' => 'own_by_me',
            'coowned', 'coowner', 'coownership', 'joint', 'jointlyowned', 'shared' => 'co_owned',
            default => $this->ownership,
        };

        $this->merge(['ownership' => $normalized]);
    }
}
