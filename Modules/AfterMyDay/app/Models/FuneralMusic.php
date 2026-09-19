<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuneralMusic extends Model
{
    protected $table = 'funeral_music';

    protected $fillable = [
        'funeral_id',
        'description',
        'sort_order',
    ];

    public function funeral(): BelongsTo
    {
        return $this->belongsTo(Funeral::class);
    }
}
