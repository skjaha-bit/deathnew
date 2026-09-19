<?php

namespace Modules\AfterMyDay\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LifetimeAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'resume_path',
        'personal_information',
        'another_thing',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'place_of_birth' => 'encrypted',
        'personal_information' => 'encrypted',
        'another_thing' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(LifetimeAchievementEducation::class)->orderBy('sort_order');
    }

    public function spouses(): HasMany
    {
        return $this->hasMany(LifetimeAchievementSpouse::class)->orderBy('sort_order');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LifetimeAchievementChild::class)->orderBy('sort_order');
    }

    public function grandchildren(): HasMany
    {
        return $this->hasMany(LifetimeAchievementGrandchild::class)->orderBy('sort_order');
    }
}
