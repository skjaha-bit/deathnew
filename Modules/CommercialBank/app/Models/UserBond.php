<?php

namespace Modules\CommercialBank\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBond extends Model
{
    protected $fillable = [
        'user_commercial_bank_id',
        'bond_type_id',
    ];

    public function userCommercialBank(): BelongsTo
    {
        return $this->belongsTo(UserCommercialBank::class);
    }

    public function bondType(): BelongsTo
    {
        return $this->belongsTo(BondType::class);
    }
}
