<?php

namespace Modules\EmergencyAccess\Console\Commands;

use Illuminate\Console\Command;
use Modules\EmergencyAccess\Models\EmergencyAccessRequest;

class ExpireEmergencyAccessRequests extends Command
{
    protected $signature = 'emergency-access:expire';

    protected $description = 'Flip approved emergency access requests past their expiry into "expired" - this is the vault re-lock.';

    public function handle(): int
    {
        $expired = EmergencyAccessRequest::query()
            ->where('status', EmergencyAccessRequest::STATUS_APPROVED)
            ->where('access_expires_at', '<=', now())
            ->get();

        foreach ($expired as $request) {
            $request->update(['status' => EmergencyAccessRequest::STATUS_EXPIRED]);

            activity()
                ->performedOn($request)
                ->withProperties(['target_user_id' => $request->target_user_id])
                ->log('emergency-access.expired');
        }

        $this->info("Expired {$expired->count()} emergency access request(s).");

        return self::SUCCESS;
    }
}
