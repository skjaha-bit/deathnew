<?php

namespace Modules\EmergencyAccess\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\EmergencyAccess\Exceptions\VaultLockedException;
use Modules\EmergencyAccess\Services\VaultAccessService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware `emergency-access.unlocked:{routeParam}` - resolves the
 * target user from the given route parameter (an EmergencyAccessRequest id
 * or a user id, see below), asserts the current admin holds an active
 * grant for that target, and shares the resolved grant into the request so
 * controllers never have to re-look it up (or skip the check by accident).
 */
class EnsureVaultUnlocked
{
    public function __construct(protected VaultAccessService $vaultAccessService) {}

    public function handle(Request $request, Closure $next, string $routeParam): Response
    {
        $actingAdmin = Auth::guard('web')->user();
        $routeValue = $request->route($routeParam);

        $targetUser = $routeValue instanceof User
            ? $routeValue
            : User::query()->findOrFail($routeValue);

        try {
            $grant = $this->vaultAccessService->assertUnlocked($targetUser, $actingAdmin);
        } catch (VaultLockedException $e) {
            abort(403, 'This vault is not currently unlocked for you. Request emergency access first.');
        }

        $request->attributes->set('emergencyAccessGrant', $grant);
        $request->attributes->set('vaultTargetUser', $targetUser);

        return $next($request);
    }
}
