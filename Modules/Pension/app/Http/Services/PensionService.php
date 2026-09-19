<?php

namespace Modules\Pension\Http\Services;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Modules\Pension\Models\PensionEntry;
use Modules\Pension\Models\PensionFund;
use Modules\Pension\Models\PensionFundInquiry;

class PensionService
{
    public function listForUser(int $userId, ?string $category = null)
    {
        $query = PensionEntry::query()->where('user_id', $userId);

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get();
    }

    public function create(int $userId, array $data): PensionEntry
    {
        return PensionEntry::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }

    public function update(PensionEntry $pensionEntry, array $data): PensionEntry
    {
        $pensionEntry->update($data);

        return $pensionEntry->fresh();
    }

    public function delete(PensionEntry $pensionEntry): bool
    {
        return (bool) $pensionEntry->delete();
    }

    /**
     * Active pension funds available for selection, optionally narrowed to a
     * category (a fund flagged 'both' is always included).
     */
    public function activeFunds(?string $category = null)
    {
        $query = PensionFund::query()->where('is_active', true);

        if ($category) {
            $query->where(function ($q) use ($category) {
                $q->where('category', $category)->orWhere('category', 'both');
            });
        }

        return $query->get();
    }

    /**
     * Send (log) inquiries to the given pension funds asking whether the user
     * has a registered balance. Stand-in for real email delivery.
     */
    public function sendInquiries(int $userId, array $pensionFundIds, string $category)
    {
        $inquiries = collect();

        foreach ($pensionFundIds as $fundId) {
            $fund = PensionFund::query()->find($fundId);

            $inquiry = PensionFundInquiry::updateOrCreate(
                [
                    'user_id' => $userId,
                    'pension_fund_id' => $fundId,
                    'category' => $category,
                ],
                [
                    'status' => 'sent',
                    'sent_at' => Date::now(),
                ]
            );

            Log::channel('api')->info('Pension fund inquiry dispatched', [
                'user_id' => $userId,
                'pension_fund_id' => $fundId,
                'pension_fund_name' => $fund?->name,
                'category' => $category,
                'message' => sprintf(
                    'Inquiry to %s: Does your fund have a registered %s pension balance for this user? Please confirm.',
                    $fund?->name ?? 'Unknown fund',
                    $category
                ),
            ]);

            $inquiries->push($inquiry);
        }

        return $inquiries;
    }

    public function inquiriesForUser(int $userId)
    {
        return PensionFundInquiry::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
