<?php

namespace Modules\Relatives\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Relatives\Http\Requests\StoreRelativeRequest;
use Modules\Relatives\Http\Requests\UpdateRelativeRequest;
use Modules\Relatives\Http\Services\RelativeService;
use Modules\Relatives\Models\Relative;

class RelativeController extends Controller
{
    use ApiResponse;

    public function __construct(protected RelativeService $relativeService) {}

    public function index(Request $request)
    {
        $relatives = $this->relativeService->listForUser($request->user()->id);

        return $this->success($relatives->map(fn (Relative $r) => $this->formatRelative($r)));
    }

    public function store(StoreRelativeRequest $request)
    {
        $relative = $this->relativeService->create($request->user()->id, $request->validated());

        return $this->success($this->formatRelative($relative), 'Relative added.', 201);
    }

    public function show(Request $request, Relative $relative)
    {
        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        return $this->success($this->formatRelative($relative));
    }

    public function update(UpdateRelativeRequest $request, Relative $relative)
    {
        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        $relative = $this->relativeService->update($relative, $request->validated());

        return $this->success($this->formatRelative($relative), 'Relative updated.');
    }

    public function destroy(Request $request, Relative $relative)
    {
        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        $this->relativeService->delete($relative);

        return $this->success(null, 'Relative removed.');
    }

    public function invite(Request $request, Relative $relative)
    {
        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        $relative = $this->relativeService->invite($relative);

        return $this->success($this->formatRelative($relative), 'Invite dispatched.');
    }

    private function formatRelative(Model $r): array
    {
        return [
            'id' => $r->id,
            'user_id' => $r->user_id,
            'name' => $r->name,
            'email' => $r->email,
            'phone' => $r->phone,
            'relation' => $r->relation,
            'permissions' => $r->permissions,
            'invite_status' => $r->invite_status,
            'invited_at' => $r->invited_at?->toIso8601String(),
            'accepted_at' => $r->accepted_at?->toIso8601String(),
            'created_at' => $r->created_at?->toIso8601String(),
            'updated_at' => $r->updated_at?->toIso8601String(),
        ];
    }
}
