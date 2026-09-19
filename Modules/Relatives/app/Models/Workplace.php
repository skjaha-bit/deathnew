<?php

namespace Modules\Relatives\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workplace extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'contact_title',
        'department',
        'notify_after_death',
        'message_to_contact',
        'additional_note',
    ];

    protected $casts = [
        'notify_after_death' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(WorkplaceEmail::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(WorkplacePhone::class);
    }
}
