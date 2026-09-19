<?php

namespace Modules\Relatives\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Relatives\Models\DeathReport;
use Modules\Relatives\Models\Relative;

class DeathReportController extends Controller
{
    use ApiResponse;

    /**
     * A relative reports that the account holder has passed away. Public,
     * unauthenticated (relatives are not necessarily app users) - this only
     * ever creates a pending queue item for admin review; it never changes
     * the account's status on its own.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'relative_id' => ['required', 'integer', 'exists:relatives,id'],
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:50'],
            'relationship' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $relative = Relative::query()->findOrFail($data['relative_id']);

        $report = DeathReport::query()->create([
            'user_id' => $relative->user_id,
            'reported_by_relative_id' => $relative->id,
            'reporter_name' => $data['reporter_name'],
            'reporter_email' => $data['reporter_email'] ?? null,
            'reporter_phone' => $data['reporter_phone'] ?? null,
            'relationship' => $data['relationship'] ?? $relative->relation,
            'notes' => $data['notes'] ?? null,
            'status' => DeathReport::STATUS_PENDING,
            'reported_at' => now(),
        ]);

        return $this->success(['id' => $report->id], 'Report received. Our team will review it shortly.');
    }
}
