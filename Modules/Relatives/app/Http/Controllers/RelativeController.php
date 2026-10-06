<?php

namespace Modules\Relatives\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Exceptions\NationalRegistryUnavailableException;
use Modules\Core\Traits\ApiResponse;
use Modules\Relatives\Http\Requests\LookupRelativeRequest;
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

    /** Checks a family member's kennitala in the national registry and returns their name. */
    public function lookup(LookupRelativeRequest $request)
    {
        $kennitala = $request->validated('kennitala');

        if ($this->relativeService->alreadyAdded($request->user()->id, $kennitala, $request->validated('relative_id'))) {
            return $this->error('You have already added this member.', 422);
        }

        try {
            $person = $this->relativeService->lookupKennitala($kennitala);
        } catch (NationalRegistryUnavailableException $e) {
            return $this->error($e->getMessage(), 503);
        }

        if (! $person['valid']) {
            return $this->error('Kennitala (SSN) is not valid.', 422);
        }

        if ($person['deceased']) {
            return $this->error('This person is deceased and cannot be added as a family member.', 422);
        }

        return $this->success([
            'kennitala' => $person['kennitala'],
            'name' => $person['name'],
        ]);
    }

    public function store(StoreRelativeRequest $request)
    {
        $relative = $this->relativeService->create($request->user()->id, $request->validated());

        return $this->success($this->formatRelative($relative), 'Relative added.', 201);
    }

    public function show(Request $request, Relative $relative)
    {
        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        $relative->load(['emails', 'phones']);

        return $this->success($this->formatRelative($relative));
    }

    public function update(UpdateRelativeRequest $request)
    {
        $data = $request->validated();
        $relative = Relative::query()->findOrFail($data['id']);
        unset($data['id']);

        abort_if($relative->user_id !== $request->user()->id, 403, 'You do not have access to this relative.');

        $relative = $this->relativeService->update($relative, $data);

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

    private function formatRelative(Relative $r): array
    {
        return [
            'id' => $r->id,
            'user_id' => $r->user_id,
            'name' => $r->name,
            'kennitala' => $r->kennitala,
            'relation' => $r->relation,
            'emails' => $r->emails->pluck('email')->values(),
            'phones' => $r->phones->map(fn ($p) => [
                'phone_code' => $p->phone_code,
                'phone_number' => $p->phone_number,
            ])->values(),
            'read_access_after_death' => $r->read_access_after_death,
            'estate_agent_notes' => $r->estate_agent_notes,
            'personal_affairs_notes' => $r->personal_affairs_notes,
            'permissions' => $r->permissions,
            'invite_status' => $r->invite_status,
            'invited_at' => $r->invited_at?->toIso8601String(),
            'accepted_at' => $r->accepted_at?->toIso8601String(),
            'created_at' => $r->created_at?->toIso8601String(),
            'updated_at' => $r->updated_at?->toIso8601String(),
        ];
    }
}
