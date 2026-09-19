<?php

namespace Modules\InheritanceMatters\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InheritanceMatter extends Model
{
    protected $fillable = [
        'user_id',
        'will_exists',
        'will_location',
        'will_file_path',
        'notes',
    ];

    protected $casts = [
        'will_exists' => 'boolean',
        'will_location' => 'encrypted',
        'notes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(InheritanceMatterAgent::class)->orderBy('sort_order');
    }
}
