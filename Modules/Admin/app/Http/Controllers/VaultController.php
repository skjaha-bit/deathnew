<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CommercialBank\Models\UserCommercialBank;
use Modules\EmergencyAccess\Services\VaultAccessService;
use Modules\InheritanceMatters\Models\InheritanceMatter;
use Modules\Insurance\Models\InsurancePolicy;
use Modules\Pension\Models\PensionEntry;
use Modules\TradeUnion\Models\UserTradeUnion;

class VaultController extends Controller
{
    /**
     * Read-only aggregate view of a deceased user's vault, only reachable
     * because the emergency-access.unlocked middleware already asserted an
     * active grant for the acting admin. Each section is logged separately
     * via VaultAccessService::reveal so the audit trail shows exactly what
     * was opened, not just that the page was loaded.
     */
    public function show(Request $request, VaultAccessService $vaultAccessService)
    {
        $target = $request->attributes->get('vaultTargetUser');
        $actor = $request->user();

        $bankAccounts = $vaultAccessService->reveal($target, $actor, 'commercial_bank', function () use ($target) {
            return UserCommercialBank::query()->where('user_id', $target->id)->with(['bank', 'accounts', 'bonds', 'funds', 'loanType'])->get();
        });

        $insurancePolicies = $vaultAccessService->reveal($target, $actor, 'insurance', function () use ($target) {
            return InsurancePolicy::query()->where('user_id', $target->id)->with('insuranceCompany')->get();
        });

        $pensionEntries = $vaultAccessService->reveal($target, $actor, 'pension', function () use ($target) {
            return PensionEntry::query()->where('user_id', $target->id)->with('pensionFund')->get();
        });

        $tradeUnions = $vaultAccessService->reveal($target, $actor, 'trade_union', function () use ($target) {
            return UserTradeUnion::query()->where('user_id', $target->id)->with('tradeUnion')->get();
        });

        $inheritanceMatter = $vaultAccessService->reveal($target, $actor, 'inheritance_matters', function () use ($target) {
            return InheritanceMatter::query()->where('user_id', $target->id)->with('agents')->first();
        });

        $grant = $request->attributes->get('emergencyAccessGrant');

        return view('admin::vault.show', compact(
            'target', 'bankAccounts', 'insurancePolicies', 'pensionEntries', 'tradeUnions', 'inheritanceMatter', 'grant'
        ));
    }
}
