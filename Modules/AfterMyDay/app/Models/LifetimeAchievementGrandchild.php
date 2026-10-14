<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifetimeAchievementGrandchild extends Model
{
    protected $fillable = [
        'lifetime_achievement_id',
        'name',
        'date_of_birth',
        'parent_name',
        'sort_order',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'name' => 'encrypted',
        'parent_name' => 'encrypted',
    ];

    public function lifetimeAchievement(): BelongsTo
    {
        return $this->belongsTo(LifetimeAchievement::class);
    }
}
