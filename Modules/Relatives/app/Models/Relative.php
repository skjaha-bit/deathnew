<?php

namespace Modules\Relatives\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relative extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_INVITED = 'invited';

    public const STATUS_ACCEPTED = 'accepted';

    protected $fillable = [
        'user_id',
        'name',
        'kennitala',
        'email',
        'phone',
        'relation',
        'read_access_after_death',
        'estate_agent_notes',
        'personal_affairs_notes',
        'permissions',
        'invite_status',
        'invited_at',
        'accepted_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'read_access_after_death' => 'boolean',
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
        'email' => 'encrypted',
        'phone' => 'encrypted',
        'relation' => 'encrypted',
        'estate_agent_notes' => 'encrypted',
        'personal_affairs_notes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(RelativeEmail::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(RelativePhone::class);
    }
}
