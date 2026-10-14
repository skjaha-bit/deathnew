<?php

namespace Modules\CommercialBank\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBankAccount extends Model
{
    protected $fillable = [
        'user_commercial_bank_id',
        'account_type_id',
        'account_number',
        'swift_iban',
        'is_joint',
        'joint_holder_name',
    ];

    protected $casts = [
        'is_joint' => 'boolean',
        'account_number' => 'encrypted',
        'swift_iban' => 'encrypted',
        'joint_holder_name' => 'encrypted',
    ];

    public function userCommercialBank(): BelongsTo
    {
        return $this->belongsTo(UserCommercialBank::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }
}
