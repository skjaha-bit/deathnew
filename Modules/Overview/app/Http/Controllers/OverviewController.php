<?php

namespace Modules\Overview\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Core\Traits\ApiResponse;
use Modules\Overview\Http\Services\OverviewService;

class OverviewController extends Controller
{
    use ApiResponse;

    public function __construct(protected OverviewService $overviewService) {}

    /**
     * A single "profile completion" dashboard: which sections the user has
     * started, and an overall percentage across the app's modules.
     */
    public function index(Request $request)
    {
        return $this->success($this->overviewService->getDashboard($request->user()->id));
    }
}
