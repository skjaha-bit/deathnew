<?php

namespace Modules\Relatives\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeathReport extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'reported_by_relative_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'relationship',
        'notes',
        'status',
        'reported_at',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Relative::class, 'reported_by_relative_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
