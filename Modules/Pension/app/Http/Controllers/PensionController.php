<?php

namespace Modules\Pension\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Pension\Http\Requests\SendPensionInquiryRequest;
use Modules\Pension\Http\Requests\StorePensionEntryRequest;
use Modules\Pension\Http\Requests\UpdatePensionEntryRequest;
use Modules\Pension\Http\Services\PensionService;
use Modules\Pension\Models\PensionEntry;
use Modules\Pension\Models\PensionFund;
use Modules\Pension\Models\PensionFundInquiry;

class PensionController extends Controller
{
    use ApiResponse;

    public function __construct(protected PensionService $pensionService) {}

    public function index(Request $request)
    {
        $entries = $this->pensionService->listForUser($request->user()->id, $request->query('category'));

        return $this->success($entries->map(fn (PensionEntry $entry) => $this->formatPensionEntry($entry)));
    }

    public function store(StorePensionEntryRequest $request)
    {
        $entry = $this->pensionService->create($request->user()->id, $request->validated());

        return $this->success($this->formatPensionEntry($entry), 'Pension entry added.', 201);
    }

    public function show(Request $request, PensionEntry $pensionEntry)
    {
        abort_if($pensionEntry->user_id !== $request->user()->id, 403, 'You do not have access to this pension entry.');

        return $this->success($this->formatPensionEntry($pensionEntry));
    }

    public function update(UpdatePensionEntryRequest $request, PensionEntry $pensionEntry)
    {
        abort_if($pensionEntry->user_id !== $request->user()->id, 403, 'You do not have access to this pension entry.');

        $pensionEntry = $this->pensionService->update($pensionEntry, $request->validated());

        return $this->success($this->formatPensionEntry($pensionEntry), 'Pension entry updated.');
    }

    public function destroy(Request $request, PensionEntry $pensionEntry)
    {
        abort_if($pensionEntry->user_id !== $request->user()->id, 403, 'You do not have access to this pension entry.');

        $this->pensionService->delete($pensionEntry);

        return $this->success(null, 'Pension entry removed.');
    }

    public function inquiriesIndex(Request $request)
    {
        $inquiries = $this->pensionService->inquiriesForUser($request->user()->id);

        return $this->success($inquiries->map(fn (PensionFundInquiry $inquiry) => $this->formatPensionFundInquiry($inquiry)));
    }

    public function inquiriesStore(SendPensionInquiryRequest $request)
    {
        $inquiries = $this->pensionService->sendInquiries(
            $request->user()->id,
            $request->validated()['pension_fund_ids'],
            $request->validated()['category']
        );

        return $this->success($inquiries->map(fn (PensionFundInquiry $inquiry) => $this->formatPensionFundInquiry($inquiry)), 'Inquiries sent.', 201);
    }

    public function fundOptions(Request $request)
    {
        $funds = $this->pensionService->activeFunds($request->query('category'));

        return $this->success($funds->map(fn (PensionFund $fund) => $this->formatPensionFund($fund)));
    }

    private function formatPensionEntry(PensionEntry $pensionEntry): array
    {
        return [
            'id' => $pensionEntry->id,
            'user_id' => $pensionEntry->user_id,
            'pension_fund_id' => $pensionEntry->pension_fund_id,
            'custom_fund_name' => $pensionEntry->custom_fund_name,
            'category' => $pensionEntry->category,
            'from_year' => $pensionEntry->from_year,
            'to_year' => $pensionEntry->to_year,
            'still_in_payment' => $pensionEntry->still_in_payment,
            'estimated_credit' => $pensionEntry->estimated_credit,
            'special_rights_text' => $pensionEntry->special_rights_text,
            'created_at' => $pensionEntry->created_at?->toIso8601String(),
            'updated_at' => $pensionEntry->updated_at?->toIso8601String(),
        ];
    }

    private function formatPensionFundInquiry(PensionFundInquiry $inquiry): array
    {
        return [
            'id' => $inquiry->id,
            'pension_fund_id' => $inquiry->pension_fund_id,
            'category' => $inquiry->category,
            'status' => $inquiry->status,
            'sent_at' => $inquiry->sent_at?->toIso8601String(),
        ];
    }

    private function formatPensionFund(PensionFund $pensionFund): array
    {
        return [
            'id' => $pensionFund->id,
            'name' => $pensionFund->name,
            'email' => $pensionFund->email,
            'category' => $pensionFund->category,
        ];
    }
}
