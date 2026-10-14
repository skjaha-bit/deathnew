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

    protected $casts = [
        'social_security_number' => 'encrypted',
        'address' => 'encrypted',
        'phone' => 'encrypted',
        'name' => 'encrypted',
        'email' => 'encrypted',
    ];

    public function inheritanceMatter(): BelongsTo
    {
        return $this->belongsTo(InheritanceMatter::class);
    }
}
