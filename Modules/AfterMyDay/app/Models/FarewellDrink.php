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

    protected $casts = [
        'location' => 'encrypted',
        'guests' => 'encrypted',
        'catering' => 'encrypted',
        'arrangement' => 'encrypted',
        'comments' => 'encrypted',
        'other_wishes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
