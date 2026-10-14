<?php

namespace Modules\Relatives\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelativeEmail extends Model
{
    protected $fillable = ['relative_id', 'email'];

    protected $casts = [
        'email' => 'encrypted',
    ];

    public function relative(): BelongsTo
    {
        return $this->belongsTo(Relative::class);
    }
}
