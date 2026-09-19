<?php

namespace Modules\Relatives\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkplaceEmail extends Model
{
    protected $fillable = ['workplace_id', 'email'];

    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class);
    }
}
