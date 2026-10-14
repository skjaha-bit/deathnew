<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeathNoticeRelative extends Model
{
    protected $fillable = [
        'death_notice_id',
        'name',
        'column_position',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'encrypted',
    ];

    public function deathNotice(): BelongsTo
    {
        return $this->belongsTo(DeathNotice::class);
    }
}
