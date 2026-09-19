<?php

namespace Modules\TradeUnion\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTradeUnion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_union_id',
        'union_name',
        'from_month',
        'from_year',
        'to_month',
        'to_year',
        'still_in_payment',
        'department_contact',
        'rights_text',
        'notes_text',
    ];

    protected $casts = [
        'still_in_payment' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradeUnion(): BelongsTo
    {
        return $this->belongsTo(TradeUnion::class);
    }
}
