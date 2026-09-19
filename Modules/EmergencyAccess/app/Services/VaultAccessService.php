<?php

namespace Modules\EmergencyAccess\Services;

use App\Models\User;
use Modules\EmergencyAccess\Exceptions\VaultLockedException;
use Modules\EmergencyAccess\Models\EmergencyAccessRequest;

class VaultAccessService
{
    /**
     * Find the approved, not-yet-expired request that lets $actingAdmin
     * view $target's vault. Access is granted to the specific requester,
     * not to "any admin" - approval does not open the vault for everyone.
     */
    public function findActiveGrant(User $target, User $actingAdmin): ?EmergencyAccessRequest
    {
        return EmergencyAccessRequest::query()
            ->where('target_user_id', $target->id)
            ->where('requested_by', $actingAdmin->id)
            ->where('status', EmergencyAccessRequest::STATUS_APPROVED)
            ->where('access_expires_at', '>', now())
            ->latest('decided_at')
            ->first();
    }

    public function assertUnlocked(User $target, User $actingAdmin): EmergencyAccessRequest
    {
        $grant = $this->findActiveGrant($target, $actingAdmin);

        if (! $grant) {
            throw new VaultLockedException(
                "No approved, active emergency access grant for user #{$target->id} held by admin #{$actingAdmin->id}."
            );
        }

        return $grant;
    }

    /**
     * Run $callback only once vault access is confirmed unlocked, logging
     * exactly which section was viewed. Controllers must go through this
     * (or the emergency-access.unlocked middleware) rather than reading
     * encrypted attributes directly - this is the single choke point.
     */
    public function reveal(User $target, User $actingAdmin, string $section, callable $callback): mixed
    {
        $grant = $this->assertUnlocked($target, $actingAdmin);

        activity()
            ->causedBy($actingAdmin)
            ->performedOn($target)
            ->withProperties(['section' => $section, 'emergency_access_request_id' => $grant->id])
            ->log('vault.viewed');

        return $callback();
    }
}
