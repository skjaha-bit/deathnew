<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifetimeAchievementSpouse extends Model
{
    protected $fillable = [
        'lifetime_achievement_id',
        'name',
        'sort_order',
    ];

    public function lifetimeAchievement(): BelongsTo
    {
        return $this->belongsTo(LifetimeAchievement::class);
    }
}
