<?php

namespace Modules\InheritanceMatters\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InheritanceMatterAgent extends Model
{
    protected $fillable = [
        'inheritance_matter_id',
        'name',
        'phone',
        'email',
        'social_security_number',
        'address',
        'sort_order',
    ];

    public function inheritanceMatter(): BelongsTo
    {
        return $this->belongsTo(InheritanceMatter::class);
    }
}
