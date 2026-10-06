<?php

namespace Modules\CommercialBank\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Modules\CommercialBank\Models\AccountType;
use Modules\CommercialBank\Models\Bank;
use Modules\CommercialBank\Models\BondType;
use Modules\CommercialBank\Models\FundType;
use Modules\CommercialBank\Models\LoanType;
use Modules\CommercialBank\Models\UserCommercialBank;

class CommercialBankService
{
    protected const WITH_RELATIONS = [
        'bank',
        'loanType',
        'accounts.accountType',
        'bonds.bondType',
        'funds.fundType',
    ];

    public function listForUser(int $userId)
    {
        return UserCommercialBank::query()
            ->with(self::WITH_RELATIONS)
            ->where('user_id', $userId)
            ->get();
    }

    public function create(int $userId, array $data): UserCommercialBank
    {
        return DB::transaction(function () use ($userId, $data) {
            $bank = UserCommercialBank::query()->create([
                'user_id' => $userId,
                'bank_id' => $data['bank_id'] ?? null,
                'swift' => $this->normalizeSwift($data['swift'] ?? null),
                'loan_type_id' => $data['loan_type_id'] ?? null,
                'bank_vault' => $data['bank_vault'] ?? false,
                'notes_text' => $data['notes_text'] ?? null,
            ]);

            $this->syncAccounts($bank, $data['accounts'] ?? []);
            $this->syncBonds($bank, $data['bonds'] ?? []);
            $this->syncFunds($bank, $data['funds'] ?? []);

            return $bank->fresh(self::WITH_RELATIONS);
        });
    }

    public function update(UserCommercialBank $bank, array $data): UserCommercialBank
    {
        return DB::transaction(function () use ($bank, $data) {
            // Same fields as create: the body replaces the whole record.
            $bank->update([
                'bank_id' => $data['bank_id'] ?? null,
                'swift' => $this->normalizeSwift($data['swift'] ?? null),
                'loan_type_id' => $data['loan_type_id'] ?? null,
                'bank_vault' => $data['bank_vault'] ?? false,
                'notes_text' => $data['notes_text'] ?? null,
            ]);

            $bank->accounts()->delete();
            $bank->bonds()->delete();
            $bank->funds()->delete();

            $this->syncAccounts($bank, $data['accounts'] ?? []);
            $this->syncBonds($bank, $data['bonds'] ?? []);
            $this->syncFunds($bank, $data['funds'] ?? []);

            return $bank->fresh(self::WITH_RELATIONS);
        });
    }

    public function delete(UserCommercialBank $bank): bool
    {
        return (bool) $bank->delete();
    }

    public function activeOptions(): Collection
    {
        return Bank::query()->where('is_active', true)->get(['id', 'name']);
    }

    public function loanTypeOptions(): Collection
    {
        return LoanType::query()->where('is_active', true)->get(['id', 'name']);
    }

    public function accountTypeOptions(): Collection
    {
        return AccountType::query()->where('is_active', true)->get(['id', 'name']);
    }

    public function bondTypeOptions(): Collection
    {
        return BondType::query()->where('is_active', true)->get(['id', 'name']);
    }

    public function fundTypeOptions(): Collection
    {
        return FundType::query()->where('is_active', true)->get(['id', 'name']);
    }

    /** SWIFT codes are stored upper-case; an empty value is stored as null. */
    protected function normalizeSwift(?string $swift): ?string
    {
        return filled($swift) ? strtoupper($swift) : null;
    }

    protected function syncAccounts(UserCommercialBank $bank, array $accounts): void
    {
        if (empty($accounts)) {
            return;
        }

        $bank->accounts()->createMany(array_map(fn (array $account) => [
            'account_type_id' => $account['account_type_id'] ?? null,
            'account_number' => $account['account_number'] ?? null,
            'swift_iban' => $account['swift_iban'] ?? null,
            'is_joint' => $account['is_joint'] ?? false,
        ], $accounts));
    }

    protected function syncBonds(UserCommercialBank $bank, array $bonds): void
    {
        if (empty($bonds)) {
            return;
        }

        $bank->bonds()->createMany(array_map(fn (array $bond) => [
            'bond_type_id' => $bond['bond_type_id'] ?? null,
        ], $bonds));
    }

    protected function syncFunds(UserCommercialBank $bank, array $funds): void
    {
        if (empty($funds)) {
            return;
        }

        $bank->funds()->createMany(array_map(fn (array $fund) => [
            'fund_type_id' => $fund['fund_type_id'] ?? null,
        ], $funds));
    }
}
