<?php

namespace Modules\Relatives\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelativePhone extends Model
{
    protected $fillable = ['relative_id', 'phone_code', 'phone_number'];

    protected $casts = [
        'phone_code' => 'encrypted',
        'phone_number' => 'encrypted',
    ];

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Relative::class);
    }
}
