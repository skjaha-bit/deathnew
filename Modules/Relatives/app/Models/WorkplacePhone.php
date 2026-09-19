<?php

namespace Modules\Relatives\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkplacePhone extends Model
{
    protected $fillable = ['workplace_id', 'phone_code', 'phone_number'];

    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class);
    }
}
