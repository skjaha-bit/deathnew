<?php

namespace App\Rules;

use App\Support\BlindIndex;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * `unique` for encrypted identity columns: checks the blind-index hash
 * column instead of the (randomly salted) encrypted value.
 */
class UniqueBlindIndex implements ValidationRule
{
    private function __construct(
        private readonly string $table,
        private readonly string $hashColumn,
        private readonly Closure $hasher,
        private readonly ?int $ignoreId,
    ) {}

    public static function email(string $table = 'users', ?int $ignoreId = null): self
    {
        return new self($table, 'email_hash', BlindIndex::email(...), $ignoreId);
    }

    public static function kennitala(string $table = 'users', ?int $ignoreId = null): self
    {
        return new self($table, 'kennitala_hash', BlindIndex::kennitala(...), $ignoreId);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hash = ($this->hasher)(is_scalar($value) ? (string) $value : null);

        if ($hash === null) {
            return;
        }

        $taken = DB::table($this->table)
            ->where($this->hashColumn, $hash)
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($taken) {
            $fail(__('validation.unique', ['attribute' => str_replace('_', ' ', $attribute)]));
        }
    }
}
