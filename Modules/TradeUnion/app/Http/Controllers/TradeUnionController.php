<?php

namespace Modules\TradeUnion\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\TradeUnion\Http\Requests\StoreUserTradeUnionRequest;
use Modules\TradeUnion\Http\Requests\UpdateUserTradeUnionRequest;
use Modules\TradeUnion\Http\Services\TradeUnionService;
use Modules\TradeUnion\Models\TradeUnion;
use Modules\TradeUnion\Models\UserTradeUnion;

class TradeUnionController extends Controller
{
    use ApiResponse;

    public function __construct(protected TradeUnionService $tradeUnionService) {}

    /**
     * Active trade unions for the "Trade union" dropdown.
     */
    public function options()
    {
        $tradeUnions = $this->tradeUnionService->activeOptions();

        return $this->success($tradeUnions->map(fn (TradeUnion $tradeUnion) => $this->formatTradeUnion($tradeUnion))->values());
    }

    public function index(Request $request)
    {
        $userTradeUnions = $this->tradeUnionService->listForUser($request->user()->id);

        return $this->success($userTradeUnions->map(fn (UserTradeUnion $userTradeUnion) => $this->formatUserTradeUnion($userTradeUnion))->values());
    }

    public function store(StoreUserTradeUnionRequest $request)
    {
        $userTradeUnion = $this->tradeUnionService->create($request->user()->id, $request->validated());

        return $this->success($this->formatUserTradeUnion($userTradeUnion), 'Trade union added.', 201);
    }

    public function show(Request $request, UserTradeUnion $userTradeUnion)
    {
        abort_if($userTradeUnion->user_id !== $request->user()->id, 403, 'You do not have access to this trade union entry.');

        return $this->success($this->formatUserTradeUnion($userTradeUnion));
    }

    public function update(UpdateUserTradeUnionRequest $request)
    {
        $data = $request->validated();

        $userTradeUnion = UserTradeUnion::query()->findOrFail($data['id']);

        abort_if($userTradeUnion->user_id !== $request->user()->id, 403, 'You do not have access to this trade union entry.');

        $userTradeUnion = $this->tradeUnionService->update($userTradeUnion, $data);

        return $this->success($this->formatUserTradeUnion($userTradeUnion), 'Trade union updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:user_trade_unions,id']]);

        $userTradeUnion = UserTradeUnion::query()->findOrFail($request->input('id'));

        abort_if($userTradeUnion->user_id !== $request->user()->id, 403, 'You do not have access to this trade union entry.');

        $this->tradeUnionService->delete($userTradeUnion);

        return $this->success(null, 'Trade union removed.');
    }

    private function formatUserTradeUnion(UserTradeUnion $userTradeUnion): array
    {
        return [
            'id' => $userTradeUnion->id,
            'user_id' => $userTradeUnion->user_id,
            'trade_union_id' => $userTradeUnion->trade_union_id,
            'union_name' => $userTradeUnion->union_name,
            'from_month' => $userTradeUnion->from_month,
            'from_year' => $userTradeUnion->from_year,
            'to_month' => $userTradeUnion->to_month,
            'to_year' => $userTradeUnion->to_year,
            'still_in_payment' => $userTradeUnion->still_in_payment,
            'department_contact' => $userTradeUnion->department_contact,
            'rights_text' => $userTradeUnion->rights_text,
            'notes_text' => $userTradeUnion->notes_text,
            'created_at' => $userTradeUnion->created_at?->toIso8601String(),
            'updated_at' => $userTradeUnion->updated_at?->toIso8601String(),
        ];
    }

    private function formatTradeUnion(TradeUnion $tradeUnion): array
    {
        return [
            'id' => $tradeUnion->id,
            'name' => $tradeUnion->name,
        ];
    }
}
