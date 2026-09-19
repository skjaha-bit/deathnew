<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoryDocument extends Model
{
    protected $fillable = [
        'memory_id',
        'file_path',
        'original_name',
        'file_size',
        'sort_order',
    ];

    public function memory(): BelongsTo
    {
        return $this->belongsTo(Memory::class);
    }
}
