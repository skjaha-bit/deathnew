<?php

namespace Modules\CommercialBank\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFund extends Model
{
    protected $fillable = [
        'user_commercial_bank_id',
        'fund_type_id',
    ];

    public function userCommercialBank(): BelongsTo
    {
        return $this->belongsTo(UserCommercialBank::class);
    }

    public function fundType(): BelongsTo
    {
        return $this->belongsTo(FundType::class);
    }
}
