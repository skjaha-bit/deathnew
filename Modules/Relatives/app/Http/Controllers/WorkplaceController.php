<?php

namespace Modules\Relatives\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Relatives\Http\Requests\StoreWorkplaceRequest;
use Modules\Relatives\Http\Requests\UpdateWorkplaceRequest;
use Modules\Relatives\Http\Services\WorkplaceService;
use Modules\Relatives\Models\Workplace;

class WorkplaceController extends Controller
{
    use ApiResponse;

    public function __construct(protected WorkplaceService $workplaceService) {}

    public function index(Request $request)
    {
        $workplaces = $this->workplaceService->listForUser($request->user()->id);

        return $this->success($workplaces->map(fn (Workplace $w) => $this->formatWorkplace($w)));
    }

    public function store(StoreWorkplaceRequest $request)
    {
        $workplace = $this->workplaceService->create($request->user()->id, $request->validated());

        return $this->success($this->formatWorkplace($workplace), 'Workplace added.', 201);
    }

    public function show(Request $request, Workplace $workplace)
    {
        abort_if($workplace->user_id !== $request->user()->id, 403, 'You do not have access to this workplace.');

        $workplace->load(['emails', 'phones']);

        return $this->success($this->formatWorkplace($workplace));
    }

    public function update(UpdateWorkplaceRequest $request)
    {
        $data = $request->validated();
        $workplace = Workplace::query()->findOrFail($data['id']);
        unset($data['id']);

        abort_if($workplace->user_id !== $request->user()->id, 403, 'You do not have access to this workplace.');

        $workplace = $this->workplaceService->update($workplace, $data);

        return $this->success($this->formatWorkplace($workplace), 'Workplace updated.');
    }

    public function destroy(Request $request, Workplace $workplace)
    {
        abort_if($workplace->user_id !== $request->user()->id, 403, 'You do not have access to this workplace.');

        $this->workplaceService->delete($workplace);

        return $this->success(null, 'Workplace removed.');
    }

    private function formatWorkplace(Workplace $w): array
    {
        return [
            'id' => $w->id,
            'user_id' => $w->user_id,
            'company_name' => $w->company_name,
            'contact_name' => $w->contact_name,
            'contact_title' => $w->contact_title,
            'department' => $w->department,
            'notify_after_death' => $w->notify_after_death,
            'message_to_contact' => $w->message_to_contact,
            'additional_note' => $w->additional_note,
            'emails' => $w->emails->pluck('email')->values(),
            'phones' => $w->phones->map(fn ($p) => [
                'phone_code' => $p->phone_code,
                'phone_number' => $p->phone_number,
            ])->values(),
            'created_at' => $w->created_at?->toIso8601String(),
            'updated_at' => $w->updated_at?->toIso8601String(),
        ];
    }
}
