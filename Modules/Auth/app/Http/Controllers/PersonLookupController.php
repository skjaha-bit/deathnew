<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Exceptions\NationalRegistryUnavailableException;
use Modules\Auth\Http\Requests\PersonLookupRequest;
use Modules\Auth\Http\Services\PersonLookupService;
use Modules\Core\Traits\ApiResponse;

class PersonLookupController extends Controller
{
    use ApiResponse;

    public function __construct(protected PersonLookupService $lookup) {}

    /** Everything Taktikal knows about a kennitala in one response, deceased or not. */
    public function show(PersonLookupRequest $request)
    {
        try {
            $result = $this->lookup->lookup($request->validated('kennitala'));
        } catch (NationalRegistryUnavailableException $e) {
            return $this->error($e->getMessage(), 503);
        }

        if (! $result['valid']) {
            return $this->error('Kennitala (SSN) is not valid.', 422);
        }

        return $this->success($result['person']);
    }
}
