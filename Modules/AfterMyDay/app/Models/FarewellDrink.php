<?php

namespace Modules\AfterMyDay\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarewellDrink extends Model
{
    protected $fillable = [
        'user_id',
        'preference',
        'location',
        'guests',
        'catering',
        'arrangement',
        'comments',
        'other_wishes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
