<?php

namespace Modules\Insurance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'user_id',
        'insurance_company_id',
        'other_type_text',
        'contact_preference',
        'contact_message',
    ];

    protected $casts = [
        'contact_message' => 'encrypted',
        'other_type_text' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(InsuranceType::class, 'insurance_policy_types');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InsuranceDocument::class);
    }
}
