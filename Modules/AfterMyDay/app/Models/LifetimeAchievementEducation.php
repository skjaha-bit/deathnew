<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifetimeAchievementEducation extends Model
{
    protected $table = 'lifetime_achievement_educations';

    protected $fillable = [
        'lifetime_achievement_id',
        'school_or_university',
        'degree',
        'year',
        'sort_order',
    ];

    public function lifetimeAchievement(): BelongsTo
    {
        return $this->belongsTo(LifetimeAchievement::class);
    }
}
