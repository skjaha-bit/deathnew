<?php

namespace Modules\CommercialBank\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCommercialBank extends Model
{
    protected $fillable = [
        'user_id',
        'bank_id',
        'swift',
        'loan_type_id',
        'bank_vault',
        'notes_text',
    ];

    protected $casts = [
        'bank_vault' => 'boolean',
        'notes_text' => 'encrypted',
        'swift' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(UserBankAccount::class);
    }

    public function bonds(): HasMany
    {
        return $this->hasMany(UserBond::class);
    }

    public function funds(): HasMany
    {
        return $this->hasMany(UserFund::class);
    }
}
