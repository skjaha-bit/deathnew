<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifetimeAchievementChild extends Model
{
    protected $fillable = [
        'lifetime_achievement_id',
        'name',
        'date_of_birth',
        'spouse_name',
        'sort_order',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'name' => 'encrypted',
        'spouse_name' => 'encrypted',
    ];

    public function lifetimeAchievement(): BelongsTo
    {
        return $this->belongsTo(LifetimeAchievement::class);
    }
}
