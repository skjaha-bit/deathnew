<?php

namespace Modules\InheritanceMatters\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\InheritanceMatters\Http\Requests\SaveInheritanceMatterRequest;
use Modules\InheritanceMatters\Http\Services\InheritanceMatterService;
use Modules\InheritanceMatters\Models\InheritanceMatter;
use Modules\InheritanceMatters\Models\InheritanceMatterAgent;

class InheritanceMatterController extends Controller
{
    use ApiResponse;

    public function __construct(protected InheritanceMatterService $inheritanceMatterService) {}

    public function show(Request $request)
    {
        $matter = $this->inheritanceMatterService->getOrCreateForUser($request->user()->id);

        return $this->success($this->formatInheritanceMatter($matter));
    }

    public function save(SaveInheritanceMatterRequest $request)
    {
        $matter = $this->inheritanceMatterService->getOrCreateForUser($request->user()->id);
        $matter = $this->inheritanceMatterService->save($matter, $request->validated(), $request->file('will_file'));

        return $this->success($this->formatInheritanceMatter($matter), 'Inheritance matters saved.');
    }

    private function formatInheritanceMatter(InheritanceMatter $matter): array
    {
        return [
            'id' => $matter->id,
            'user_id' => $matter->user_id,
            'will_exists' => $matter->will_exists,
            'will_location' => $matter->will_location,
            'will_file_path' => $matter->will_file_path,
            'will_file_url' => $matter->will_file_path ? asset('storage/'.$matter->will_file_path) : null,
            'notes' => $matter->notes,
            'agents' => $matter->agents->map(fn (InheritanceMatterAgent $agent) => $this->formatAgent($agent))->values(),
            'created_at' => $matter->created_at?->toIso8601String(),
            'updated_at' => $matter->updated_at?->toIso8601String(),
        ];
    }

    private function formatAgent(InheritanceMatterAgent $agent): array
    {
        return [
            'id' => $agent->id,
            'name' => $agent->name,
            'phone' => $agent->phone,
            'email' => $agent->email,
            'social_security_number' => $agent->social_security_number,
            'address' => $agent->address,
            'sort_order' => $agent->sort_order,
        ];
    }
}
