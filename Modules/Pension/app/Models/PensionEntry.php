<?php

namespace Modules\Pension\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PensionEntry extends Model
{
    protected $fillable = [
        'user_id',
        'pension_fund_id',
        'custom_fund_name',
        'category',
        'from_year',
        'to_year',
        'still_in_payment',
        'estimated_credit',
        'special_rights_text',
    ];

    protected $casts = [
        'still_in_payment' => 'boolean',
        'special_rights_text' => 'encrypted',
        'custom_fund_name' => 'encrypted',
        'from_year' => 'encrypted',
        'to_year' => 'encrypted',
        'estimated_credit' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pensionFund(): BelongsTo
    {
        return $this->belongsTo(PensionFund::class);
    }
}
