<?php

namespace Modules\Property\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CommercialBank\Models\LoanType;
use Modules\Insurance\Models\InsuranceCompany;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'property_type_id',
        'name',
        'location',
        'asset_registration_number',
        'ownership',
        'co_owner_name',
        'co_owner_contact',
        'deeds_location',
        'has_will',
        'lender',
        'loan_number',
        'loan_outstanding_balance',
        'loan_type_id',
        'insurance_company_id',
        'policy_number',
        'has_loan_protection',
        'notes_text',
    ];

    protected $casts = [
        'has_will' => 'boolean',
        'has_loan_protection' => 'boolean',
        'loan_outstanding_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }
}
