<?php

namespace Modules\CommercialBank\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CommercialBank\Http\Requests\StoreUserCommercialBankRequest;
use Modules\CommercialBank\Http\Requests\UpdateUserCommercialBankRequest;
use Modules\CommercialBank\Http\Services\CommercialBankService;
use Modules\CommercialBank\Models\AccountType;
use Modules\CommercialBank\Models\Bank;
use Modules\CommercialBank\Models\BondType;
use Modules\CommercialBank\Models\FundType;
use Modules\CommercialBank\Models\LoanType;
use Modules\CommercialBank\Models\UserBankAccount;
use Modules\CommercialBank\Models\UserBond;
use Modules\CommercialBank\Models\UserCommercialBank;
use Modules\CommercialBank\Models\UserFund;
use Modules\Core\Traits\ApiResponse;

class CommercialBankController extends Controller
{
    use ApiResponse;

    protected const WITH_RELATIONS = [
        'bank',
        'loanType',
        'accounts.accountType',
        'bonds.bondType',
        'funds.fundType',
    ];

    public function __construct(protected CommercialBankService $commercialBankService) {}

    public function banks()
    {
        $banks = $this->commercialBankService->activeOptions();

        return $this->success($banks->map(fn (Bank $bank) => $this->formatOption($bank))->all());
    }

    public function loanTypes()
    {
        $types = $this->commercialBankService->loanTypeOptions();

        return $this->success($types->map(fn (LoanType $type) => $this->formatOption($type))->all());
    }

    public function accountTypes()
    {
        $types = $this->commercialBankService->accountTypeOptions();

        return $this->success($types->map(fn (AccountType $type) => $this->formatOption($type))->all());
    }

    public function bondTypes()
    {
        $types = $this->commercialBankService->bondTypeOptions();

        return $this->success($types->map(fn (BondType $type) => $this->formatOption($type))->all());
    }

    public function fundTypes()
    {
        $types = $this->commercialBankService->fundTypeOptions();

        return $this->success($types->map(fn (FundType $type) => $this->formatOption($type))->all());
    }

    public function index(Request $request)
    {
        $banks = $this->commercialBankService->listForUser($request->user()->id);

        return $this->success($banks->map(fn (UserCommercialBank $bank) => $this->formatUserCommercialBank($bank))->all());
    }

    public function store(StoreUserCommercialBankRequest $request)
    {
        $bank = $this->commercialBankService->create($request->user()->id, $request->validated());

        return $this->success($this->formatUserCommercialBank($bank), 'Bank record added.', 201);
    }

    public function show(Request $request, UserCommercialBank $userCommercialBank)
    {
        abort_if($userCommercialBank->user_id !== $request->user()->id, 403, 'You do not have access to this bank record.');

        $userCommercialBank->load(self::WITH_RELATIONS);

        return $this->success($this->formatUserCommercialBank($userCommercialBank));
    }

    public function update(UpdateUserCommercialBankRequest $request)
    {
        $data = $request->validated();

        $userCommercialBank = UserCommercialBank::query()->findOrFail($data['id']);

        abort_if($userCommercialBank->user_id !== $request->user()->id, 403, 'You do not have access to this bank record.');

        $userCommercialBank = $this->commercialBankService->update($userCommercialBank, $data);

        return $this->success($this->formatUserCommercialBank($userCommercialBank), 'Bank record updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:user_commercial_banks,id']]);

        $userCommercialBank = UserCommercialBank::query()->findOrFail($request->input('id'));

        abort_if($userCommercialBank->user_id !== $request->user()->id, 403, 'You do not have access to this bank record.');

        $this->commercialBankService->delete($userCommercialBank);

        return $this->success(null, 'Bank record removed.');
    }

    private function formatOption(Bank|LoanType|AccountType|BondType|FundType $option): array
    {
        return [
            'id' => $option->id,
            'name' => $option->name,
        ];
    }

    private function formatUserCommercialBank(UserCommercialBank $bank): array
    {
        return [
            'id' => $bank->id,
            'user_id' => $bank->user_id,
            'bank_id' => $bank->bank_id,
            'bank_name' => $bank->bank?->name,
            'loan_type_id' => $bank->loan_type_id,
            'loan_type_name' => $bank->loanType?->name,
            'bank_vault' => $bank->bank_vault,
            'notes_text' => $bank->notes_text,
            'accounts' => $bank->accounts->map(fn (UserBankAccount $account) => $this->formatAccount($account))->all(),
            'bonds' => $bank->bonds->map(fn (UserBond $bond) => $this->formatBond($bond))->all(),
            'funds' => $bank->funds->map(fn (UserFund $fund) => $this->formatFund($fund))->all(),
            'created_at' => $bank->created_at?->toIso8601String(),
            'updated_at' => $bank->updated_at?->toIso8601String(),
        ];
    }

    private function formatAccount(UserBankAccount $account): array
    {
        return [
            'id' => $account->id,
            'account_type_id' => $account->account_type_id,
            'account_type_name' => $account->accountType?->name,
            'account_number' => $account->account_number,
            'swift_iban' => $account->swift_iban,
            'is_joint' => $account->is_joint,
        ];
    }

    private function formatBond(UserBond $bond): array
    {
        return [
            'id' => $bond->id,
            'bond_type_id' => $bond->bond_type_id,
            'bond_type_name' => $bond->bondType?->name,
        ];
    }

    private function formatFund(UserFund $fund): array
    {
        return [
            'id' => $fund->id,
            'fund_type_id' => $fund->fund_type_id,
            'fund_type_name' => $fund->fundType?->name,
        ];
    }
}
