<?php

namespace Modules\AfterMyDay\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeathNotice extends Model
{
    protected $fillable = [
        'user_id',
        'preference',
        'obituary_media_preference',
        'symbol',
        'photo_path',
        'introduction',
        'job_title',
        'home_address',
        'date_of_birth',
        'body_text',
        'alignment',
        'layout',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(DeathNoticeRelative::class)->orderBy('sort_order');
    }
}
