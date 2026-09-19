<?php

namespace Modules\EmergencyAccess\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmergencyAccessRequest extends Model
{
    use LogsActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DENIED = 'denied';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'target_user_id',
        'requested_by',
        'approved_by',
        'status',
        'reason',
        'decision_reason',
        'requested_at',
        'decided_at',
        'access_expires_at',
        'revoked_at',
        'revoked_by',
        'request_ip',
        'request_user_agent',
        'decision_ip',
        'decision_user_agent',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'decided_at' => 'datetime',
        'access_expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $request) {
            if ($request->approved_by !== null && $request->approved_by === $request->requested_by) {
                throw new \RuntimeException('An admin cannot approve their own emergency access request.');
            }
        });
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->access_expires_at !== null
            && $this->access_expires_at->isFuture();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'target_user_id', 'requested_by', 'approved_by'])
            ->logOnlyDirty();
    }
}
